<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Instance;
use App\Billing;

class PaymentSuccess extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Instance $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $bill = Billing::where('instances_id', '=', $this->instance->id)->orderBy('created_at', 'desc')->first();
        // $pdf = \App::call(
        //     'App\Http\Controllers\Accountent\AccountentController@pdf',
        //     ['number' => $bill->number, 'instanceId' => $this->instance->id]
        // );

        // Generate bill pdf
        $infos = json_decode($this->instance->getParameter('billing_infos'), true);
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
        $pdf = $dompdf->loadHTML(view('accountent.billing', $data));

        return $this->view('emails.cron.paymentsuccess')
            ->subject(trans('email.cron.paymentsuccess.subject'))
            ->attachData($pdf->output(), 'bill.pdf');
    }
}
