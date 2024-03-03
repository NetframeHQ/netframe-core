<?php
namespace App\Http\Controllers\Instance;

use App\Http\Controllers\BaseController;
use App\Instance;
use Carbon\Carbon;
use App\NewsFeed;
use App\Comment;
use App\Like;
use App\Share;
use App\User;
use App\View;
use App\Helpers\StatsHelper;

class StatsController extends BaseController
{
    private $imagine;

    public function __construct()
    {
        $this->middleware('instanceManager');
    }

    /*
     * $period = nb days to take for stats display
     */
    public function home($period = 7)
    {
        $instance = Instance::find(session('instanceId'));

        // manage period
        $endInterval = Carbon::now()
            ->format('Y-m-d');
        $startInterval = Carbon::now()
            ->subDays($period)
            ->format('Y-m-d');
        $startPreviousInterval = Carbon::now()
            ->subDays($period*2)
            ->format('Y-m-d');

        $instanceNewUsers = $instance->stats()
            ->where('day', '>=', $startInterval)
            ->where('day', '<=', $endInterval)
            ->where('stat_type', '=', 'users')
            ->sum('counter');
        $instanceConnexions = \DB::table('user_auth_logger')
            ->where('instances_id', '=', session('instanceId'))
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->count();
        $instancePreviewNewUsers = $instance->stats()
            ->where('day', '>=', $startPreviousInterval)
            ->where('day', '<=', $startInterval)
            ->where('stat_type', '=', 'users')
            ->sum('counter');
        $instancePreviewConnexions = \DB::table('user_auth_logger')
            ->where('instances_id', '=', session('instanceId'))
            ->where('created_at', '>=', $startPreviousInterval)
            ->where('created_at', '<=', $startInterval)
            ->count();
        $users = $instance->activeUsers()->count();
        $groups = $instance->communities()->where('active', '=', 1)->count();
        $news = $instance->newsfeeds()
            ->where('post_type', '=', 'App\\News')
            ->where('author_type', '!=', 'App\\Channel')
            ->count();
        $channelsPosts = $instance->newsfeeds()
            ->where('post_type', '=', 'App\\News')
            ->where('author_type', '=', 'App\\Channel')
            ->count();
        $events = $instance->events()->count();
        $offers = $instance->offers()->count();
        $medias = $instance->medias()->count();
        $channels = $instance->channels()->count();
        $likes = $instance->likes()->count();
        $comments = $instance->comments()->count();

        $newGroups = $instance->communities()
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->count();
        $newNews = $instance->newsfeeds()
            ->where('post_type', '=', 'App\\News')
            ->where('author_type', '!=', 'App\\Channel')
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->count();
        $newChannelsPosts = $instance->newsfeeds()
            ->where('post_type', '=', 'App\\News')
            ->where('author_type', '=', 'App\\Channel')
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->count();
        $newOffers = $instance->offers()
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->count();
        $newEvents = $instance->events()
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->count();
        $newMedias = $instance->medias()
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->count();
        $newChannels = $instance->events()
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->count();
        $newLikes = $instance->likes()
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->count();
        $newComments = $instance->comments()
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->count();

        $newPreviewGroups = $instance->communities()
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->count();
        $newPreviewNews = $instance->newsfeeds()
            ->where('post_type', '=', 'App\\News')
            ->where('author_type', '!=', 'App\\Channel')
            ->where('created_at', '>=', $startPreviousInterval)
            ->where('created_at', '<=', $startInterval)
            ->count();
        $newPreviewChannelsPosts = $instance->newsfeeds()
            ->where('post_type', '=', 'App\\News')
            ->where('author_type', '=', 'App\\Channel')
            ->where('created_at', '>=', $startPreviousInterval)
            ->where('created_at', '<=', $startInterval)
            ->count();
        $newPreviewOffers = $instance->offers()
            ->where('created_at', '>=', $startPreviousInterval)
            ->where('created_at', '<=', $startInterval)
            ->count();
        $newPreviewEvents = $instance->events()
            ->where('created_at', '>=', $startPreviousInterval)
            ->where('created_at', '<=', $startInterval)
            ->count();
        $newPreviewMedias = $instance->medias()
            ->where('created_at', '>=', $startPreviousInterval)
            ->where('created_at', '<=', $startInterval)
            ->count();
        $newPreviewChannels = $instance->events()
            ->where('created_at', '>=', $startPreviousInterval)
            ->where('created_at', '<=', $startInterval)
            ->count();
        $newPreviewLikes = $instance->likes()
            ->where('created_at', '>=', $startPreviousInterval)
            ->where('created_at', '<=', $startInterval)
            ->count();
        $newPreviewComments = $instance->comments()
            ->where('created_at', '>=', $startPreviousInterval)
            ->where('created_at', '<=', $startInterval)
            ->count();

        // top users
        $usersIds = $instance->users()->pluck('id')->toArray();
        // posts per user = *2.5
        $topUsersPosts = NewsFeed::select(\DB::raw('count(id)*2.5 as score, users_id'))
            ->whereIn('users_id', $usersIds)
            ->where('author_type', '!=', 'App\\Channel')
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->groupBy('users_id')
            ->get();
        // comments per user = *2
        $topUsersComments = Comment::select(\DB::raw('count(id)*2 as score, users_id'))
            ->whereIn('users_id', $usersIds)
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->groupBy('users_id')
            ->get();
        // likes per user = *0.5
        $topUsersLikes = Like::select(\DB::raw('count(id)*0.5 as score, users_id'))
            ->whereIn('users_id', $usersIds)
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->groupBy('users_id')
            ->get();
        // shares per user = *1.5
        $topUsersShares = Share::select(\DB::raw('count(id)*1.5 as score, users_id'))
            ->whereIn('users_id', $usersIds)
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->groupBy('users_id')
            ->get();

        // views = *0.1
        $topUsersShares = View::select(\DB::raw('count(id)*0.1 as score, users_id'))
            ->whereIn('users_id', $usersIds)
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->groupBy('users_id')
            ->get();

        $topUsersComputes = [
            $topUsersPosts,
            $topUsersComments,
            $topUsersLikes,
            $topUsersShares
        ];

        $topUsers = StatsHelper::computeTopusers($topUsersComputes);

        // @TODO top posts

        $data = [
            'period' => $period,
            'startPeriod' => $startInterval,
            'endPeriod' => $endInterval,
            'startPreviousPeriod' => $startPreviousInterval,
            'users' => $users,
            'groups' => $groups,
            'news' => $news,
            'channelsPosts' => $channelsPosts,
            'events' => $events,
            'offers' => $offers,
            'medias' => $medias,
            'channels' => $channels,
            'connexions' => $instanceConnexions,
            'newUsers' => $instanceNewUsers,
            'newGroups' => $newGroups,
            'newNews' => $newNews,
            'newChannelsPosts' => $newChannelsPosts,
            'newEvents' => $newEvents,
            'newOffers' => $newOffers,
            'newMedias' => $newMedias,
            'newChannels' => $newChannels,
            'previewConnexions' => $instancePreviewConnexions,
            'newPreviewUsers' => $instancePreviewNewUsers,
            'newPreviewGroups' => $newPreviewGroups,
            'newPreviewNews' => $newPreviewNews,
            'newPreviewChannelsPosts' => $newPreviewChannelsPosts,
            'newPreviewEvents' => $newPreviewEvents,
            'newPreviewOffers' => $newPreviewOffers,
            'newPreviewMedias' => $newPreviewMedias,
            'newPreviewChannels' => $newPreviewChannels,
            'monoProfile' => true,
            'likes' => $likes,
            'comments' => $comments,
            'newLikes' => $newLikes,
            'newComments' => $newComments,
            'newPreviewLikes' => $newPreviewLikes,
            'newPreviewComments' => $newPreviewComments,
            'topUsers' => $topUsers,
            'instance' => $instance,
        ];

        if (!session('instanceMonoProfile')) {
            $houses = $instance->houses()->where('active', '=', 1)->count();
            $projects = $instance->projects()->where('active', '=', 1)->count();

            $newHouses = $instance->houses()
                ->where('created_at', '>=', $startInterval)
                ->where('created_at', '<=', $endInterval)
                ->count();
            $newProjects = $instance->projects()
                ->where('created_at', '>=', $startInterval)
                ->where('created_at', '<=', $endInterval)
                ->count();

            $newPreviewHouses = $instance->houses()
                ->where('created_at', '>=', $startPreviousInterval)
                ->where('created_at', '<=', $startInterval)
                ->count();
            $newPreviewProjects = $instance->projects()
                ->where('created_at', '>=', $startPreviousInterval)
                ->where('created_at', '<=', $startInterval)
                ->count();

            $data['monoProfile'] = false;
            $data['houses'] = $houses;
            $data['projects'] = $projects;
            $data['newHouses'] = $newHouses;
            $data['newProjects'] = $newProjects;
            $data['newPreviewHouses'] = $newPreviewHouses;
            $data['newPreviewProjects'] = $newPreviewProjects;
        }
        return view('instances.stats', $data);
    }
}
