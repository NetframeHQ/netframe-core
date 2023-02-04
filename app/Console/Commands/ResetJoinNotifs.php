<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notif;

class ResetJoinNotifs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:notifJoin';

    private $notifTypesReset = [
        'joinCommunity',
        'joinChannel',
        'joinHouse',
        'joinProject',
        'askFriend',
        'inviteChannel',
        'inviteCommunity',
        'inviteHouse',
        'inviteProject',
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset notifs join to put them in top of the list';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $notifs = Notif::whereIn('type', $this->notifTypesReset)->get();
        foreach ($notifs as $notif) {
            $notif->read = 0;
            $notif->created_at = Carbon::now();
            $notif->save();
        }
    }
}
