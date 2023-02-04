<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Repository\NotificationsRepository;
use App\User;
use App\Instance;
use App\Friends;
use App\Notif;

class DirectoryController extends BaseController
{
    public function home()
    {
        $dataUser = User::find(auth()->guard('web')->user()->id);
        $data['profile'] = $dataUser;

        $instance = Instance::find(session('instanceId'));
        $profiles = $instance
            ->users()
            ->where('active', '=', 1)
            ->orderBy('firstname', 'ASC')
            ->orderBy('name', 'ASC')
            ->take(15)
            ->get();
        $data['results'] = $profiles;

        $notificationsRepository = new NotificationsRepository();
        $data['friends'] = $notificationsRepository->listFriends();

        return view('directory.home', $data);
    }

    public function scroll()
    {
        $size = 15;
        $array = request()->get('postData');

        $from = ($array['limit']>=0 && $array['limit']!=null) ? $array['limit'] : 15;
        $limit = $from + $size;
        $query = $array['query'] ?? "";
        $instance = Instance::find(session('instanceId'));
        $friends = [];
        $user = auth()->guard('web')->user();
        if ($query!="") {
            $profiles = $instance->users()
                        ->where('active', '=', 1)
                        ->where(function ($w) use ($query) {
                            $w->whereRaw('CONCAT(firstname," ",name) LIKE "'.$query.'%"')
                              ->orWhereRaw('CONCAT(name," ",firstname) LIKE "'.$query.'%"');
                        })
                        ->orderBy('firstname', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->skip($from)
                        ->take($limit)
                        ->get();
            $allFriends = $user->friendsList();
            foreach ($allFriends as $friend) {
            // dump(strpos(strtolower($friend->name), strtolower($query)));
                if (strpos(strtolower($friend->name), strtolower($query)) === 0
                    or strpos(strtolower($friend->firstname), strtolower($query)) === 0) {
                    $friends[] = $friend;
                }
            }
            // dump($friends);
        } else {
            $friends = $user->friendsList();
            $profiles = $instance
                        ->users()
                        ->where('active', '=', 1)
                        ->orderBy('firstname', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->skip($from)
                        ->take($limit)
                        ->get();
        }
        $data['results'] = $profiles;
        // dd($data);
        return response()->json([
            'body' => view('directory.list', $data)->render(),
            'friends' => view('directory.list', ['results'=>$friends])->render(),
            'limit'=>$limit
        ]);
    }

    public function tooltip()
    {
        $id = request()->get('postData');
        // \Log::info($id);
        $instance = Instance::find(session('instanceId'));
        $user = $instance->users()->where('active', '=', 1)->where('id', $id)->first();
        return response()->json(['body'=>view('directory.tooltip', ['profile'=>$user])->render()]);
    }

    public function addFriend()
    {
        $dataJson = array();
        // formate string to json and decode json to array
        $data = request()->get('postData');
        $type = request()->get('type');

        $notif = new Notif();
        $friends = new Friends();
        $friends->instances_id = session('instanceId');
        $friends->users_id = $data['author_id'];
        $friends->friends_id = $data['user_from'];
        $friends->blacklist = 0;
        $friends->status = 0;

        $userInvited = User::find($data['author_id']);
        $user = User::find($data['user_from']);

        if ($data['user_from'] != auth()->guard('web')->user()->id
            || $data['author_id'] == $data['user_from']
            || !$userInvited->instances->contains(session('instanceId'))) {
            return response(view('errors.403'), 403);
        }

        $parameter = [
            'user_from' => $data['user_from']
        ];

        // check if friend relation exists
        $checkRelation = Friends::where('instances_id', '=', session('instanceId'))
            ->where(function ($wF) use ($data) {
                $wF->orWhere(function ($wF1) use ($data) {
                        $wF1->where('users_id', '=', $data['author_id'])
                            ->where('friends_id', '=', $data['user_from']);
                })
                    ->orWhere(function ($wF2) use ($data) {
                        $wF2->where('users_id', '=', $data['user_from'])
                            ->where('friends_id', '=', $data['author_id']);
                    });
            })
            ->first();

        $deleteNotif = Notif::where('instances_id', '=', session('instanceId'))
            ->where(function ($wT) {
                $wT->orWhere('type', '=', 'askFriend')
                   ->orWhere('type', '=', 'friendOk');
            })
            ->where(function ($wF) use ($data) {
                $wF->orWhere(function ($wF1) use ($data) {
                    $wF1->where('user_from', '=', $data['author_id'])
                        ->where('author_id', '=', $data['user_from'])
                        ->where('author_type', '=', 'App\\User');
                })
                ->orWhere(function ($wF2) use ($data) {
                    $wF2->where('user_from', '=', $data['user_from'])
                        ->where('author_id', '=', $data['author_id'])
                        ->where('author_type', '=', 'App\\User');
                });
            })
            ->get();

        switch ($type) {
            case 'add':
                if ($data['author_type'] == 'user') {
                    if ($checkRelation == null) { // add relation
                        $addFriend = $notif->insertAuthor(
                            $data['author_id'],
                            $data['user_from'],
                            $parameter,
                            'askFriend'
                        );
                        $friends->save();

                        $dataJson['addThis'] = true;
                        $dataJson['displayText'] = trans('page.add_in_progress');
                    } else { //delete relation
                        $checkRelation->delete();

                        foreach ($deleteNotif as $notification) {
                            $notification->delete();
                        }

                        // update subscribe to confidentiality 1
                        $user
                            ->subscriptionsList()
                            ->where('profile_type', '=', 'App\\User')
                            ->where('profile_id', '=', $data['author_id'])
                            ->update(['confidentiality' => 1]);

                        $dataJson['suppThis'] = true;
                        $dataJson['displayText'] = trans('page.add_friend');
                    }
                }
                break;

            case 'unlocked':
                if ($checkRelation == null) {
                    $checkRelation->delete();
                }

                if (! empty($unlocked)) {
                    $unlocked->delete();

                    $dataJson['unlockedThis'] = true;
                    $dataJson['displayText'] = trans('page.add_friend');
                } else {
                    if ($checkRelation->count() == 0) {
                        $notif->save();
                        $friends->save();

                        $dataJson['ReAddThis'] = true;
                        $dataJson['displayText'] = trans('page.add_in_progress');
                    } else {
                        $checkRelation->delete();

                        foreach ($deleteNotif as $notification) {
                            $notification->delete();
                        }

                        $dataJson['ReSuppThis'] = true;
                        $dataJson['displayText'] = trans('page.add_friend');
                    }
                }
                break;
        }

        return response()->json($dataJson);
    }

    public function friendAnswer($action)
    {
        $dataJson = array();
        // formate string to json and decode json to array
        $data = request()->get('postData');
        //$type = request()->get('type');

        if ($data['users_id'] != auth()->guard('web')->user()->id) {
            return response(view('errors.403'), 403);
        }

        $notif = new Notif();
        $friends = new Friends();

        $deleteNotif = Notif::where('instances_id', '=', session('instanceId'))
            ->where(function ($wT) {
                $wT->orWhere('type', '=', 'askFriend')
                ->orWhere('type', '=', 'friendOk');
            })
            ->where(function ($wF) use ($data) {
                $wF->orWhere(function ($wF1) use ($data) {
                    $wF1->where('user_from', '=', $data['friend_id'])
                    ->where('author_id', '=', $data['users_id'])
                    ->where('author_type', '=', 'App\\User');
                })
                ->orWhere(function ($wF2) use ($data) {
                    $wF2->where('user_from', '=', $data['users_id'])
                    ->where('author_id', '=', $data['friend_id'])
                    ->where('author_type', '=', 'App\\User');
                });
            })
            ->get();

        // check if friend relation exists
        $checkRelation = Friends::where('instances_id', '=', session('instanceId'))
            ->where(function ($wF) use ($data) {
                $wF->orWhere(function ($wF1) use ($data) {
                    $wF1->where('users_id', '=', $data['friend_id'])
                    ->where('friends_id', '=', $data['users_id']);
                })
                ->orWhere(function ($wF2) use ($data) {
                    $wF2->where('users_id', '=', $data['users_id'])
                    ->where('friends_id', '=', $data['friend_id']);
                });
            })
            ->first();

        switch ($action) {
            case 'accepted':
                foreach ($deleteNotif as $notification) {
                    $notification->delete();
                }

                $checkRelation->status = 1;
                $checkRelation->save();

                $parameter = [
                    'user_from' => $data['users_id']
                ];

                $friendOk = $notif->insertAuthor($data['friend_id'], $data['users_id'], $parameter, 'friendOk');

                break;

            case 'deny':
                foreach ($deleteNotif as $notification) {
                    $notification->delete();
                }
                $checkRelation->delete();
                break;

            case 'blacklist':
                foreach ($deleteNotif as $notification) {
                    $notification->delete();
                }
                $checkRelation->blacklist = 1;
                $checkRelation->save();

                break;
            default:
                break;
        }

        return response()->json($dataJson);
    }


    public function deleteFriend()
    {
        $dataJson = array();
        // formate string to json and decode json to array
        $data = request()->get('postData');

        $friends = new Friends();
        $notif = new Notif();

        // check if friend relation exists
        $checkRelation = Friends::where('instances_id', '=', session('instanceId'))
        ->where(function ($wF) use ($data) {
            $wF->orWhere(function ($wF1) use ($data) {
                $wF1->where('users_id', '=', $data['friend_id'])
                ->where('friends_id', '=', $data['users_id']);
            })
            ->orWhere(function ($wF2) use ($data) {
                $wF2->where('users_id', '=', $data['users_id'])
                ->where('friends_id', '=', $data['friend_id']);
            });
        })
        ->delete();

        $deleteNotif = Notif::where('instances_id', '=', session('instanceId'))
        ->where(function ($wT) {
            $wT->orWhere('type', '=', 'askFriend')
            ->orWhere('type', '=', 'friendOk');
        })
        ->where(function ($wF) use ($data) {
            $wF->orWhere(function ($wF1) use ($data) {
                $wF1->where('user_from', '=', $data['friend_id'])
                ->where('author_id', '=', $data['users_id'])
                ->where('author_type', '=', 'App\\User');
            })
            ->orWhere(function ($wF2) use ($data) {
                $wF2->where('user_from', '=', $data['users_id'])
                ->where('author_id', '=', $data['friend_id'])
                ->where('author_type', '=', 'App\\User');
            });
        })
        ->get();
        foreach ($deleteNotif as $notification) {
            // $notification->delete();
        }

        return response()->json($dataJson);
    }
}
