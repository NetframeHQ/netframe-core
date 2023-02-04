<?php

namespace App\Console\Commands;

use Elasticsearch\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use App\UserAuthLogger;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use App\Mail\EndTrial;
use App\Mail\PaymentError;
use App\Mail\PaymentSuccess;
use App\Instance;

class GenerateBillsCommand extends Command
{
    protected $name = "generate:bills";
    protected $description = "Generates bills";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Generating bills...');
        $count = \App\BillingCount::first();
        if ($count == null) {
            $count = new \App\BillingCount();
            $count->value = 0;
        }
        $id = $count->value;
        $next=[];
        $instances = Instance::select('instances.*')
            ->leftJoin('instance_parameters', 'instance_parameters.instances_id', '=', 'instances.id')
            ->where('instance_parameters.parameter_name', '=', 'billing_offer')
            ->where('instance_parameters.parameter_value', '=', 'normal')
            ->where('instances.created_at', '>=', config('billing.oldInstancesDate')) // exception for older instances
            ->where('active', '=', 1)
            ->cursor();

        foreach ($instances as $instance) {
            // Get the last bill
            $billing = \App\Billing::where('instances_id', '=', $instance->id)->orderBy('id', 'desc')->first();

            //Date of the last bill if bill exists || instance begin
            $lastBillDate = date('Y-m-d', strtotime(empty($billing) ? $instance->begin_date : $billing->created_at));

            //Date of the next bill
            $nextBillDate = date('Y-m-d', strtotime("+1 month -1 day", strtotime($lastBillDate)));
            $today = date('Y-m-d');

            while (date('Y-m-d', strtotime("+1 month", strtotime($nextBillDate))) < $today) {
                $nextBillDate = date('Y-m-d', strtotime("+1 month", strtotime($nextBillDate)));
            }

            // $next[] = $nextBillDate;
            if ($today>$nextBillDate) {
                $nb_users = \DB::table('user_auth_logger')
                    ->where('instances_id', '=', $instance->id)
                    ->whereBetween('created_at', [$lastBillDate, $nextBillDate])
                    ->count(\DB::raw('distinct(users_id)'));

                $bill = new \App\Billing;
                $bill->number = (new \Datetime())->format('Y').str_pad($id, 6, "0", STR_PAD_LEFT);
                $bill->paid = false;
                $bill->instance()->associate($instance);
                $billingLine = new \App\BillingLine();
                $billingLine->nb_users = $nb_users;
                $billingLine->amountUnit = config('billing.amount');
                $billingLine->tva = config('billing.tva');
                $ht = $billingLine->nb_users*$billingLine->amountUnit;
                $bill->total = $ht+($billingLine->tva*$ht/100);
                $billingLine->instance()->associate($instance);

                if ($bill->total > 0) {
                    $bill->save();
                    $billingLine->billing()->associate($bill);
                    $billingLine->save();
                    $id++;
                    echo '.';
                }
            }

            // Paiement des factures
            $bill = \App\Billing::where('instances_id', '=', $instance->id)
                ->where('paid', '=', false)
                ->orderBy('id', 'desc')
                ->first();
            if (isset($bill) && $bill->total > 0) {
                $bi = \App\BillingInfos::where('instances_id', '=', $instance->id)
                    ->where('type', '=', 'stripe_customer')
                    ->first();
                if (isset($bi)) {
                    $customer = json_decode($bi->value, true);
                    $admins = $instance->administrators;
                    try {
                        $stripe = Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

                        $charge = Charge::create(array(
                            'customer' => $customer['customerID'],
                            'amount'   => $bill->total*100,
                            'currency' => 'eur'
                        ));
                        $bill->paid = true;
                        $bill->save();

                        foreach ($admins as $admin) {
                            // Attach a pdf to the email
                            if (\App::environment(['production'])
                                || (\App::environment(['local']) && $admin->email == 'julien@illisite.fr')) {
                                Mail::to($admin->email)->send(new PaymentSuccess($instance));
                            }
                        }
                        /*
                        }else{
                            //erreur de carte
                            $bill->last_attempt = date("Y-m-d H:i:s");
                            $bill->save();

                            foreach ($admins as $admin) {
                                if(\App::environment(['production'])
                                    || (\App::environment(['local']) && $admin->email == 'daouda@illisite.fr')) {
                                    Mail::to($admin->email)->send(new PaymentError($instance));
                                }
                            }
                        }
                        */
                    } catch (\Stripe\Error\InvalidRequest $e) {
                        $error = $e;
                    } catch (\Stripe\Error\Card $e) {
                        $error = $e;
                    }
                    if (isset($error)) {
                        $bill->last_attempt = date("Y-m-d H:i:s");
                        $bill->save();

                        foreach ($admins as $admin) {
                            if (\App::environment(['production'])
                                || (\App::environment(['local']) && $admin->email == 'julien@illisite.fr')) {
                                Mail::to($admin->email)->send(new PaymentError($instance));
                            }
                        }
                    }
                }
            }

            if (in_array($instance->remainingDays(), [10,5,3,1])) {
                $infos = \DB::table('billing_infos')->where('instances_id', $instance->id)->first();
                if (isset($infos)) {
                    $admins = $instance->administrators;
                    foreach ($admins as $admin) {
                        if (\App::environment(['production'])
                            || (\App::environment(['local']) && $admin->email == 'julien@illisite.fr')) {
                            \Mail::to($admin->email)->send(new EndTrial($instance));
                        }
                    }
                }
            }
        }

        $count->value = $id;
        $count->save();
        $this->info("\nDone!");

        // trial
        // Mail::to($boarding->email)->send(new BoardingDemand($boarding));
    }
}
