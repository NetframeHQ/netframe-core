<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class WelcomeAdmin extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $admin, $mailType)
    {
        $this->admin = $admin;
        $this->mailType = $mailType;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data['admin'] = $this->admin;

        switch ($this->mailType) {
            case 'welcome':
                $data['welcomeStep'] = 'welcome';
                return $this->view('emails.cron.welcome-admin', $data)
                    ->subject(trans('email.cron.welcomeAdmin.subject'));
                break;

            case 'userManual':
                $data['welcomeStep'] = 'userManual';
                return $this->view('emails.cron.welcome-admin', $data)
                    ->subject(trans('email.cron.welcomeAdmin.subject'))
                    ->attach(public_path().'/userManual/Netframe-'.$this->admin->lang.'.pdf', [
                        'as' => 'netframe.pdf',
                        'mime' => 'application/pdf',
                    ]);
                break;
        }
    }
}
