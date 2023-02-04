<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Instance;

class AssistanceNeeded extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Instance $instance = null, User $user)
    {
        $this->user = $user;
        $this->instance = $instance;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.admin.first-tour', ['instance' => $this->instance, 'user' => $this->user])
            ->subject(trans('admin.email.firstTour.subject'));
    }
}
