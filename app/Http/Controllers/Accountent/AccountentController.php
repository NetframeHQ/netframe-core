<?php
namespace App\Http\Controllers\Accountent;

use App\Http\Controllers\Controller;
use Intervention\Validation\Validator;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use App\Instance;

class AccountentController extends Controller
{
    public function index()
    {
        $data = [];
        $instance = \Auth::guard('accountent')->user()->instances()->first();
        $data['bills'] = $instance->billings;
        return view('accountent.home', $data);
    }

    public function billing($number = null)
    {
        if ($number == null) {
            return redirect()->route('instance.subscription');
        }
        $bill = \App\Billing::where('number', '=', $number)->firstOrFail();
        if ($bill == null) {
            return redirect()->route('instance.subscription');
        }
        $instance = \Auth::guard('accountent')->user()->instances()->first();
        $infos = json_decode($instance->getParameter('billing_infos'), true);
        if ($infos == null || $infos=="") {
            session()->flash('error_flash_message', \Lang::get('form.noInfos'));
            return redirect()->route('accountent.infos');
        }
        $data = array();
        $data['bill'] = $bill;
        $data['infos'] = $infos;
        $totalHt = 0;
        $totalTtc = 0;
        foreach ($bill->billingLines as $bl) {
            $totalHt = $bl->nb_users*$bl->amountUnit;
            $totalTtc = $totalHt+($bl->tva/100)*$totalHt;
        }
        $data['ht'] = $totalHt;
        $data['ttc'] = $totalTtc;
        return view('accountent.billing', $data);
    }

    public function pdf($number = null, $instanceId = null)
    {
        if ($number == null) {
            return redirect()->route('instance.subscription');
        }
        $bill = \App\Billing::where('number', '=', $number)->firstOrFail();
        if ($bill == null) {
            return redirect()->route('instance.subscription');
        }
        $instance = isset($instanceId)
            ? Instance::find($instanceId)->first()
            : \Auth::guard('accountent')->user()->instances()->first();
        $infos = json_decode($instance->getParameter('billing_infos'), true);
        if ($infos == null || $infos=="") {
            session()->flash('error_flash_message', \Lang::get('form.noInfos'));
            return redirect()->route('accountent.infos');
        }
        $data = array();
        $data['bill'] = $bill;
        $data['infos'] = $infos;
        $totalHt = 0;
        $totalTtc = 0;
        foreach ($bill->billingLines as $bl) {
            $totalHt = $bl->nb_users*$bl->amountUnit;
            $totalTtc = $totalHt+($bl->tva/100)*$totalHt;
        }
        $data['ht'] = $totalHt;
        $data['ttc'] = $totalTtc;
        $data['pdf'] = true;
        $dompdf = \App::make('dompdf.wrapper');
        $dompdf->loadHTML(view('accountent.billing', $data));
        return $dompdf->stream();
    }

    public function paymentinfos($type = "card")
    {

        $instance = \Auth::guard('accountent')->user()->instances()->first();
        $data = [];
        $billing_payment = $instance->getParameter("billing_payment");
        $dt = ['type'=>$type];
        $infos = $instance->billing_infos;
        $card = '';
        if (isset($infos)) {
            $number = json_decode($infos->value)->number;
            $card = 'XXXXXXXXXXXX'.substr($number, 12);
        }
        $dt['card'] = $card;
        if (request()->isMethod('POST')) {
            if (!request()->has('delete')) {
                $rule = array();
                $inputPost = array();
                if (request()->get('type')=="card") {
                    $rule = [
                        \Lang::get('form.pay.cardName') => 'required',
                        \Lang::get('form.pay.cardNumber') => 'required|creditcard',
                        \Lang::get('form.pay.month') => 'required|max:2|min:2',
                        \Lang::get('form.pay.year') => 'required|max:4|min:4',
                        \Lang::get('form.pay.crypto') => 'required|max:3|min:3',
                    ];
                    $inputPost = array(
                        \Lang::get('form.pay.cardName') => trim(request()->get('card-name')),
                        \Lang::get('form.pay.cardNumber') => trim(request()->get('card-number')),
                        \Lang::get('form.pay.month') => trim(request()->get('card-expiry-month')),
                        \Lang::get('form.pay.year') => trim(request()->get('card-expiry-year')),
                        \Lang::get('form.pay.crypto') => trim(request()->get('card-crypto')),
                    );
                } else {
                    $rule = [
                        \Lang::get('form.pay.iban') => 'required|iban',
                        \Lang::get('form.pay.bic') => 'bic',
                        \Lang::get('form.pay.ibanName') => 'required',
                    ];
                    $inputPost = array(
                        \Lang::get('form.pay.ibanName') => trim(request()->get('iban-name')),
                        \Lang::get('form.pay.iban') => trim(request()->get('iban')),
                        \Lang::get('form.pay.bic') => trim(request()->get('bic')),
                    );
                }
                $validation = validator($inputPost, $rule);
                if (request()->get('type')=="card") {
                    $validation->after(function ($v) {
                        $month = request()->get('card-expiry-month');
                        $year = request()->get('card-expiry-year');
                        if ($year<date('Y') || ($year == date('Y') && $month < date('m'))) {
                            $v->errors()->add('year', \Lang::get('form.pay.error_date'));
                        }
                    });
                }
                if ($validation->fails()) {
                    return redirect()->route('accountent.paymentinfos', ['type' => request()->get('type')])
                                     ->withErrors($validation)
                                     ->withInput();
                }

                //delete if exists the previous card
                \App\BillingInfos::where(
                    'instances_id',
                    '=',
                    \Auth::guard('accountent')->user()->instances()->first()->id
                )->delete();

                $billingsinfos = new \App\BillingInfos;
                $billingsinfos->instance()->associate($instance);
                $billingsinfos->type = request()->get('type');
                $data = array();
                if (request()->get('type')=="card") {
                    $data['name'] = request()->get('card-name');
                    $data['number'] = request()->get('card-number');
                    $data['expiry-month'] = request()->get('card-expiry-month');
                    $data['expiry-year'] = request()->get('card-expiry-year');
                    $data['crypto'] = request()->get('card-crypto');
                    $billingsinfos->value = json_encode($data);
                } else {
                    $data['name'] = request()->get('iban-name');
                    $data['iban'] = request()->get('iban');
                    $data['bic'] = request()->get('bic');
                    $billingsinfos->value = json_encode($data);

                    // Debit

                    // \Stripe\Stripe::setApiKey(env("STRIPE_SECRET_KEY"));

                    // $source = \Stripe\Source::create(array(
                    //   "type" => "sepa_debit",
                    //   "sepa_debit" => array("iban" => $data['iban']),
                    //   "currency" => "eur",
                    //   "owner" => array(
                    //     "name" => $data['name'],
                    //   ),
                    // ));
                    // if(isset($source['id'])){
                    //     $data['source_id'] = $source['id'];
                    //     $customer = \Stripe\Customer::create(array(
                    //       "source" => $data['source_id'],
                    //     ));
                    //     $charge = \Stripe\Charge::create(array(
                    //       "amount" => 1099,
                    //       "currency" => "eur",
                    //       "customer" => $customer->id,
                    //       "source" => $data['source_id'],
                    //     ));
                    //     dd($charge);
                    // }
                }
                $billingsinfos->save();

                session()->flash('flash_message', \Lang::get('form.successRecord'));
            } else {
                $instance->billing_infos()->delete();
            }
            return redirect()->route('accountent.paymentinfos', ['type' => request()->get('type')]);
        }

        return view('accountent.paymentinfos', $dt);
    }

    public function infos()
    {
        $instance = \Auth::guard('accountent')->user()->instances()->first();
        $infos = json_decode($instance->getParameter('billing_infos'), true);
        if ($infos == null || $infos == "") {
            $infos = array_fill_keys(['designation','city','codepostal','address'], '');
        }

        if (request()->isMethod('POST')) {
            $rule = [
                "designation" => "required",
                "address" => "required",
                "city" => "required",
                "codepostal" => "required",
            ];
            $inputPost = array(
                "designation" => trim(request()->get('designation')),
                "address" => trim(request()->get('address')),
                "city" => trim(request()->get('city')),
                "codepostal" => trim(request()->get('codepostal')),
            );
            $validation = validator($inputPost, $rule);
            if ($validation->fails()) {
                return redirect()->route('accountent.infos')
                                 ->withErrors($validation)
                                 ->withInput();
            }
            $data = array(
                "designation" => request()->get('designation'),
                "address" => request()->get('address'),
                "city" => request()->get('city'),
                "codepostal" => request()->get('codepostal'),
            );
            $instance->setParameter('billing_infos', json_encode($data));
            session()->flash('flash_message', \Lang::get('form.successRecord'));
            return redirect()->route('accountent.infos');
        }
        return view('accountent.infos', $infos);
    }

    public function pay($number = null)
    {

        if ($number == null) {
            return redirect()->route('accountent.home');
        }

        /*$instance = Instance::find(session('instanceId'));*/

        $instance = \Auth::guard('accountent')->user()->instances()->first();
        $bill = $instance->billings()->where('number', '=', $number)->first();
        if ($bill==null || $bill->paid) {
            return redirect()->route('accountent.home');
        }
        $paymentinfos = $instance->billing_infos;
        if ($paymentinfos == null) {
            session()->flash('error_flash_message', \Lang::get('form.msgErrorPaymentMode'));
            return redirect()->route('accountent.paymentinfos');
        }
        /*if($instance->billing_infos->type=="iban"){
            session()->flash('error_flash_message','Le paiement se fait par prélèvement automatique!');
            return redirect()->route('accountent.home');
        }*/
        $data = array();
        $infos = json_decode($instance->billing_infos->value, true);
        $data['bill'] = $bill;
        $data['card_name'] = $infos['name'];
        $data['type'] = $instance->billing_infos->type;
        $data['pay'] = true;
        if ($infos['expiry-year'] < date('Y')
            || ($infos['expiry-year'] == date('Y') && $infos['expiry-month'] < date('m'))) {
            session()->flash('error_message', \Lang::get('form.pay.expired_card'));
            // dd(session());
            $data['pay'] = false;
        }

        if (request()->isMethod('POST')) {
            try {
                $stripe = Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
                $token = \Stripe\Token::create([
                    'card' => [
                        'number'    => $infos['number'],
                        'exp_month' => $infos['expiry-month'],
                        'exp_year'  => $infos['expiry-year'],
                        'cvc'       => $infos['crypto'],
                    ],
                ]);
                if (isset($token['id'])) {
                     $customer = Customer::create(array(
                        'source'  => $token['id'],
                     ));
                    $charge = Charge::create(array(
                        'customer' => $customer->id,
                        'amount'   => $bill->total*100,
                        'currency' => 'eur'
                    ));
                    $bill->paid = true;
                    $bill->save();
                    session()->flash('success_message', \Lang::get('form.successPayment'));
                } else {
                    $bill->last_attempt = date("Y-m-d H:i:s");
                    $bill->save();
                    session()->flash('error_message', \Lang::get('form.msgErrorPayment'));
                    return redirect()->route('accountent.home');
                }
            } catch (\Stripe\Error\Card $e) {
                $error = $e->getJsonBody()['error'];
                session()->flash(
                    'error_message',
                    \Lang::get('form.msgErrorPayment') . ' ' . \Lang::get('accountent.stripe_errors.' . $error['code'])
                );
                return redirect()->route('accountent.pay', ['number'=>$number]);
            }
            return redirect()->route('accountent.home');
        }

        return view('accountent.pay', $data);
    }


    // public function generateBills(){
    //     $count = \App\BillingCount::first();
    //     $id = $count->value+1;
    //     foreach (Instance::cursor() as $instance){
    //         $bill = new \App\Billing;
    //         $bill->instance()->attach($instance);
    //         $bill->number = (new \Datetime())->format('Y').str_pad($id, 6, "0", STR_PAD_LEFT);
    //         $bill->paid = false;
    //         $bill->total = 0;
    //         $bill->attach($instance);
    //         $billingLine = new \App\BillingLine();
    //         $billingLine->attach($bill);
    //         $billingLine->nb_users = count($instance->users()->getResults());
    //         $billingLine->amountUnit = config('billing.amount');
    //         $ht = $billingLine->nb_users*$billingLine->amountUnit;
    //         $bill->total+= $h+($billingLine->tva*$ht/100);
    //         $bill->save();
    //         $billingLine->save();
    //         $id++;
    //     }
    //     $count->value = $id;
    //     $count->save();
    // }
}
