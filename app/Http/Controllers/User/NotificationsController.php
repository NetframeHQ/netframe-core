<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\NotificationsRepository;
use App\Notif;
use App\Profile;
use App\Project;
use App\House;
use App\Community;
use App\User;
use App\Friends;
use App\Netframe;
use App\Events\NewAction;
use App\Events\AddProfile;

class NotificationsController extends BaseController
{

    /**
     *
     * @param NotificationsRepository $notificationsRepository
     */
    public function __construct()
    {
        $this->middleware('checkAuth');
        parent::__construct();
    }

    /**
     * Performs the search and display the results list.
     *
     * @return \Illuminate\View\View
     */
    public function notifications()
    {
        $notificationsRepository = new NotificationsRepository();

        if (request()->has('limit')) {
            $start = request()->get('limit') * 20;
            $limit = [$start, 20];
            $view = 'notifications.results-details';
        } else {
            $limit = null;
            $view = 'notifications.results';
        }

        Notif::markReadForUser();

        $data['results'] = $notificationsRepository->findWaiting($limit);
        $data['profile'] = auth()->guard('web')->user();

        return view($view, $data);
    }

    public function lasts()
    {
        $notificationsRepository = new NotificationsRepository();
        $countUnread = Notif::where(array(
            'author_id' => auth()->guard('web')->user()->id,
            'read' => 0
            ))->count();

        if ($countUnread == 0) {
            $countUnread = 5;
        } elseif ($countUnread > 5) {
            $countUnread = 5;
        }

        $limit = [0, $countUnread];
        $data['notifications'] = $notificationsRepository->findWaiting($limit);

        return response()->json(['view' => view('notifications.popover', $data)->render()]);
    }
}
