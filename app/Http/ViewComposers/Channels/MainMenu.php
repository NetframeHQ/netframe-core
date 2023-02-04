<?php

namespace App\Http\ViewComposers\Channels;

use Illuminate\View\View;

class MainMenu
{
    /**
     * new message with contact list
     */

    public function __construct()
    {
    }

    public function compose(View $view)
    {
        $getData = $view->getData();
        $channels = auth()
            ->guard('web')
            ->user()
            ->channels()
            ->where('status', '=', 1)
            ->where('active', '=', 1)
            ->orderBy('updated_at', 'desc')
            ->get();
        $personnalChannels = auth()
            ->guard('web')
            ->user()
            ->directMessagesChansActive()
            ->where('channels.active', '=', 1)
            ->orderBy('updated_at', 'desc')
            ->get();
        return $view->with('channels', $channels)
                    ->with('personnalChannels', $personnalChannels);
    }
}
