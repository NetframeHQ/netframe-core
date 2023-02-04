<?php

namespace App\Repository;

use App\Notif;
use App\Friends;
use App\User;
use App\Project;
use App\House;
use App\Community;
use App\Channel;
use App\Profile;
use App\Share;
use App\UsersReference;
use App\Media;
use App\TEvent;
use App\WorkflowDetailsAction;

class NotificationsRepository
{

    /**
     * Finds waiting.
     *
     * @param string $query
     *
     * @return array
     */
    public function findWaiting($limit = null, $markRead = true, $idNotif = null)
    {
        $notify = new Notif();

        if ($limit === null) {
            $limit = [0, 20];
        }


        $resultsFriends = array();

        /*
        $waiting = $notify->findWaitingNotifByUserId($limit, 'askFriend');


        foreach ($waiting as $wait) {
            $type = $wait['type'];

            $notifConfig = config('notification.type');

            switch ($type)
            {
                case 'askFriend':
                    $type = 'friends';

                    $findAttents = new Friends();
                    $attentes = $findAttents->findByProfileId(0, 0, $wait->user_from)->get();

                    $userFrom = User::find($wait->user_from);

                    $resultsFriends[] = (object) array(
                        'id'            => $wait->id,
                        'friends_id'    => $userFrom->id,
                        'userInfos'     => $userFrom->firstname.' '.$userFrom->name,
                        'notifyInfos'   => trans('notifications.ask_pending'),
                        'type'          => $notifConfig[$type]['send'],
                        'created_at'    => $wait->created_at,
                        'url'           => url()->route(
                            'notifications.results',
                            array('userInfos' => $userFrom->firstname . ' ' . $userFrom->name)
                        ),
                        'urlUser'       => \App\Helpers\StringHelper::uriHomeUserObject($userFrom),
                        'profilePicture'=> $userFrom->profileImage,
                        'read'          => $wait->read,
                        'avatarPicture' => $userFrom->getType(),
                    );
                    break;
            }
        }
        */


        if ($idNotif != null) {
            $waiting = $notify->where('id', '=', $idNotif)->get();
        } else {
            $waiting = $notify->findWaitingNotifByUserId($limit);
        }

        $results = array();

        foreach ($waiting as $wait) {
            $type = $wait['type'];

            $notifConfig = config('notification.type');

            switch ($type) {
                case 'userTaggued':
                    $userFrom = User::find($wait->user_from);
                    $parameter = json_decode($wait->parameter);
                    $postClass = $parameter->post_type;
                    $post = $postClass::find($parameter->post_id);

                    if (in_array(class_basename($post), ['Share', 'News', 'TEvent', 'Offer'])) {
                        // if news or share check if publish in channel
                        if (in_array(class_basename($post), ['Share', 'News'])
                            && class_basename($post->posts()->first()->author) == 'Channel') {
                            $notifLink = $post->posts()->first()->author->getUrlNotif();
                            $notifTxt2 = trans('notifications.hasTaggued.channel');
                        } else {
                            $notifLink = $post->getUrl();
                            $notifTxt2 = trans('notifications.hasTaggued.post');
                        }
                    } elseif (class_basename($post) == 'Comment') {
                        $parentPost = $post->post;
                        // check media
                        if (class_basename($parentPost) == 'Media') {
                            $notifLink = [
                                'href' => '#',
                                'class' => 'viewMedia',
                                'data-media-name' => $parentPost->name,
                                'data-media-id' => $parentPost->id,
                                'data-media-type' => $parentPost->type,
                                'data-media-platform' => $parentPost->platform,
                                'data-media-mime-type' => $parentPost->mime_type,
                            ];

                            if ('local' !== $parentPost->platform) {
                                $notifLink['data-media-file-name'] = $parentPost->file_name;
                            }
                            $notifTxt2 = trans('notifications.hasTaggued.mediaComment');
                        }

                        // check profile
                        $profile_types = ['Community', 'Project', 'House', 'User'];
                        if (in_array(class_basename($parentPost), $profile_types)) {
                            $notifLink = $parentPost->getUrl();
                            $notifTxt2 = trans('notifications.hasTaggued.profileComment');
                        }

                        // check publication
                        $publication_types = ['Share', 'News', 'TEvent', 'Offer', 'NetframeAction'];
                        if (in_array(class_basename($parentPost), $publication_types)) {
                            $notifLink = $parentPost->getUrl();
                            $notifTxt2 = trans('notifications.hasTaggued.postComment');
                        }
                    }

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage(
                            $userFrom->profileImage,
                            40,
                            40,
                            [],
                            $userFrom->getType(),
                            'avatar'
                        ),
                        'notifLink' => $notifLink,
                        'notifTitle' => $userFrom->getNameDisplay(),
                        'notifTxt' => trans('notifications.hasTaggued.you').' '.$notifTxt2,
                    );
                    break;

                case 'inviteHouse':
                case 'inviteProject':
                case 'inviteChannel':
                case 'inviteCommunity':
                    $type = 'inviteOn';
                    $userFrom = User::find($wait->user_from);
                    $parameter = json_decode($wait->parameter);
                    $profileClass = $parameter->profile_type;
                    $profile = $profileClass::find($parameter->profile_id);

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage(
                            $profile->profileImage,
                            40,
                            40,
                            [],
                            $profile->getType(),
                            'avatar'
                        ),
                        'notifLink' => '',
                        'notifTitle' => trans("notifications.the_" . strtolower(class_basename($profile)))
                            . ' <a href="' . $profile->getUrl() . '">' . $profile->getNameDisplay() . '</a>',
                        'notifTxt' => trans('notifications.inviteOn') . ' ' . trans('members.roles.' . $parameter->role)
                            .' '.\HTML::inviteAnswerBtn([
                                'profile_id' => $profile->id,
                                'profile_type' => $profile->getType(),
                                'users_id' => auth()->guard('web')->user()->id
                            ]),
                    );


                    break;

                case 'memberUpdate':
                    $type = 'memberUpdate';

                    $userFrom = User::find($wait->user_from);
                    $parameter = json_decode($wait->parameter);
                    $profileClass = $parameter->profile_type;
                    $profile = $profileClass::find($parameter->profile_id);

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage(
                            $profile->profileImage,
                            40,
                            40,
                            [],
                            $profile->getType(),
                            'avatar'
                        ),
                        'notifLink' => $profile->getUrl(),
                        'notifTitle' => trans("notifications.the_" . strtolower(class_basename($profile)))
                            . ' ' . $profile->getNameDisplay(),
                        'notifTxt' => trans('notifications.memberUpdate')
                            . ' ' . trans('members.roles.' . $parameter->roles_id),
                    );

                    break;

                case 'userNewReferenceByUser':
                    $type = 'user';

                    $userFrom = User::find($wait->user_from);
                    $parameter = json_decode($wait->parameter);

                    $reference = UsersReference::find($parameter->referenceId);

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage(
                            $userFrom->profileImage,
                            40,
                            40,
                            [],
                            $userFrom->getType(),
                            'avatar'
                        ),
                        'notifLink' => auth()->guard('web')->user()->getUrl(),
                        'notifTitle' => $userFrom->getNameDisplay(),
                        'notifTxt' => trans('notifications.newReferenceByUser').' : '.$reference->reference->name,
                    );

                    break;

                case 'askFriend':
                    $type = 'friends';

                    $findAttents = new Friends();
                    $attentes = $findAttents->findByProfileId(0, 0, $wait->user_from)->get();

                    $userFrom = User::find($wait->user_from);

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage(
                            $userFrom->profileImage,
                            40,
                            40,
                            [],
                            $userFrom->getType(),
                            'avatar'
                        ),
                        'notifLink' => '',
                        'notifTitle' => '<a href="'.$userFrom->getUrl().'">'.$userFrom->getNameDisplay().'</a>',
                        'notifTxt' => trans('notifications.ask_pending') . ' ' . \HTML::askedAnswerBtn([
                            'friend_id' => $userFrom->id,
                            'users_id'  => auth()->guard('web')->user()->id
                        ]),
                    );
                    break;

                case 'friendOk':
                    $findAttents = new Friends();
                    $attentes = $findAttents->findByProfileId(0, 1, $wait->user_from)->get();
                    $type = 'friends';

                    $userFrom = User::find($wait->user_from);

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage(
                            $userFrom->profileImage,
                            40,
                            40,
                            [],
                            $userFrom->getType(),
                            'avatar'
                        ),
                        'notifLink' => $userFrom->getUrl(),
                        'notifTitle' => $userFrom->getNameDisplay(),
                        'notifTxt' => trans('notifications.ask_accepted'),
                    );

                    break;

                case 'comment':
                    $type = 'comment';

                    $decode = json_decode($wait->parameter);
                    if (!is_object($decode)) {
                        continue 2;
                    }

                    $rest = $decode->text_comment;
                    $userFrom = User::find($wait->user_from);

                    $model = $decode->post_type;
                    $objectCommented = $model::find($decode->post_id);
                    if ($objectCommented != null) {
                        $notifArray = [];

                        //link to element and element name
                        switch ($model) {
                            case "App\News":
                            case "App\Share":
                            case "App\NetframeAction":
                            case "App\TEvent":
                            case "App\Offer":
                                //link to modal news with comments
                                $notifLink = url()->route('post.modal', [
                                    'id' => $objectCommented->posts()->first()->id,
                                    'modal' => 'off'
                                ]);
                                $notifyInfos2 = trans('notifications.commentModel.'.class_basename($model));
                                break;

                            case "App\Community":
                            case "App\House":
                            case "App\Project":
                            case "App\User":
                                $notifLink = $objectCommented->getUrl();
                                $notifyInfos2 = trans('notifications.commentModel.' . $model)
                                    . " " . $objectCommented->getNameDisplay();
                                $notifArray['notifRightImage'] = \HTML::thumbImage(
                                    $objectCommented->profileImage,
                                    40,
                                    40,
                                    [],
                                    $objectCommented->getType(),
                                    ''
                                );
                                break;

                            case "App\Media":
                                //link to modal player
                                if (!$objectCommented->isTypeDisplay()) {
                                    $notifLink = url()->route('media_download', array('id' => $objectCommented->id));
                                } else {
                                    $notifLink = [
                                        'href' => '#',
                                        'class' => 'viewMedia',
                                        'data-media-name' => $objectCommented->name,
                                        'data-media-id' => $objectCommented->id,
                                        'data-media-type' => $objectCommented->type,
                                        'data-media-platform' => $objectCommented->platform,
                                        'data-media-mime-type' => $objectCommented->mime_type,
                                    ];

                                    if ('local' !== $objectCommented->platform) {
                                        $notifLink['data-media-file-name'] = $objectCommented->file_name;
                                    }
                                }

                                if ($objectCommented->platform == 'local') {
                                    $notifyInfos2 = trans('notifications.media_type.local.'.$objectCommented->type);
                                } else {
                                    $notifyInfos2 = trans(
                                        'notifications.media_type.platform.' . $objectCommented->platform
                                    );
                                }
                                $notifArray['notifRightImage'] = \HTML::thumbnail($objectCommented, '', '', []);

                                break;
                        }

                        if (($model == 'App\Media' && $objectCommented->active == 1) || $model != 'App\Media') {
                            $notifArray['id'] = $wait->id;
                            $notifArray['created_at'] = $wait->created_at;
                            $notifArray['notifImg'] = \HTML::thumbImage(
                                $userFrom->profileImage,
                                40,
                                40,
                                [],
                                $userFrom->getType(),
                                'avatar'
                            );
                            $notifArray['notifLink'] = $notifLink;
                            $notifArray['notifTitle'] = $userFrom->getNameDisplay();
                            $notifArray['notifTxt'] = trans('notifications.send_comment').' '.$notifyInfos2;

                            $results[] = (object) $notifArray;
                        }
                    }

                    break;

                case 'share':
                    $type = 'post';

                    $decode = json_decode($wait->parameter);
                    $resume = $decode->postPreview;
                    $userFrom = User::find($wait->user_from);

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage(
                            $userFrom->profileImage,
                            40,
                            40,
                            [],
                            $userFrom->getType(),
                            'avatar'
                        ),
                        'notifLink' => url()->route('post.modal', ['id' => $decode->idNewsFeed, 'modal' => 'off']),
                        'notifTitle' => $userFrom->getNameDisplay(),
                        'notifTxt' => trans('notifications.share').' '.$resume,
                    );

                    break;

                case 'shareProfile':
                    $type = 'post';

                    $decode = json_decode($wait->parameter);
                    $share = Share::find($decode->idShare);
                    $profile = $share->post;
                    $userFrom = User::find($wait->user_from);

                    $notifTxt = trans('notifications.share_profile').' '.$profile->getNameDisplay();
                    /*
                    if($userFrom != $share->author){
                        $notifTxt .= ' ' . trans('notifications.onPage')
                            . ' ' . trans('netframe.' . $share->author->getType())
                            . ' ' . $share->author->getNameDisplay();
                    }
                    */

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage(
                            $userFrom->profileImage,
                            40,
                            40,
                            [],
                            $userFrom->getType(),
                            'avatar'
                        ),
                        'notifLink' => $profile->getUrl(),
                        'notifTitle' => $userFrom->getNameDisplay(),
                        'notifTxt' => $notifTxt,
                        'notifRightImage' => \HTML::thumbImage(
                            $profile->profileImage,
                            40,
                            40,
                            [],
                            $profile->getType(),
                            ''
                        ),
                    );

                    break;

                case 'shareMedia':
                    $type = 'post';

                    $decode = json_decode($wait->parameter);
                    $share = Share::find($decode->idShare);
                    $media = $share->media;
                    $userFrom = User::find($wait->user_from);

                    if ($media->active == 1) {
                        if ($media->platform == 'local') {
                            $notifyInfos2 = trans('notifications.media_type.local.'.$media->type);
                        } else {
                            $notifyInfos2 = trans('notifications.media_type.platform.'.$media->platform);
                        }

                        if (!$media->isTypeDisplay()) {
                            $notifLink = url()->route('media_download', array('id' => $media->id));
                        } else {
                            $notifLink = [
                                'href' => '#',
                                'class' => 'viewMedia',
                                'data-media-name' => $media->name,
                                'data-media-id' => $media->id,
                                'data-media-type' => $media->type,
                                'data-media-platform' => $media->platform,
                                'data-media-mime-type' => $media->mime_type,
                            ];

                            if ($media->platform !== 'local') {
                                $notifLink['data-media-file-name'] = $media->file_name;
                            }
                        }

                        $results[] = (object) array(
                            'id'            => $wait->id,
                            'created_at'    => $wait->created_at,
                            'notifImg' => \HTML::thumbImage(
                                $userFrom->profileImage,
                                40,
                                40,
                                [],
                                $userFrom->getType(),
                                'avatar'
                            ),
                            'notifLink' => $notifLink,
                            'notifTitle' => $userFrom->getNameDisplay(),
                            'notifTxt' => trans('notifications.share_media1').' '.$notifyInfos2,
                            'notifRightImage' => \HTML::thumbnail($media, '', '', []),
                        );
                    }

                    break;

                case 'joinChannel':
                    $decode = json_decode($wait->parameter);
                    $type = 'channel';

                    $joinChannel = Channel::find($decode->profile_id);
                    $userFrom = User::find($wait->user_from);

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage(
                            $userFrom->profileImage,
                            40,
                            40,
                            [],
                            $userFrom->getType(),
                            'avatar'
                        ),
                        'notifLink' => '',
                        'notifTitle' => $userFrom->getNameDisplay(),
                        'notifTxt' => trans('notifications.join_channels') . ' ' . $joinChannel->name
                            . '<br>"' . $decode->comment . '" ' . \HTML::joinAnswerBtn([
                                'profile_id' => $joinChannel->id,
                                'friend_id' => $userFrom->id,
                                'users_id' => auth()->guard('web')->user()->id,
                                'type_profile' => 'channel'
                            ], true),
                    );

                    break;

                case 'joinChannelOk':
                    $type = 'channel';

                    $decode = json_decode($wait->parameter);

                    $joinChannel = Channel::find($decode->profile_id);
                    $userFrom = User::find($wait->user_from);

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage(
                            $joinChannel->profileImage,
                            40,
                            40,
                            [],
                            $joinChannel->getType(),
                            'avatar'
                        ),
                        'notifLink' => $joinChannel->getUrl(),
                        'notifTitle' => $joinChannel->getNameDisplay(),
                        'notifTxt' => trans('notifications.join_accept'),
                    );

                    break;

                case 'joinHouse':
                    $decode = json_decode($wait->parameter);
                    $type = 'house';

                    $joinHouse = House::find($decode->profile_id);
                    $userFrom = User::find($wait->user_from);

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage(
                            $userFrom->profileImage,
                            40,
                            40,
                            [],
                            $userFrom->getType(),
                            'avatar'
                        ),
                        'notifLink' => '',
                        'notifTitle' => $userFrom->getNameDisplay(),
                        'notifTxt' => trans('notifications.join_houses') . ' ' . $joinHouse->name
                            . '<br>"' . $decode->comment . '" ' . \HTML::joinAnswerBtn([
                                'profile_id' => $joinHouse->id,
                                'friend_id' => $userFrom->id,
                                'users_id' => auth()->guard('web')->user()->id,
                                'type_profile' => 'house'
                            ]),
                    );

                    break;

                case 'joinHouseOk':
                    $type = 'house';

                    $decode = json_decode($wait->parameter);

                    $joinHouse = House::find($decode->profile_id);
                    $userFrom = User::find($wait->user_from);

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage(
                            $joinHouse->profileImage,
                            40,
                            40,
                            [],
                            $joinHouse->getType(),
                            'avatar'
                        ),
                        'notifLink' => $joinHouse->getUrl(),
                        'notifTitle' => $joinHouse->getNameDisplay(),
                        'notifTxt' => trans('notifications.join_accept'),
                    );

                    break;

                case 'joinCommunity':
                    $type = 'community';
                    $decode = json_decode($wait->parameter);

                    $joinCommunity = Community::find($decode->profile_id);
                    $userFrom = User::find($wait->user_from);

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage(
                            $userFrom->profileImage,
                            40,
                            40,
                            [],
                            $userFrom->getType(),
                            'avatar'
                        ),
                        'notifLink' => '',
                        'notifTitle' => $userFrom->getNameDisplay(),
                        'notifTxt' => trans('notifications.join_community') . ' ' . $joinCommunity->name . '<br>"'
                            . $decode->comment . '" ' . \HTML::joinAnswerBtn([
                                'profile_id' => $joinCommunity->id,
                                'friend_id' => $userFrom->id,
                                'users_id' => auth()->guard('web')->user()->id,
                                'type_profile' => 'community'
                            ]),
                    );

                    break;

                case 'joinCommunityOk':
                    $type = 'community';

                    $decode = json_decode($wait->parameter);

                    $joinCommunity = Community::find($decode->profile_id);
                    $userFrom = User::find($wait->user_from);

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage(
                            $joinCommunity->profileImage,
                            40,
                            40,
                            [],
                            $joinCommunity->getType(),
                            'avatar'
                        ),
                        'notifLink' => $joinCommunity->getUrl(),
                        'notifTitle' => $joinCommunity->getNameDisplay(),
                        'notifTxt' => trans('notifications.join_accept'),
                    );

                    break;

                case 'joinProject':
                    $type = 'project';
                    $decode = json_decode($wait->parameter);

                    $joinProject = Project::find($decode->profile_id);
                    $userFrom = User::find($wait->user_from);

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage(
                            $userFrom->profileImage,
                            40,
                            40,
                            [],
                            $userFrom->getType(),
                            'avatar'
                        ),
                        'notifLink' => '',
                        'notifTitle' => $userFrom->getNameDisplay(),
                        'notifTxt' => trans('notifications.join_project') . ' ' . $joinProject->title . '<br>"'
                            . $decode->comment . '" ' . \HTML::joinAnswerBtn([
                                'profile_id' => $joinProject->id,
                                'friend_id' => $userFrom->id,
                                'users_id' => auth()->guard('web')->user()->id,
                                'type_profile' => 'project'
                            ]),
                    );

                    break;

                case 'joinProjectOk':
                    $type = 'project';

                    $decode = json_decode($wait->parameter);

                    $joinProject = Project::find($decode->profile_id);
                    $userFrom = User::find($wait->user_from);

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage(
                            $joinProject->profileImage,
                            40,
                            40,
                            [],
                            $joinProject->getType(),
                            'avatar'
                        ),
                        'notifLink' => $joinProject->getUrl(),
                        'notifTitle' => $joinProject->getNameDisplay(),
                        'notifTxt' => trans('notifications.join_accept'),
                    );
                    break;

                case 'likeProfile':
                case 'likeContent':
                case 'followProfile':
                case 'clipMedia':
                case 'clipProfile':
                    $type2 = 'actions';

                    $decode = json_decode($wait->parameter);
                    $userFrom = User::find($wait->user_from);
                    $model = $decode->element_type;
                    $element = $model::find($decode->element_id);
                    if ($element != null) {
                        $arrayNotif = [];

                        if (($model == 'App\Media' && $element->active == 1) || $model != 'App\Media') {
                            $notifyInfos2 = '';

                            if ($type == 'likeProfile' || $type == 'followProfile') {
                                $notifLink = $element->getUrl();
                                $notifyInfos2 = $element->getNameDisplay();
                                $arrayNotif['notifRightImage'] = \HTML::thumbImage(
                                    $element->profileImage,
                                    40,
                                    40,
                                    [],
                                    $element->getType(),
                                    ''
                                );
                            } else {
                                if ($model == 'App\\Media' && $element->active == 1) {
                                    if ($element->platform == 'local') {
                                        $notifyInfos2 = trans('notifications.media_type.local.'.$element->type);
                                    } else {
                                        $notifyInfos2 = trans('notifications.media_type.platform.'.$element->platform);
                                    }
                                    $notifLink = '';

                                    if (!$element->isTypeDisplay()) {
                                        $notifLink = url()->route('media_download', array('id' => $element->id));
                                    } else {
                                        $notifLink = [
                                            'href' => '#',
                                            'class' => 'viewMedia',
                                            'data-media-name' => $element->name,
                                            'data-media-id' => $element->id,
                                            'data-media-type' => $element->type,
                                            'data-media-platform' => $element->platform,
                                            'data-media-mime-type' => $element->mime_type,
                                        ];

                                        if ('local' !== $element->platform) {
                                            $notifLink['data-media-file-name'] = $element->file_name;
                                        }
                                    }
                                    $arrayNotif['notifRightImage'] = \HTML::thumbnail($element, '', '', []);
                                } elseif ($model == 'App\\Comment') {
                                    $notifyInfos2 = trans('notifications.comment') .' : '. $element->content;
                                    $notifLink = $element->post->getUrl();
                                } elseif ($model == 'App\\UsersReference') {
                                    $notifyInfos2 = trans('notifications.likeReference')
                                        . ' : ' . $element->reference->name;
                                    $notifLink = 'ref';
                                } else {
                                    $notifyInfos2 = trans('notifications.likePost');
                                    $notifLink = url()->route('post.modal', [
                                        'id' => $element->posts()->first()->id,
                                        'modal' => 'off'
                                    ]);
                                }
                            }

                            $arrayNotif['id'] = $wait->id;
                            $arrayNotif['created_at'] = $wait->created_at;
                            $arrayNotif['notifImg'] = \HTML::thumbImage(
                                $userFrom->profileImage,
                                40,
                                40,
                                [],
                                'user',
                                'avatar'
                            );
                            $arrayNotif['notifLink'] = $notifLink;
                            $arrayNotif['notifTitle'] = $userFrom->getNameDisplay();
                            $arrayNotif['notifTxt'] = trans('notifications.'.$type).' '.$notifyInfos2;

                            $results[] = (object) $arrayNotif;
                        }
                    }

                    break;

                case "participateEvent":
                    $type = 'event';

                    $decode = json_decode($wait->parameter);

                    $event = TEvent::find($decode->event_id);
                    if ($event != null) {
                        $userFrom = User::find($wait->user_from);

                        $results[] = (object) array(
                            'id'            => $wait->id,
                            'created_at'    => $wait->created_at,
                            'notifImg' => \HTML::thumbImage($userFrom->profileImage, 40, 40, [], 'user', 'avatar'),
                            'notifLink' => $event->getUrl(),
                            'notifTitle' => $userFrom->getNameDisplay(),
                            'notifTxt' => trans('notifications.participateEvent').' '.$event->title,
                        );
                    }
                    break;

                case "has_join_community":
                case "has_join_project":
                case "has_join_house":
                    $decode = json_decode($wait->parameter);

                    $profile_type = $decode->profile_type;
                    $profileJoined = $profile_type::find($decode->profile_id);
                    if ($profileJoined != null) {
                        $userFrom = User::find($wait->user_from);

                        $results[] = (object) array(
                            'id'            => $wait->id,
                            'created_at'    => $wait->created_at,
                            'notifImg' => \HTML::thumbImage($userFrom->profileImage, 40, 40, [], 'user', 'avatar'),
                            'notifLink' => $profileJoined->getUrl(),
                            'notifTitle' => $userFrom->getNameDisplay(),
                            'notifTxt' => trans('notifications.'.$type).' '.$profileJoined->getNameDisplay(),
                            'notifRightImage' => \HTML::thumbImage(
                                $profileJoined->profileImage,
                                40,
                                40,
                                [],
                                $profileJoined->getType(),
                                ''
                            ),
                        );
                    }
                    break;

                case 'workflow':
                    // specify workflow type in json parameters
                    $decode = json_decode($wait->parameter);
                    $userFrom = User::find($wait->user_from);
                    $notifyInfos2 = '';


                    // /!\ multiple files in decode message
                    switch ($decode->workflow_type) {
                        case 'askValidateFile':
                            $media = Media::find($decode->file_id);
                            // $notifyInfos2 = $media->name;
                            $workflowAction = WorkflowDetailsAction::find($decode->workflow_action_id);
                            if ($media != null) {
                                $notifyInfos2 = \HTML::fileValidateAsk($workflowAction, $media, $wait);
                            } else {
                                $notifyInfos2 = '';
                            }
                            break;
                        case 'answerValidateFileAccept':
                            $media = Media::find($decode->file_id);
                            if ($media != null) {
                                $notifyInfos2 = $media->name;
                            } else {
                                $notifyInfos2 = '';
                            }
                            break;
                        case 'answerValidateFileDecline':
                            $media = Media::find($decode->file_id);
                            if ($media != null) {
                                $notifyInfos2 = $media->name . '<br>' . trans('notifications.workflow.reason')
                                    . ' : ' . $decode->reason;
                            } else {
                                $notifyInfos2 = '';
                            }
                            break;
                        case 'fileValidated':
                            $media = Media::find($decode->file_id);
                            if ($media != null) {
                                $notifyInfos2 = $media->name;
                            } else {
                                $notifyInfos2 = '';
                            }
                            break;
                    }

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage($userFrom->profileImage, 40, 40, [], 'user', 'avatar'),
                        'notifLink' => '',
                        'notifTitle' => $userFrom->getNameDisplay(),
                        'notifTxt' => trans('notifications.workflow.'.$decode->workflow_type).' '.$notifyInfos2,
                    );
                    break;

                case 'deleteWorkflow':
                    $decode = json_decode($wait->parameter);
                    $userFrom = User::find($wait->user_from);
                    $notifyInfos2 = $decode->file_name;

                    $results[] = (object) array(
                        'id'            => $wait->id,
                        'created_at'    => $wait->created_at,
                        'notifImg' => \HTML::thumbImage($userFrom->profileImage, 40, 40, [], 'user', 'avatar'),
                        'notifLink' => '',
                        'notifTitle' => $userFrom->getNameDisplay(),
                        'notifTxt' => trans('notifications.workflow.delete').' '.$notifyInfos2,
                    );
                    break;

                case 'assign_task':
                    $userFrom = User::find($wait->user_from);
                    $decode = json_decode($wait->parameter, true);
                    $task = \App\TaskRow::find($decode['task_id']);
                    if ($task) {
                        $params = array('project'=>$task->tables_tasks_id);
                        if (isset($task->parent)) {
                            $params['parent'] = $task->parent;
                        }
                        $results[] = (object) array(
                            'id'            => $wait->id,
                            'created_at'    => $wait->created_at,
                            'notifImg' => \HTML::thumbImage($userFrom->profileImage, 40, 40, [], 'user', 'avatar'),
                            'notifLink' => route('task.project', $params),
                            'notifTitle' => $userFrom->getNameDisplay(),
                            'notifTxt' => trans('notifications.task.assign') . ' ' . $task->name
                                . ' ' . trans('notifications.task.from') . ' ' . $task->project->name,
                        );
                    } else {
                        //delete on task doesn't exist anymore
                        $wait->delete();
                    }

                    break;

                case 'add_collab':
                    $userFrom = User::find($wait->user_from);
                    $decode = json_decode($wait->parameter, true);
                    $doc = \App\ColabDoc::find($decode['collab_doc']);
                    if ($doc) {
                        $results[] = (object) array(
                            'id'            => $wait->id,
                            'created_at'    => $wait->created_at,
                            'notifImg' => \HTML::thumbImage($userFrom->profileImage, 40, 40, [], 'user', 'avatar'),
                            'notifLink' => "/collab/".$doc->id,
                            'notifTitle' => $userFrom->getNameDisplay(),
                            'notifTxt' => trans('notifications.collab.add').' '.$doc->name,
                        );
                    } else {
                        //delete on task doesn't exist anymore
                        $wait->delete();
                    }

                    break;

                default:
                    break;
            }

            if ($wait->read == 0 && $markRead && $wait->type != 'askFriend') {
                $wait->read = 1;
                $wait->save();
            }
        }

        return array_merge($resultsFriends, $results);
    }

    /**
     *
     * @todo optimiser en 2 requetes une pour friends_id et une pour user_id avec jointure user pour
     */
    public function listFriends()
    {
        $friends = Friends::where('blacklist', '=', 0)
            ->where('instances_id', '=', session('instanceId'))
            ->where('status', '=', 1)
            ->where(function ($w) {
                $w->orWhere('friends_id', '=', auth()->guard('web')->user()->id)
                ->orWhere('users_id', '=', auth()->guard('web')->user()->id);
            })
            ->orderBy('friends.created_at', 'desc')
            ->get();

        $results = array();
        foreach ($friends as $friend) {
            $friendId = auth()->guard('web')->user()->id == $friend->friends_id
                ? $friend->users_id
                : $friend->friends_id;
            $userFriend = User::find($friendId);

            if ($userFriend->active == 1) {
                $results[] = (object) $userFriend;
            }
        }

        return $results;
    }

    /**
     * Finds friends.
     *
     * @param string $query
     *
     * @return array
     */
    public function findFriends($authorType = '', $authorId = 0)
    {
        $friends = Friends::where('blacklist', '=', 0)
        ->where('status', '=', 1)
        ->where(function ($w) {
            $w->orWhere('friends_id', '=', auth()->guard('web')->user()->id)
            ->orWhere('users_id', '=', auth()->guard('web')->user()->id);
        })
        ->orderBy('friends.created_at', 'desc')
        ->get();

        $notify = new Notif();

        $results = array();

        $notifyProjectCommunity = null;

        foreach ($friends as $friend) {
            $friendId = (auth()->guard('web')->user()->id == $friend->friends_id)
                ? $friend->users_id
                : $friend->friends_id;

            $notifyProjectCommunity = $notify
                ->findByAthorId($friendId, auth()->guard('web')->user()->id, 'projectCommunity')
                ->first();

            $userFriend = User::find($friendId);

            if ($authorId != 0) {
                $follower = $userFriend->follow($authorType, $authorId);
            } else {
                $follower = 0;
            }

            $results[$friend->id] = (object) array(
                'id' => $friend->id,
                'friends_id' => $userFriend->id,
                'userInfos' => $userFriend->firstname.' '.$userFriend->name,
                'type' => trans('friends.friend'),
                'sended' => $notifyProjectCommunity,
                'created_at' => $friend->created_at,
                'url'           => url()->route('notifications.results', array('userInfos' => $userFriend->name)),
                'urlUser'       => \App\Helpers\StringHelper::uriHomeUserObject($userFriend),
                'profilePicture'=> $userFriend->profileImage,
                'follower'      => $follower
            );
        }

        return $results;
    }
}
