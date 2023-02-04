<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Instance;

class EndTrial extends Mailable
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
        $data['instance'] = $this->instance;
        $data['remainingDays'] = $this->instance->remainingDays();
        $data['instance_subscription'] = $this->instance->getUrl().'/instance/subscription';

        return $this->view('emails.cron.endtrial', $data)
            ->subject(trans('email.cron.endtrial.subject'));
    }
}
