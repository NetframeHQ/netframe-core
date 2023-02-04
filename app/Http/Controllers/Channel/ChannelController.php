<?php
namespace App\Http\Controllers\Channel;

use App\Http\Controllers\BaseController;
use App\Repository\SearchRepository2;
use App\Helpers\Lib\Acl;
use App\Channel;
use App\User;
use App\Netframe;
use App\Profile;
use App\Events\InterestAction;
use App\Events\AddProfile;
use App\Events\LivechatChannel;

class ChannelController extends BaseController
{

    public function __construct(SearchRepository2 $searchRepository)
    {
        $this->middleware('checkAppActive:channel');
        $this->middleware('checkAuth');
        parent::__construct();

        $this->searchRepository = $searchRepository;
    }

    public function main()
    {
        // get channel with more recent message
        $channel = auth()
            ->guard('web')
            ->user()
            ->allChannels()
            ->wherePivot('status', '=', '1')
            ->where('active', '=', 1)
            ->orderBy('updated_at', 'DESC')
            ->first();

        if ($channel != null) {
            session()->flash('channelDisplayId', $channel->id);
            session()->flash('mainChannel', true);

            return redirect()->route('channels.home', ['id' => $channel->id]);
        } else {
            // redirect to channel creation
            return redirect()->route('channel.edit');
        }
    }

    public function app($feedId, $when = null, $datetime = null)
    {
        $channel = Channel::findOrFail($feedId);
        return view('channel.app', ['channel', $channel]);
    }

    public function feeds()
    {
        $channels = auth()->guard('web')->user()->allChannels()->where('active', '=', 1)->orderBy('name')->get();
        $data = [];
        $data['returnCode'] = 'success';
        $data['channels'] = $channels;

        return response()->json($data);
    }

    public function myFeeds()
    {
        $channels = auth()
            ->guard('web')
            ->user()
            ->channels()
            ->wherePivot('roles_id', '<=', '2')
            ->orderBy('name')
            ->get();
        $data = [];
        $data['channels'] = $channels;

        return view('channel.all-feeds', $data);
    }

    public function markRead($feedId)
    {
        $channel = Channel::findOrFail($feedId);
        if ($channel->personnal == 1 && !auth()->guard('web')->user()->directMessagesChans->contains($feedId)) {
            return response(view('errors.403'), 403);
        }

        \DB::table('channels_has_news_feeds')
            ->where('channels_id', '=', $feedId)
            ->where('users_id', '=', auth()->guard('web')->user()->id)
            ->update(['read' => 1]);
    }

    public function feed($feedId, $when = null, $datetime = null)
    {
        $channel = Channel::findOrFail($feedId);

        // test access rights
        if ($channel->instances_id != session('instanceId')
            ||
            ($channel->personnal == 1 && !auth()->guard('web')->user()->directMessagesChans->contains($feedId))
            ||
            ($channel->personnal == 0
                && $channel->confidentiality == 0
                && !auth()->guard('web')->user()->channels->contains($feedId)
            )
        ) {
            return response(view('errors.403'), 403);
        }

        session()->reflash();
        if (request()->isMethod('POST')) {
            $orderBy = ($when == 'after') ? 'asc' : 'desc';

            \DB::table('channels_has_news_feeds')
                ->where('channels_id', '=', $feedId)
                ->where('users_id', '=', auth()->guard('web')->user()->id)
                ->update(['read' => 1]);

            $channel = Channel::find($feedId);

            if ($channel->personnal == 1 && !auth()->guard('web')->user()->directMessagesChans->contains($feedId)) {
                return response(view('errors.403'), 403);
            }

            if ($channel->confidentiality == 0 || auth()->guard('web')->user()->channels->contains($feedId)) {
                $channel = auth()->guard('web')->user()->channels()->where('channels.id', '=', $feedId)->first();
            }

            $takeMessages = ($when == null) ? 30 : 15;

            $posts = $channel->posts()
                ->where(function ($wd) use ($when, $datetime) {
                    if ($when != null) {
                        if ($when == 'before') {
                            $compare = '<';
                        } elseif ($when == 'after') {
                            $compare = '>';
                        }
                        $wd->where('updated_at', $compare, $datetime);
                    }
                })
                ->orderBy('updated_at', $orderBy)
                ->take($takeMessages)
                ->with(['post'])
                ->get()
                ->reverse();

            $data = [];

            foreach ($posts as $post) {
                $post->post->newsFeedId = $post->id;
                $data[] = $post->post;
            }

            return response()->json($data);
        } else {
            $data = [];
            $channel = Channel::findOrFail($feedId);
            if ($channel->personnal == 0) {
                if ($channel->confidentiality == 0 || auth()->guard('web')->user()->channels->contains($feedId)) {
                    $channel = auth()->guard('web')->user()->channels()->where('id', '=', $feedId)->first();
                    $data['joined'] = null;
                } else {
                    $joined = $channel->users()->where('users_id', '=', auth()->guard('web')->user()->id)->first();
                    if ($joined != null) {
                        $joined = $joined->pivot->status;
                    }
                    $data['joined'] = $joined;
                }
            } else {
                $channel = auth()
                    ->guard('web')
                    ->user()
                    ->directMessagesChans()
                    ->where('channels.id', '=', $feedId)
                    ->first();
                $data['joined'] = null;
            }
            if ($channel == null) {
                $channel = Channel::findOrFail($feedId);
            }

            if ($channel->personnal == 1 && $channel->newsfeed()->count() == 0) {
                view()->share('currentChannel', $channel);
            }

            $joined = $channel->users()->where('users_id', '=', auth()->guard('web')->user()->id)->first();
            if ($joined != null) {
                $joined = $joined->pivot->status;
            }

            $data['unitPost'] = false;
            $data['joined'] = $joined;
            $data['rights'] = $this->Acl->getRights('App\Channel', $feedId);
            $data['channel'] = $channel;

            session()->flash('channelDisplayId', $channel->id);

            return view('channel.app', $data);
        }
    }

    public function edit($id = null)
    {
        $data = [];

        if (!is_null($id) && (!Acl::getRights('channel', $id) || Acl::getRights('channel', $id) > 2 )) {
            return response(view('errors.403'), 403);
        }

        $channel = Channel::findOrNew($id);

        if (request()->isMethod('POST')) {
            $validator = validator(request()->all(), config('validation.channel/edit'));
            if ($validator->fails()) {
                $channel->name= request()->get('name');
                $channel->description = request()->get('description');
                $channel->confidentiality = (
                    request()->has('confidentiality') && request()->get('confidentiality') == 1
                ) ? 0 : 1;
                $channel->save();

                $profileModel = Profile::gather(request()->get('type_foreign'));
                $profile = $profileModel::find(request()->get('id_foreign'));
                $channel->profile = $profile;

                $channel->formTags = \App\Helpers\TagsHelper::getFromForm(request()->get('tags'));
                $channel->formTagsSelecteds = request()->get('tags');

                $data['channel'] = $channel;

                session()->flash('channelDisplayId', $channel->id);

                return view('channel.form.edit', $data)->withErrors($validator);
            } else {
                // test rights on channel profile
                if (!Acl::getRights(request()->get('type_foreign'), request()->get('id_foreign'))
                    || Acl::getRights(request()->get('type_foreign'), request()->get('id_foreign')) > 4) {
                    return response(view('errors.403'), 403);
                }

                if ($id != null) {
                    $oldTags = $channel->tags;
                }

                // create channel
                $channel->instances_id = session('instanceId');
                $channel->users_id = auth()->guard('web')->user()->id;
                $channel->profile_id = request()->get('id_foreign');
                $channel->profile_type = 'App\\'.ucfirst(request()->get('type_foreign'));
                $channel->name = request()->get('name');
                $channel->description = request()->get('description');
                $channel->confidentiality = (
                    request()->has('confidentiality') && request()->get('confidentiality') == 1
                ) ? 0 : 1;
                $channel->save();

                // Save the tags
                \App\Helpers\TagsHelper::attachPostedTags(request()->get('tags'), $channel);

                //insert interest
                if ($id == null) {
                    $tags = $channel->tags;
                } else {
                    $channel->load('tags');
                    $tags = \App\Helpers\TagsHelper::compareTags($oldTags, $channel->tags);
                }

                if ($channel->tags != null) {
                    event(new InterestAction(auth()->guard('web')->user(), $channel->tags, 'profile.create'));
                }

                if ($id == null) {
                    auth()->guard('web')->user()->channels()->attach($channel->id, ['roles_id' => 1, 'status' => 1]);

                    // update acl
                    session([
                        "acl" => Netframe::getAcl(auth()->guard('web')->user()->id)
                    ]);

                    \App\Helpers\ActionMessageHelper::success(trans('channels.edit.result.add'));

                    session([
                        "allFeeds" => auth()
                            ->guard('web')
                            ->user()
                            ->channels()
                            ->where('active', '=', 1)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                    ]);
                } else {
                    \App\Helpers\ActionMessageHelper::success(trans('channels.edit.result.update'));
                }
                return redirect()->route('channel.edit', ['id' => $channel->id]);
            }
        } else {
        }

        if ($id == null) {
            $channel->confidentiality = 1;
        } else {
            $oldTags = $channel->tags;
        }

        $data['channel'] = $channel;

        session()->flash('channelDisplayId', $channel->id);

        return view('channel.form.edit', $data);
    }

    public function delete($id)
    {
        $data = [];

        if (!is_null($id) && (!Acl::getRights('channel', $id) || Acl::getRights('channel', $id) > 2 )) {
            return response(view('errors.403'), 403);
        }

        $channel = Channel::findOrFail($id);

        $channel->delete();

        return redirect()->route('channels.my.feeds');
    }

    public function disable($id, $active = 0)
    {
        if (!is_null($id) && (!Acl::getRights('channel', $id) || Acl::getRights('channel', $id) > 2 )) {
            return response(view('errors.403'), 403);
        }

        $channel = Channel::findOrFail($id);
        $channel->active = $active;
        $channel->save();

        return redirect()->route('channel.edit', ['id' => $channel->id]);
    }

    public function unread($id)
    {
        $unread = \DB::table('channels_has_news_feeds')
            ->where('channels_id', '=', $id)
            ->where('users_id', '=', auth()->guard('web')->user()->id)
            ->where('read', '=', 0)
            ->selectRaw('count(*) as unreadMessages')
            ->pluck('unreadMessages');

        $data = [];
        $data['channelId'] = $id;
        $data['returnCode'] = 'success';
        $data['unread'] = $unread[0];

        return response()->json($data);
    }

    /*
     * edit community of project
     * @param int $id id of the project
     * @param int $status refers to members status (config netframe.members_status)
     */
    public function editCommunity($id, $status = null)
    {
        if (!is_null($id) && (!Acl::getRights('channel', $id) || Acl::getRights('channel', $id) > 2 )) {
            return response(view('errors.403'), 403);
        }

        $channel = Channel::findOrFail($id);

        //get project members
        $communityStatus = config('netframe.members_status');

        $channelCommunity = $channel
            ->users()
            ->wherePivot('status', $status)
            ->orderBy('channels_has_users.roles_id')
            ->get();

        $data = [];
        $data['profile'] = $channel;
        $data['channel'] = $channel;
        $data['profileCommunity'] = $channelCommunity;
        $data['communityType'] = $communityStatus[$status];

        session()->flash('channelDisplayId', $channel->id);

        return view('join.community', $data);
    }

    public function inviteUsers($id)
    {
        if (!is_null($id) && (!Acl::getRights('channel', $id) || Acl::getRights('channel', $id) > 2 )) {
            return response(view('errors.403'), 403);
        }

        $channel = Channel::findOrFail($id);

        $query = '';
        $loadFilters = (request()->has('loadFilters')) ? request()->get('loadFilters') : true ;
        $hashtag = (request()->has('$hashtag')) ? request()->get('$hashtag') : '';
        $placeSearch = (request()->has('placeSearch')) ? request()->get('placeSearch') : '';
        $targetsProfiles = ['user' => 1];
        $byInterests = (request()->has('byInterests') && request()->get('byInterests') == 1) ? 1 : 0;

        $this->searchRepository->route = 'search_results';
        $this->searchRepository->targetsProfiles = $targetsProfiles;
        $this->searchRepository->toggleFilter = false;
        $this->searchRepository->byInterests = $byInterests;
        $this->searchRepository->newProfile = 0;
        $this->searchRepository->inviteProfile = $channel;

        $searchParameters = $this->searchRepository->initializeConfig(
            'search_results',
            $targetsProfiles,
            false,
            $byInterests
        );
        $results = $this->searchRepository->search($searchParameters, $targetsProfiles);

        $data = [];
        $data['profile'] = $channel;
        $data['channel'] = $channel;
        $data['profileCommunity'] = $results[0];

        session()->flash('channelDisplayId', $channel->id);

        return view('join.invite', $data);
    }

    public function messenger($userId)
    {
        // check if both user have a private channel
        $contacts = [$userId, auth()->guard('web')->user()->id];
        $channel = Channel::where('personnal', '=', 1)
            ->leftJoin('channels_has_users as chu1', 'chu1.channels_id', '=', 'channels.id')
            ->leftJoin('channels_has_users as chu2', 'chu2.channels_id', '=', 'channels.id')
            ->where('chu1.users_id', '=', $userId)
            ->where('chu2.users_id', '=', auth()->guard('web')->user()->id)
            ->first();

        if ($channel == null) {
            // create a new private channel for this users
            $channel = $this->createChannel($userId);
        }

        // redirect to channel

        return redirect()->route('channels.home', ['id' => $channel->id]);
    }

    public function searchContacts()
    {
        $query = request()->get('query');
        $targetsProfiles = ['user' => 1];

        $this->searchRepository->route = 'search_results';
        $this->searchRepository->targetsProfiles = $targetsProfiles;
        $this->searchRepository->toggleFilter = false;
        $this->searchRepository->byInterests = 0;
        $this->searchRepository->newProfile = 0;

        $searchParameters = $this->searchRepository->initializeConfig('search_results', $targetsProfiles, false, 0);
        $results = $this->searchRepository->search($searchParameters, $targetsProfiles);

        $returnResult = [];
        foreach ($results[0] as $user) {
            $returnResult[] = [
                'id' => $user->id,
                'name' => $user->getNameDisplay(),
                'profileImage' => ($user->profileImage != null) ? $user->profileImage->id : null,
                'online' => ($user->isOnline()) ? 'status-online' : 'status-offline',
            ];
        }

        return response()->json(['users' => $returnResult]);
    }

    public function livechat($channelId, $form = false, $fromUser = false)
    {
        if ($fromUser) {
            $channel = Channel::where('personnal', '=', 1)
                ->leftJoin('channels_has_users as chu1', 'chu1.channels_id', '=', 'channels.id')
                ->leftJoin('channels_has_users as chu2', 'chu2.channels_id', '=', 'channels.id')
                ->where('chu1.users_id', '=', $channelId)
                ->where('chu2.users_id', '=', auth()->guard('web')->user()->id)
                ->first();
            if ($channel == null) {
                // create a new private channel for this users
                $channel = $this->createChannel($channelId);
            }
        } else {
            $channel = Channel::find($channelId);
        }

        if ($channel->personnal == 1 && !auth()->guard('web')->user()->directMessagesChans->contains($channel->id)) {
            return response(view('errors.403'), 403);
        }

        if ($channel->confidentiality == 0 || auth()->guard('web')->user()->channels->contains($channel->id)) {
            $channel = auth()->guard('web')->user()->channels()->where('id', '=', $channel->id)->first();
        }

        $form = true;

        if ($form) {
            // temporary redirect to jisti meet global service
            $visioConf = config('external-api.jitsi');
            $roomFullName = 'netframe-instance-' .
                session('instanceId') .
                '-channel-' .
                $channel->id .
                '-' .
                $visioConf['keyMeet'];
            return redirect()->to('https://meet.jit.si/' . $roomFullName);

            // standard redirect
            $visioConf = config('external-api.jitsi');
            $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

            // expire token
            $now = strtotime(date('Y-m-d H:i:s'));
            $expireToken = $now + 86400;

            //$group = 'instance1';
            $room = 'instance-'.session('instanceId').'-channel-'.$channel->id;
            $iss = $visioConf['iss'];
            $sub = $visioConf['url'];

            if (auth('web')->user()->profileImage !=null) {
                $avatar = auth('web')->user()->profileImage->getUrl();
            } else {
                $avatar = 'https://work.netframe.co/assets/img/avatar.jpg';
            }

            // Create token payload as a JSON string
            $payload = json_encode([
                "context"=> [
                    "user" => [
                        "avatar" => $avatar,
                        "name" => auth('web')->user()->getNameDisplay(),
                        "email" => auth('web')->user()->email,
                        "id" => "user-".auth('web')->user()->id,
                    ],
                ],
                "aud" => "jitsi",
                "iss" => $iss,
                "sub" => $sub,
                "room" => $room,
                "exp" => $expireToken
            ]);

            // Encode Header to Base64Url String
            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
            $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $visioConf['key'], true);
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
            // Create JWT
            $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

            return redirect()->to('https://'.$sub.'/'.$room.'?jwt='.$jwt);
        } else {
            $data = [
                'channel' => $channel,
            ];

            session()->flash('channelDisplayId', $channel->id);

            return view('channel.visio', $data);
        }
    }

    public function livechatMembers()
    {
        $channel = Channel::find(request()->get('channelId'));

        if ($channel->personnal == 1
            && !auth()->guard('web')->user()->directMessagesChans->contains(request()->get('channelId'))) {
            return response(view('errors.403'), 403);
        }

        if (is_numeric(request()->get('members'))) {
            $channel->live_members = $channel->live_members+request()->get('members');
            $channel->save();
        }

        broadcast(new LivechatChannel($channel->id));
    }

    private function createChannel($userId)
    {
        $channel = new Channel();
        $channel->instances_id = session('instanceId');
        $channel->users_id = auth()->guard('web')->user()->id;
        $channel->profile_id = auth()->guard('web')->user()->id;
        $channel->profile_type = User::class;
        $channel->personnal = 1;
        $channel->name = 'personnal';
        $channel->confidentiality = 1;
        $channel->free_join = 0;
        $channel->active = 1;
        $channel->save();

        $channel->users()->attach($userId, ['roles_id' => 1, 'status' => 1]);
        $channel->users()->attach(auth()->guard('web')->user()->id, ['roles_id' => 1, 'status' => 1]);

        // force recheck rights
        event(new AddProfile($userId));
        event(new AddProfile(auth()->guard('web')->user()->id, ['roles_id' => 1, 'status' => 1]));

        return $channel;
    }
}
