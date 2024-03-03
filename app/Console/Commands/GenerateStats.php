<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Instance;
use App\Stat;
use Carbon\Carbon;

class GenerateStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:stats {date? : date of first day to gen stats, if empty, it take day minus 1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $startDate;
    private $statDate;
    private $instances;
    private $instance;
    private $users;
    private $communities;
    private $houses;
    private $projects;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // manage period
        if (!empty($this->argument('date'))) {
            $now = Carbon::createFromFormat('Y-m-d', $this->argument('date'));
        } else {
            $now = Carbon::now()
                ->subDays(1);
        }

        $this->startDateRecord = $now->startOfWeek()->format('Y-m-d');
        $this->startDate = $now->startOfWeek()->format('Y-m-d H:i:s');
        $this->endDate = $now->endOfWeek()->format('Y-m-d H:i:s');

        // get all active instances
        $this->instances = Instance::whereActive(1)->get();
        $this->info($this->startDate . ' --> ' . $this->endDate);

        // launch generations
        foreach ($this->instances as $instance) {
            $this->instance = $instance;
            session(['instanceId' => $this->instance->id]);
            $this->users = $this->instance->activeUsers;
            $this->communities = $this->instance->communities()->where('active', '=', 1)->get();
            $this->houses = $this->instance->houses()->where('active', '=', 1)->get();
            $this->projects = $this->instance->projects()->where('active', '=', 1)->get();
            $this->generatePostsStats();
            $this->generateMediasStats();
            $this->generateLikesStats();
            $this->generateSharesStats();
            $this->generateCommentsStats();
            $this->generateUsersStats();
        }

        return Command::SUCCESS;
    }

    private function generatePostsStats()
    {
        // generate posts stats for users
        foreach ($this->users as $user) {
            $medias = $user->truePosts()
                ->leftJoin('views', function ($join) {
                    $join->on('views.post_id', '=', 'news_feeds.id');
                })
                ->where('views.created_at', '>=', $this->startDate)
                ->where('views.created_at', '<=', $this->endDate)
                ->where('views.post_type', '=', 'App\\NewsFeed')
                ->get();

            // store in stats
            $this->recordStat('post', $user, count($medias), $medias);
        }

        // generate posts stats for communities
        foreach ($this->communities as $community) {
            $posts = $community->posts()
                ->leftJoin('views', function ($join) {
                    $join->on('views.post_id', '=', 'news_feeds.id');
                })
                ->where('views.created_at', '>=', $this->startDate)
                ->where('views.created_at', '<=', $this->endDate)
                ->where('views.post_type', '=', 'App\\NewsFeed')
                ->get();

            // store in stats
            $this->recordStat('post', $community, count($posts), $posts);
        }

        // generate posts stats for houses
        foreach ($this->houses as $house) {
            $posts = $house->posts()
                ->leftJoin('views', function ($join) {
                    $join->on('views.post_id', '=', 'news_feeds.id');
                })
                ->where('views.created_at', '>=', $this->startDate)
                ->where('views.created_at', '<=', $this->endDate)
                ->where('views.post_type', '=', 'App\\NewsFeed')
                ->get();

            // store in stats
            $this->recordStat('post', $house, count($posts), $posts);
        }

        // generate posts stats for projects
        foreach ($this->projects as $project) {
            $posts = $project->posts()
                ->leftJoin('views', function ($join) {
                    $join->on('views.post_id', '=', 'news_feeds.id');
                })
                ->where('views.created_at', '>=', $this->startDate)
                ->where('views.created_at', '<=', $this->endDate)
                ->where('views.post_type', '=', 'App\\NewsFeed')
                ->get();

            // store in stats
            $this->recordStat('post', $project, count($posts), $posts);
        }
    }

    private function generateMediasStats()
    {
        // generate medias stats for users
        foreach ($this->users as $user) {
            $posts = $user->medias()
                ->leftJoin('views', function ($join) {
                    $join->on('views.post_id', '=', 'medias.id');
                })
                ->where('views.created_at', '>=', $this->startDate)
                ->where('views.created_at', '<=', $this->endDate)
                ->where('views.post_type', '=', 'App\\Media')
                ->whereIn('views.type', [1, 3])
                ->get();

            // store in stats
            $this->recordStat('medias', $user, count($posts), $posts);
        }


        // generate medias stats for communities
        foreach ($this->communities as $community) {
            $posts = $community->medias()
                ->leftJoin('views', function ($join) {
                    $join->on('views.post_id', '=', 'medias.id');
                })
                ->where('views.created_at', '>=', $this->startDate)
                ->where('views.created_at', '<=', $this->endDate)
                ->where('views.post_type', '=', 'App\\Media')
                ->whereIn('views.type', [1, 3])
                ->get();

            // store in stats
            $this->recordStat('medias', $community, count($posts), $posts);
        }

        // generate medias stats for houses
        foreach ($this->houses as $house) {
            $posts = $house->medias()
                ->leftJoin('views', function ($join) {
                    $join->on('views.post_id', '=', 'medias.id');
                })
                ->where('views.created_at', '>=', $this->startDate)
                ->where('views.created_at', '<=', $this->endDate)
                ->where('views.post_type', '=', 'App\\Media')
                ->whereIn('views.type', [1, 3])
                ->get();

            // store in stats
            $this->recordStat('medias', $house, count($posts), $posts);
        }

        // generate medias stats for projects
        foreach ($this->projects as $project) {
            $posts = $project->medias()
                ->leftJoin('views', function ($join) {
                    $join->on('views.post_id', '=', 'medias.id');
                })
                ->where('views.created_at', '>=', $this->startDate)
                ->where('views.created_at', '<=', $this->endDate)
                ->where('views.post_type', '=', 'App\\Media')
                ->whereIn('views.type', [1, 3])
                ->get();

            // store in stats
            $this->recordStat('medias', $project, count($posts), $posts);
        }
    }

    private function generateLikesStats()
    {
        // generate likes stats for users
        foreach ($this->users as $user) {
            $newsLikes = $user->news()
                ->leftJoin('likes', function ($join) {
                    $join->on('likes.liked_id', '=', 'news.id');
                })
                ->where('likes.created_at', '>=', $this->startDate)
                ->where('likes.created_at', '<=', $this->endDate)
                ->where('likes.liked_type', '=', 'App\\News')
                ->get();

            $offersLikes = $user->offers()
                ->leftJoin('likes', function ($join) {
                    $join->on('likes.liked_id', '=', 'offers.id');
                })
                ->where('likes.created_at', '>=', $this->startDate)
                ->where('likes.created_at', '<=', $this->endDate)
                ->where('likes.liked_type', '=', 'App\\Offer')
                ->get();

            $eventsLikes = $user->events()
                ->leftJoin('likes', function ($join) {
                    $join->on('likes.liked_id', '=', 'events.id');
                })
                ->where('likes.created_at', '>=', $this->startDate)
                ->where('likes.created_at', '<=', $this->endDate)
                ->where('likes.liked_type', '=', 'App\\TEvent')
                ->get();

            /*
            $sharesLikes = $user->shares()
                ->leftJoin('likes', function ($join) {
                    $join->on('likes.liked_id', '=', 'shares.id');
                })
                ->where('likes.created_at', '>=', $this->startDate)
                ->where('likes.created_at', '<=', $this->endDate)
                ->where('likes.liked_type', '=', 'App\\Share')
                ->get();
            */

            $mediasLikes = $user->medias()
                ->leftJoin('likes', function ($join) {
                    $join->on('likes.liked_id', '=', 'medias.id');
                })
                ->where('likes.created_at', '>=', $this->startDate)
                ->where('likes.created_at', '<=', $this->endDate)
                ->where('likes.liked_type', '=', 'App\\Media')
                ->get();

            $commentsLikes = $user->comments()
                ->leftJoin('likes', function ($join) {
                    $join->on('likes.liked_id', '=', 'comments.id');
                })
                ->where('likes.created_at', '>=', $this->startDate)
                ->where('likes.created_at', '<=', $this->endDate)
                ->where('likes.liked_type', '=', 'App\\Comment')
                ->get();

            $likes = $newsLikes->merge($offersLikes)
                ->merge($eventsLikes)
                //->merge($sharesLikes)
                ->merge($mediasLikes)
                ->merge($commentsLikes);
            $likesArray = [
                'news' => $newsLikes,
                'offers' => $offersLikes,
                'events' => $eventsLikes,
                //'shares' => $sharesLikes,
                'medias' => $mediasLikes,
                'comments' => $commentsLikes,
            ];

            // store in stats
            $this->recordStat('likes', $user, count($likes), $likesArray);
        }

        // generate likes stats for profiles
        $profilesNames = [
            'communities',
            'projects',
            'houses',
        ];

        // generate likes stats for all profiles
        foreach ($profilesNames as $profilesName) {
            foreach ($this->{$profilesName} as $profile) {
                $newsLikes = $profile->posts()
                    ->with(['post.liked'])
                    ->leftJoin('news', function ($join) {
                        $join->on('news.id', '=', 'news_feeds.post_id');
                    })
                    ->leftJoin('likes', function ($joinL) {
                        $joinL->on('likes.liked_id', '=', 'news.id');
                    })
                    ->where('likes.created_at', '>=', $this->startDate)
                    ->where('likes.created_at', '<=', $this->endDate)
                    ->where('likes.liked_type', '=', 'App\\News')
                    ->where('news_feeds.post_type', '=', 'App\\News')
                    ->get();

                $offersLikes = $profile->posts()
                    ->with(['post.liked'])
                    ->leftJoin('offers', function ($join) {
                        $join->on('offers.id', '=', 'news_feeds.post_id');
                    })
                    ->leftJoin('likes', function ($joinL) {
                        $joinL->on('likes.liked_id', '=', 'offers.id');
                    })
                    ->where('likes.created_at', '>=', $this->startDate)
                    ->where('likes.created_at', '<=', $this->endDate)
                    ->where('likes.liked_type', '=', 'App\\Offer')
                    ->where('news_feeds.post_type', '=', 'App\\Offer')
                    ->get();

                $eventsLikes = $profile->posts()
                    ->with(['post.liked'])
                    ->leftJoin('events', function ($join) {
                        $join->on('events.id', '=', 'news_feeds.post_id');
                    })
                    ->leftJoin('likes', function ($joinL) {
                        $joinL->on('likes.liked_id', '=', 'events.id');
                    })
                    ->where('likes.created_at', '>=', $this->startDate)
                    ->where('likes.created_at', '<=', $this->endDate)
                    ->where('likes.liked_type', '=', 'App\\TEvent')
                    ->where('news_feeds.post_type', '=', 'App\\TEvent')
                    ->get();

                $sharesLikes = $profile->posts()
                    ->with(['post.liked'])
                    ->leftJoin('shares', function ($join) {
                        $join->on('shares.id', '=', 'news_feeds.post_id');
                    })
                    ->leftJoin('likes', function ($joinL) {
                        $joinL->on('likes.liked_id', '=', 'shares.id');
                    })
                    ->where('likes.created_at', '>=', $this->startDate)
                    ->where('likes.created_at', '<=', $this->endDate)
                    ->where('likes.liked_type', '=', 'App\\Share')
                    ->where('news_feeds.post_type', '=', 'App\\Share')
                    ->get();

                $mediasLikes = $profile->medias()
                    ->leftJoin('likes', function ($join) {
                        $join->on('likes.liked_id', '=', 'medias.id');
                    })
                    ->where('likes.created_at', '>=', $this->startDate)
                    ->where('likes.created_at', '<=', $this->endDate)
                    ->where('likes.liked_type', '=', 'App\\Media')
                    ->get();

                $commentsLikes = $profile->comments()
                    ->leftJoin('likes', function ($joinL) {
                        $joinL->on('likes.liked_id', '=', 'comments.id');
                    })
                    ->where('likes.created_at', '>=', $this->startDate)
                    ->where('likes.created_at', '<=', $this->endDate)
                    ->where('likes.liked_type', '=', 'App\\Comment')
                    ->get();

                $likes = $newsLikes->merge($offersLikes)
                    ->merge($eventsLikes)
                    ->merge($sharesLikes)
                    ->merge($mediasLikes)
                    ->merge($commentsLikes);

                $likesArray = [
                    'news' => $newsLikes,
                    'offers' => $offersLikes,
                    'events' => $eventsLikes,
                    'events' => $sharesLikes,
                    'medias' => $mediasLikes,
                    'comments' => $commentsLikes,
                ];

                // store in stats
                $this->recordStat('likes', $profile, count($likes), $likesArray);
            }
        }
    }

    private function generateCommentsStats()
    {
        // generate comments stats for users
        foreach ($this->users as $user) {
            $newsComments = $user->news()
                ->leftJoin('comments', function ($join) {
                    $join->on('comments.post_id', '=', 'news.id');
                })
                ->where('comments.created_at', '>=', $this->startDate)
                ->where('comments.created_at', '<=', $this->endDate)
                ->where('comments.post_type', '=', 'App\\News')
                ->get();

            $offersComments = $user->offers()
                ->leftJoin('comments', function ($join) {
                    $join->on('comments.post_id', '=', 'offers.id');
                })
                ->where('comments.created_at', '>=', $this->startDate)
                ->where('comments.created_at', '<=', $this->endDate)
                ->where('comments.post_type', '=', 'App\\Offer')
                ->get();

            $eventsComments = $user->events()
                ->leftJoin('comments', function ($join) {
                    $join->on('comments.post_id', '=', 'events.id');
                })
                ->where('comments.created_at', '>=', $this->startDate)
                ->where('comments.created_at', '<=', $this->endDate)
                ->where('comments.post_type', '=', 'App\\TEvent')
                ->get();

            $sharesComments = $user->shares()
                ->leftJoin('comments', function ($join) {
                    $join->on('comments.post_id', '=', 'shares.id');
                })
                ->where('comments.created_at', '>=', $this->startDate)
                ->where('comments.created_at', '<=', $this->endDate)
                ->where('comments.post_type', '=', 'App\\Share')
                ->get();

            $mediasComments = $user->medias()
                ->leftJoin('comments', function ($join) {
                    $join->on('comments.post_id', '=', 'medias.id');
                })
                ->where('comments.created_at', '>=', $this->startDate)
                ->where('comments.created_at', '<=', $this->endDate)
                ->where('comments.post_type', '=', 'App\\Media')
                ->get();

            $comments = $newsComments->merge($offersComments)
                ->merge($eventsComments)
                ->merge($sharesComments)
                ->merge($mediasComments);

            $commentsArray = [
                'news' => $newsComments,
                'offers' => $offersComments,
                'events' => $eventsComments,
                'shares' => $sharesComments,
                'medias' => $mediasComments,
            ];

            // store in stats
            $this->recordStat('comments', $user, count($comments), $commentsArray);
        }

        // generate likes stats for profiles
        $profilesNames = [
            'communities',
            'projects',
            'houses',
        ];

        // generate likes stats for all profiles
        foreach ($profilesNames as $profilesName) {
            foreach ($this->{$profilesName} as $profile) {
                $newsComments = $profile->posts()
                    ->with(['post.comments'])
                    ->leftJoin('news', function ($join) {
                        $join->on('news.id', '=', 'news_feeds.post_id');
                    })
                    ->leftJoin('comments', function ($joinL) {
                        $joinL->on('comments.post_id', '=', 'news.id');
                    })
                    ->where('comments.created_at', '>=', $this->startDate)
                    ->where('comments.created_at', '<=', $this->endDate)
                    ->where('comments.post_type', '=', 'App\\News')
                    ->where('news_feeds.post_type', '=', 'App\\News')
                    ->get();

                $offersComments = $profile->posts()
                    ->with(['post.comments'])
                    ->leftJoin('offers', function ($join) {
                        $join->on('offers.id', '=', 'news_feeds.post_id');
                    })
                    ->leftJoin('comments', function ($joinL) {
                        $joinL->on('comments.post_id', '=', 'offers.id');
                    })
                    ->where('comments.created_at', '>=', $this->startDate)
                    ->where('comments.created_at', '<=', $this->endDate)
                    ->where('comments.post_type', '=', 'App\\Offer')
                    ->where('news_feeds.post_type', '=', 'App\\Offer')
                    ->get();

                $eventsComments = $profile->posts()
                    ->with(['post.comments'])
                    ->leftJoin('events', function ($join) {
                        $join->on('events.id', '=', 'news_feeds.post_id');
                    })
                    ->leftJoin('comments', function ($joinL) {
                        $joinL->on('comments.post_id', '=', 'events.id');
                    })
                    ->where('comments.created_at', '>=', $this->startDate)
                    ->where('comments.created_at', '<=', $this->endDate)
                    ->where('comments.post_type', '=', 'App\\TEvent')
                    ->where('news_feeds.post_type', '=', 'App\\TEvent')
                    ->get();

                $sharesComments = $profile->posts()
                    ->with(['post.comments'])
                    ->leftJoin('shares', function ($join) {
                        $join->on('shares.id', '=', 'news_feeds.post_id');
                    })
                    ->leftJoin('comments', function ($joinL) {
                        $joinL->on('comments.post_id', '=', 'shares.id');
                    })
                    ->where('comments.created_at', '>=', $this->startDate)
                    ->where('comments.created_at', '<=', $this->endDate)
                    ->where('comments.post_type', '=', 'App\\Share')
                    ->where('news_feeds.post_type', '=', 'App\\Share')
                    ->get();

                $mediasComments = $profile->medias()
                    ->leftJoin('comments', function ($join) {
                        $join->on('comments.post_id', '=', 'medias.id');
                    })
                    ->where('comments.created_at', '>=', $this->startDate)
                    ->where('comments.created_at', '<=', $this->endDate)
                    ->where('comments.post_type', '=', 'App\\Media')
                    ->get();

                $comments = $newsComments->merge($offersComments)
                    ->merge($eventsComments)
                    ->merge($sharesComments)
                    ->merge($mediasComments);

                $commentsArray = [
                    'news' => $newsComments,
                    'offers' => $offersComments,
                    'events' => $eventsComments,
                    'shares' => $sharesComments,
                    'medias' => $mediasComments,
                ];

                // store in stats
                $this->recordStat('comments', $profile, count($comments), $commentsArray);
            }
        }
    }

    private function generateSharesStats()
    {
        // generate comments stats for users
        foreach ($this->users as $user) {
            $newsShares = $user->news()
                ->leftJoin('shares', function ($join) {
                    $join->on('shares.post_id', '=', 'news.id');
                })
                ->where('shares.created_at', '>=', $this->startDate)
                ->where('shares.created_at', '<=', $this->endDate)
                ->where('shares.post_type', '=', 'App\\News')
                ->get();

            $offersShares = $user->offers()
                ->leftJoin('shares', function ($join) {
                    $join->on('shares.post_id', '=', 'offers.id');
                })
                ->where('shares.created_at', '>=', $this->startDate)
                ->where('shares.created_at', '<=', $this->endDate)
                ->where('shares.post_type', '=', 'App\\Offer')
                ->get();

            $eventsShares = $user->events()
                ->leftJoin('shares', function ($join) {
                    $join->on('shares.post_id', '=', 'events.id');
                })
                ->where('shares.created_at', '>=', $this->startDate)
                ->where('shares.created_at', '<=', $this->endDate)
                ->where('shares.post_type', '=', 'App\\TEvent')
                ->get();

            $mediasShares = $user->medias()
                ->leftJoin('shares', function ($join) {
                    $join->on('shares.post_id', '=', 'medias.id');
                })
                ->where('shares.created_at', '>=', $this->startDate)
                ->where('shares.created_at', '<=', $this->endDate)
                ->where('shares.post_type', '=', 'App\\Media')
                ->get();

            $shares = $newsShares->merge($offersShares)
                ->merge($eventsShares)
                ->merge($mediasShares);

            $sharesArray = [
                'news' => $newsShares,
                'offers' => $offersShares,
                'events' => $eventsShares,
                'medias' => $mediasShares,
            ];

            // store in stats
            $this->recordStat('shares', $user, count($shares), $sharesArray);
        }

        // generate likes stats for profiles
        $profilesNames = [
            'communities',
            'projects',
            'houses',
        ];

        // generate likes stats for all profiles
        foreach ($profilesNames as $profilesName) {
            foreach ($this->{$profilesName} as $profile) {
                $newsShares = $profile->posts()
                    ->with(['post.shares'])
                    ->leftJoin('news', function ($join) {
                        $join->on('news.id', '=', 'news_feeds.post_id');
                    })
                    ->leftJoin('shares', function ($joinL) {
                        $joinL->on('shares.post_id', '=', 'news.id');
                    })
                    ->where('shares.created_at', '>=', $this->startDate)
                    ->where('shares.created_at', '<=', $this->endDate)
                    ->where('shares.post_type', '=', 'App\\News')
                    ->where('news_feeds.post_type', '=', 'App\\News')
                    ->get();

                $offersShares = $profile->posts()
                    ->with(['post.shares'])
                    ->leftJoin('offers', function ($join) {
                        $join->on('offers.id', '=', 'news_feeds.post_id');
                    })
                    ->leftJoin('shares', function ($joinL) {
                        $joinL->on('shares.post_id', '=', 'offers.id');
                    })
                    ->where('shares.created_at', '>=', $this->startDate)
                    ->where('shares.created_at', '<=', $this->endDate)
                    ->where('shares.post_type', '=', 'App\\Offer')
                    ->where('news_feeds.post_type', '=', 'App\\Offer')
                    ->get();

                $eventsShares = $profile->posts()
                    ->with(['post.shares'])
                    ->leftJoin('events', function ($join) {
                        $join->on('events.id', '=', 'news_feeds.post_id');
                    })
                    ->leftJoin('shares', function ($joinL) {
                        $joinL->on('shares.post_id', '=', 'events.id');
                    })
                    ->where('shares.created_at', '>=', $this->startDate)
                    ->where('shares.created_at', '<=', $this->endDate)
                    ->where('shares.post_type', '=', 'App\\TEvent')
                    ->where('news_feeds.post_type', '=', 'App\\TEvent')
                    ->get();

                $mediasShares = $profile->medias()
                    ->leftJoin('shares', function ($join) {
                        $join->on('shares.post_id', '=', 'medias.id');
                    })
                    ->where('shares.created_at', '>=', $this->startDate)
                    ->where('shares.created_at', '<=', $this->endDate)
                    ->where('shares.post_type', '=', 'App\\Media')
                    ->get();

                $shares = $newsShares->merge($offersShares)
                    ->merge($eventsShares)
                    ->merge($mediasShares);

                $sharesArray = [
                    'news' => $newsShares,
                    'offers' => $offersShares,
                    'events' => $eventsShares,
                    'medias' => $mediasShares,
                ];

                // store in stats
                $this->recordStat('shares', $profile, count($shares), $sharesArray);
            }
        }
    }

    private function generateUsersStats()
    {
        // count instance active users total and new users in period
        $totalNewUsers = $this->instance->users()
            ->where('users.created_at', '>=', $this->startDate)
            ->where('users.created_at', '<=', $this->endDate)
            ->count();
        $this->recordStat('users', $this->instance, $totalNewUsers, [], true);

        // count users connexions
        $nbConnexions = \DB::table('user_auth_logger')
            ->where('instances_id', '=', $this->instance->id)
            ->where('created_at', '>=', $this->startDate)
            ->where('created_at', '<=', $this->endDate)
            ->count();
        $this->recordStat('connexions', $this->instance, $nbConnexions, [], true);

        // count profiles active users
        $profilesNames = [
            'communities',
            'projects',
            'houses',
        ];

        // generate likes stats for all profiles
        foreach ($profilesNames as $profilesName) {
            foreach ($this->{$profilesName} as $profile) {
                $totalNewUsers = $profile->allUsers()
                    ->wherePivot('created_at', '>=', $this->startDate)
                    ->wherePivot('created_at', '<=', $this->endDate)
                    ->count();
                $this->recordStat('users', $profile, $totalNewUsers, [], true);
            }
        }
    }

    private function recordStat($type, $entity, $counter, $collection, $forceRecord = false)
    {
        if ($counter != 0 || $forceRecord) {
            $stat = Stat::where('day', '=', $this->startDateRecord)
                ->where('instances_id', '=', $this->instance->id)
                ->where('entity_type', '=', get_class($entity))
                ->where('entity_id', '=', $entity->id)
                ->where('stat_type', '=', $type)
                ->firstOrNew();

            $stat->day = $this->startDateRecord;
            $stat->instances_id = $this->instance->id;
            $stat->entity_type = get_class($entity);
            $stat->entity_id = $entity->id;
            $stat->stat_type = $type;
            $stat->counter = $counter;
            $stat->stat_detail = json_encode($collection);
            $stat->save();
        }
    }
}
