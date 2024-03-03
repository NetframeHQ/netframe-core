<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Repository\SearchRepository2;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Events\NewPost;
use App\Events\PostAction;
use App\Events\DeletePost;
use App\Events\DeleteComment;
use App\Helpers\SessionHelper;
use App\NewsFeed;
use App\Project;
use App\Community;
use App\House;
use App\Media;
use App\TEvent;
use App\News;
use App\Offer;
use App\Profile;
use App\Comment;
use App\Like;
use App\Subscription;
use App\Share;
use App\Notif;
use App\Netframe;
use App\ReportAbuse;
use App\Events\LikeElement;
use App\Events\NewComment;
use App\Events\NewAction;
use App\Events\SocialAction;
use App\Events\InterestAction;
use App\Events\SubscribeToProfile;
use App\NetframeAction;
use Illuminate\Http\Request;
use App\Events\CheckUserTag;
use App\Instance;
use App\Application;
use App\Emoji;
use App\Repository\NotificationsRepository;

/*
use Predis\Session\SessionHandler;
use AppRepository\TypePost;
use Codeception\PhpUnit\ResultPrinter\Report;
*/

/**
 *
 *
 *  Controller for Global Ajax processing in application
 *
 */
class NetframeController extends BaseController
{

    public function __construct(SearchRepository2 $searchRepository)
    {
        $this->middleware('checkAuth');
        parent::__construct();

        $this->searchRepository = $searchRepository;
    }

    public function missingMethod($parameters = array())
    {
        return response(view('errors.404'), 404);
    }

    public function postSidebarToggle()
    {
        $sidebarState = request()->get('sidebarstate');
        auth()->guard('web')->user()->setParameter('sidebarstate', $sidebarState);
    }

    public function postSetGeolocation()
    {
        if (request()->has('stop')) {
            session()->forget('geoip');
        } else {
            $latitude = request()->get('latitude');
            $longitude = request()->get('longitude');
            $geoip = new \stdClass();
            $geoip->lat = $latitude;
            $geoip->lng = $longitude;
            $geoloc = array('latitude' => $latitude, 'longitude' => $longitude);
            session(['geoip' => $geoloc]);
        }
    }

    public function postNavigateProfile()
    {
        if (!$this->Acl->getRights(request()->get('profile'), request()->get('id'))) {
            return response(view('errors.403'), 403);
        }

        // format & secure string value receipt in ajax


        $profile = User::find(request()->get('id'));
        $profileDisplayName = $profile->getNameDisplay();


        /*
        $profileType = htmlspecialchars(strval(request()->get('profile')));
        // get id profile
        $profileId = intval(request()->get('id'));
        $profileName = strval(request()->get('name'));

        $modelProfile = studly_case($profileType);
        $dataProfile = $referenceProfile = $modelProfile::where('id', '=', $profileId)->firstOrFail();

        // for change session Navigation
        SessionHelper::setProfile('current', $dataProfile, $profileType);
        SessionHelper::setProfile('as', $dataProfile, $profileType);

        // for sendback data json
        $dataProfile = (object) $dataProfile->toArray();
        $dataProfile->profile = $profileType;
        if($referenceProfile->profileImage != null){
            $dataProfile->profileImage = $referenceProfile->profileImage->id;
        }
        else{
            $dataProfile->profileImage = 'null';
        }
        if($profileType == 'user')
        {
            $dataProfile->profileName = $dataProfile->name.' '.$dataProfile->firstname;
        } else {
            $dataProfile->profileName = $profileName;
        }
        */

        SessionHelper::setProfile('current', $profile, request()->get('profile'));
        SessionHelper::setProfile('as', $profile, request()->get('profile'));

        if ($profile->profileImage != null) {
            $profile->profileImage = $profile->profileImage->id;
        } else {
            $profile->profileImage = 'null';
        }
        $profile->profileName = $profileDisplayName;
        $profile->url = $profile->getUrl();
        $profile->profileType = $profile->getType();

        return response()->json($profile);
    }


    /**
     * Delete Article from page
     *
     * @param unknown $id
     * @return JSON Response \Illuminate\Http\JsonResponse
     */
    public function getDeletePublish($id)
    {

        $posts = NewsFeed::find($id);
        if ($posts != null) {
            $post = $posts->post;

            if (!BaseController::hasRights($post) && !BaseController::hasRights($posts)) {
                return response(view('errors.403'), 403);
            }

            // Run event delete data in newsfeed
            event(new DeletePost($posts));

            $posts->delete();

            return response()->json(array(
                    'delete' => true,
                    'targetId' => '#' . class_basename($posts->post)
                        . '-' . class_basename($posts->author)
                        . '-' . $posts->post_id
            ));
        }
    }

    /**
     *  Get Formular Comment for Article or Edit Article
     *
     *  @param string $typePost ex: news, events...
     *  @param int $id
     *
     *  @return HTML View netframe/form-comment.blade.php
     */
    public function getFormCommentPublish($typeElement, $idElement, $replyTo = null)
    {
        $data = [];

        if ($typeElement == 'newsfeed') {
            $newsfeed = NewsFeed::find($idElement);

            if ($newsfeed->post->disable_comments != 0) {
                return response(view('errors.403'), 403);
            }
            $data['post'] = $newsfeed->post;
            $data['profile'] = $newsfeed->author;

            //check post author profile
            $author = $newsfeed->post->author;

            if ($newsfeed->instances_id != session('instanceId')
                || ($author->confidentiality == 0 && !BaseController::hasViewProfile($author))) {
                return response(view('errors.403'), 403);
            }
        } elseif ($typeElement == 'media') {
            $media = Media::find($idElement);
            $author = $media->author()->first();
            $data['post'] = $media;
            $data['profile'] = $author;

            if ($media->instances_id != session('instanceId')
                || (
                    class_basename($author) != 'User'
                    && $author->confidentiality == 0
                    && !BaseController::hasViewProfile($author)
                )
            ) {
                return response(view('errors.403'), 403);
            }
        }




        if ($replyTo!=null) {
            $data['replyTo'] = $replyTo;
        }

        return view('netframe.form-comment', $data);
    }

    /**
     *  POST or EDIT Data for Comment
     *
     *  @return JSON response
     */
    public function postCommentPublish()
    {
        $data = array();

        if (request()->ajax()) {
            $jsonData = array();

            $post_type = request()->get('post_type');
            $post = $post_type::find(request()->get('post_id'));

            if (isset($post->disable_comments) && $post->disable_comments != 0) {
                return response(view('errors.403'), 403);
            }

            $data['post'] = $post;

            // Call dynamically model for get Profile data
            $data['profile'] = Profile::gather(request()->get('author_type'))->find(request()->get('author_id'));
            $validator = validator(request()->all(), config('validation.page/commentPost'));

            // check instance and confidentiality of commented element
            if (in_array(request()->get('post_type'), ['App\Project', 'App\House', 'App\Community'])) {
                $profileClass = "\\".request()->get('post_type');
                $profileCommented = $profileClass::find(request()->get('post_id'));
                if ($profileCommented->instances_id != session('instanceId')
                    || ($profileCommented->confidentiality == 0
                        && !BaseController::hasViewProfile($profileCommented)
                    )) {
                    return response(view('errors.403'), 403);
                }
            } elseif (in_array(request()->get('post_type'), [
                'App\Share',
                'App\Offer',
                'App\News',
                'App\TEvent',
                'App\NetframeAction'
            ])) {
                //check post author profile
                $postClass = "\\".request()->get('post_type');
                $postCommented = $postClass::find(request()->get('post_id'));
                $author = $postCommented->author;

                if ($postCommented->instances_id != session('instanceId')
                    || ($author->confidentiality == 0 && !BaseController::hasViewProfile($author))) {
                    return response(view('errors.403'), 403);
                }
            }

            // if an INSERT or an EDIT
            if (request()->has('comment_id')) {
                $comment = Comment::find(request()->get('comment_id'));
                if (!BaseController::hasRights($comment)) {
                    return response(view('errors.403'), 403);
                }
                // send json data edit for action javascript
                $jsonData['edit'] = 'true';
            } else {
                $comment = new Comment();
                $comment->like = 0;
                if (!$this->Acl->getRights(request()->get('author_type'), request()->get('author_id'), 5)
                    || $post->instances_id != session('instanceId')) {
                    return response(view('errors.403'), 403);
                }
                $jsonData['edit'] = 'false';
            }

            if ($validator->fails()) {
                $data['errors'] = $validator->messages();
                $data['inputOld'] = request()->all();
                 return response()->json([
                            'view' => view('netframe.form-comment', $data)->render(),
                 ]);
            } else {
                $comment->instances_id = session('instanceId');
                $comment->content = htmlentities(request()->get('content'));
                $comment->users_id = auth()->guard('web')->user()->id;
                $comment->author_id = request()->get('author_id');
                $comment->author_type = "App\\".studly_case(request()->get('author_type'));
                $comment->post_id = request()->get('post_id');
                $comment->post_type = studly_case(request()->get('post_type'));
                if (request()->has('reply_to')) {
                    $replyTo = Comment::find(request()->get('reply_to'));
                    if ($replyTo && $replyTo->post_id==$comment->post_id) {
                        $comment->comments_id = $replyTo->id;
                        $comment->level = ($replyTo->level<=3) ? $replyTo->level+1 : $replyTo->level;
                    } else {
                        return response(view('errors.403'), 403);
                    }
                }
                $comment->save();

                event(new NewComment($comment));

                event(new CheckUserTag($comment, $comment->content));

                //insert interest
                if ($comment->post->tags != null) {
                    $interestElement = config('interests.equivalence.'.class_basename($comment->post));
                    event(new InterestAction(
                        auth()->guard('web')->user(),
                        $comment->post->tags,
                        $interestElement . '.comment'
                    ));
                }

                /*
                if( !in_array(request()->get('post_type'), config('netframe.model_commentable')) ){
                    event(new PostAction(request()->get('post_type'), request()->get('post_id')));
                }
                */

                $data['comment'] = $comment;
                /* ---------- JSON DATA SEND TO VIEW ------------------ */
                $jsonData['view'] = view('netframe.form-comment', $data)->render();
                $jsonData['viewComment'] = $this->getViewCommentAuthor($comment);

                // Generate id for div comment
                // If an EDIT COMMENT
                if ($jsonData['edit'] == 'true') {
                    // Build Id Comment div for find comment div id return json
                    $postType = class_basename($comment->post);
                    if ($postType == 'App\Media') {
                        $postType = 'Media';
                    }
                    $jsonData['targetId'] = "#comment-{$comment->id}-{$postType}-{$comment->post_id}";
                } else {
                    // If an INSERT COMMENT Generate ID post for find div id
                    //$authorType = get_class($comment->post->author);
                    $postType = ($comment->post_type == 'App\Media') ? 'Media' : $comment->post_type;
                    if (in_array(request()->get('post_type'), config('netframe.model_commentable'))) {
                        $jsonData['targetId'] = "#comments-{$postType}-{$comment->post_id}";
                    } else {
                        //$postAuthor = (class_basename($comment->post) == 'NetframeAction')
                        //    ? $comment->post->posts->first()->author_type
                        //    : $comment->post->author_type;
                        $check_basename = ['NetframeAction', 'News', 'Offer', 'TEvent', 'Share'];
                        if (in_array(class_basename($comment->post), $check_basename)) {
                            $postAuthor = $comment->post->posts->first()->author_type;
                        } else {
                            $postAuthor = $comment->post->author_type;
                        }
                        $jsonData['targetId'] = "#" . class_basename($postType)
                            . "-" . class_basename($postAuthor)
                            . "-" . $comment->post_id;
                    }
                }

                return response()->json($jsonData);
            }
        }
    }

    /**
     * Get View Comment Author in ajax after to have posted comment
     *
     * @param unknown $commentId
     * @return HTML View page/comment.blade.php
     */
    public function getViewCommentAuthor($comment)
    {
        $data = array();
        $data['comment'] = $comment;
        $data['post'] = $comment->post;
        return view('page.comment', $data)->render();
    }

    /**
     * Get view form edit Comment
     *
     * @param unknown $id
     * @return HTML View netframe/form-comment.blade.php
     */
    public function getEditComment($id)
    {
        $data = array();
        $data['mod'] = "edit";

        $comment = Comment::find($id);
        if (!BaseController::hasRights($comment)) {
            return response(view('errors.403'), 403);
        }

        $data['post'] = $comment->post;
        $data['comment'] = $comment;
        $data['profile'] = $comment->author;

        return view('netframe.form-comment', $data);
    }

    /**
     *
     * @param int $id Comments
     * @return JsonResponse
     */
    public function getDeleteComment($id)
    {
        $comment = Comment::find($id);
        if ((BaseController::hasRights($comment) && BaseController::hasRights($comment) < 3)
            || (
                BaseController::hasRightsProfile($comment->post)
                && BaseController::hasRightsProfile($comment->post) < 3
            )
            || (
                class_basename($comment->post) == 'Media'
                && BaseController::hasRightsProfile($comment->post->author->first())
                && BaseController::hasRightsProfile($comment->post->author->first()) < 3
            )
            || (
                BaseController::hasRights($comment->post->newsfeedRef)
                && BaseController::hasRights($comment->post->newsfeedRef) < 4
            )
            ) {
            // Run event delete data in newsfeed
            event(new DeleteComment($comment));

            $comment->delete();

            $postType = class_basename($comment->post_type);

            return response()->json(array(
                'delete' => true,
                'targetId' => "#comment-{$comment->id}-{$postType}-{$comment->post_id}"
            ));
        } else {
            return response(view('errors.403'), 403);
        }
    }

    /**
     *  Get Formular Comment for profile
     *
     *  @param string $profileType ex: user, house...
     *  @param int $id profile
     *
     */
    public function getFormCommentProfile($profileType, $profileId)
    {
        $data = array();

        $profile = Profile::gather($profileType)->find($profileId);

        if ($profile->instances_id != session('instanceId')
            || ($profile->confidentiality == 0 && !BaseController::hasViewProfile($profile))) {
            return response(view('errors.403'), 403);
        }

        $data['profileCommented'] = $profile;
        $data['post'] = $profile;

        return view('netframe.form-comment', $data);
    }

    /**
     *  Get Form Comment for media
     *
     *  @param int $id comment
     *
     */
    public function getFormCommentMedia($mediaId)
    {
        $data = array();

        $media = \App\Media::find($mediaId);

        if ($media->instances_id != session('instanceId')) {
            return response(view('errors.403'), 403);
        }

        $data['post'] = $media;

        return view('netframe.form-comment', $data);
    }

    /**
     *
     * @param string
     * @return JsonResponse
     */
    public function postLike()
    {
        $dataJson = array();
        // formate string to json and decode json to array
        $data = request()->get('postData');

        $like = new Like();

        $likedElement = $data['liked_type']::find($data['liked_id']);

        $like->instances_id = session('instanceId');
        $like->users_id = auth()->guard('web')->user()->id;
        $like->liker_id = auth()->guard('web')->user()->id;
        $like->liker_type = "App\\User";
        $like->liked_id = $data['liked_id'];
        $like->liked_type = $data['liked_type'];
        if (isset($data['emojis_id'])) {
            $like->emojis_id = $data['emojis_id'];
        }

        if (!$this->Acl->getRights(strtolower($data['liker_type']), $data['liker_id'])
            || $likedElement->instances_id != session('instanceId')) {
            return response(view('errors.403'), 403);
        }

        if (in_array($data['liked_type'], ['App\Share', 'App\Offer', 'App\News', 'App\TEvent', 'App\NetframeAction'])) {
            $author = $likedElement->posts[0]->author;

            if ($likedElement->instances_id != session('instanceId')
                || ($author->confidentiality == 0 && !BaseController::hasViewProfile($author))) {
                return response(view('errors.403'), 403);
            }
        }

        // Check if user Liked already or not
        if ($like->likeExist($like->users_id, $like->liker_id, $like->liker_type, $like->liked_id, $like->liked_type)) {
            $like = Like::where('users_id', '=', auth()->guard('web')->user()->id)
                        ->where('instances_id', '=', session('instanceId'))
                        ->where('liker_id', '=', auth()->guard('web')->user()->id)
                        ->where('liker_type', '=', "App\\User")
                        ->where('liked_id', '=', $data['liked_id'])
                        ->where('liked_type', '=', $data['liked_type']);

            $ll = $like->first();
            // Select another emoji
            if (isset($data['emojis_id']) && $ll->emojis_id != $data['emojis_id']) {
                $ll->emojis_id = $data['emojis_id'];
                $ll->update();
                $dataJson['increment'] = 0;
                $dataJson['likeThis'] = true;
                $emoji = $ll->emoji;
                if ($emoji) {
                    $dataJson['view'] = view('macros.like', ['emoji'=>$emoji->value])->render();
                }
            } else {
                // UNLIKED
                $foo = $like->delete();


                // Propagate Like decrement on other table type_foreign
                event(new LikeElement($data['liked_type'], $data, 'decrement'));
                $dataJson['increment'] = -1;
                $dataJson['view'] = view('macros.like')->render();
            }

            // remove netframe action
        } else {
            // LIKE THIS
            $like->save();

            // Propagate Like increment on other table type_foreign
            event(new LikeElement($data['liked_type'], $data, 'increment'));
            /*
            if(isset($data['idNewsFeeds']) && $data['idNewsFeeds'] != ''){
                Event::dispatch('newsfeed.updateAction', [$data['liked_type'], $data['liked_id']]);
                event(new PostAction($data['liked_type'], $data['liked_id']));
            }
            */
            $dataJson['increment'] = 1;
            $dataJson['likeThis'] = true;
            $emoji = $like->emoji;
            if ($emoji) {
                $dataJson['view'] = view('macros.like', ['emoji'=>$emoji->value])->render();
            }


            // insert netframe action
            //event(new NewAction(
            //    'like_post',
            //    $data['liked_id'],
            //    $data['liked_type'],
            //    auth()->guard('web')->user()->id,
            //    'user'
            //));

            //insert interest
            if ($like->liked->tags != null) {
                $interestElement = config('interests.equivalence.'.class_basename($like->liked));
                event(new InterestAction(auth()->guard('web')->user(), $like->liked->tags, $interestElement.'.like'));
            }

            //insert notification
            $post = $like->liked;
            event(new SocialAction($post->users_id, $post->id, get_class($post), 'likeContent'));
        }

        $ids = json_decode(\App\Instance::find(session('instanceId'))->getParameter('like_buttons'), true);
        if ($ids==null) {
            $ids = config('instances.defaultEmojis');
        }
        $likes = \App\Emoji::join('likes', function ($joinS) {
            $joinS->on('likes.emojis_id', '=', 'emojis.id');
        })
        ->where('liked_id', $data['liked_id'])
        ->where('liked_type', '=', $data['liked_type'])
        ->groupBy('emojis_id')
        ->orderBy(\DB::raw('total', 'DESC'))
        ->orderBy('created_at', 'ASC')
        ->select('emojis.*', \DB::raw('count(*) as total'))
        ->findMany($ids);

        $dataJson['reacts'] = view('macros.like-reactions', [
            'liked_id' => $data['liked_id'],
            'liked_type' => str_replace('App\\', '', $data['liked_type']),
            'likes' => $likes
        ])->render();

        if (count($likes)>0) {
            $dataJson['test'] = $data['liked_id'].$data['liked_type'];
        }

        return response()->json($dataJson);
    }


    /*
     * Like on profile
     *
     */
    public function postLikeProfile()
    {
        $dataJson = array();
        // formate string to json and decode json to array
        $data = request()->get('postData');

        $likeProfile = new Like();
        $profile = auth()->guard('web')->user();

        $profileLiked = Profile::gather($data['profile_type'])->find($data['profile_id']);

        if ((($data['profile_type'] == 'User' && !$profileLiked->instances->contains(session('instanceId')))
                || ($data['profile_type'] != 'User' && $profileLiked->instances_id != session('instanceId')
            )) || ($profileLiked->confidentiality == 0 && !BaseController::hasViewProfile($profileLiked))) {
            return response(view('errors.403'), 403);
        }

        $likeExist = Like::existing($data['profile_id'], "App\\".$data['profile_type']);

        if ($likeExist) {
            $dataJson['unlike'] = true;
            $dataJson['increment'] = -1;
            // Delete Record like profile
            $likeExist->delete();
            event(new LikeElement($data['profile_type'], [ 'liked_id' => $data['profile_id'] ], $data, 'decrement'));
        } else {
            $dataJson['liked'] = true;
            $dataJson['increment'] = 1;
            // Insert Record like profile
            $likeIt = new Like();
            $likeIt->users_id = auth()->guard('web')->user()->id;
            $likeIt->instances_id = session('instanceId');
            $likeIt->liker_id = $profile->id;
            $likeIt->liker_type = "App\\".class_basename($profile);
            $likeIt->liked_id = $data['profile_id'];
            $likeIt->liked_type = "App\\".$data['profile_type'];

            $likeIt->save();

            // Propagate Like increment on other table type_foreign
            event(new LikeElement($data['profile_type'], [ 'liked_id' => $data['profile_id'] ], 'increment'));

            //retrive associate profile
            $profile = $likeIt->liked;
            if (get_class($profile) == 'App\\User') {
                $profile->users_id = $profile->id;
            }

            // Check if subscription exist, if not, we insert record subscription
            if (!Subscription::existing($data['profile_id'], $data['profile_type'])) {
                event(new SubscribeToProfile(
                    auth()->guard('web')->user()->id,
                    $data['profile_id'],
                    $data['profile_type']
                ));
                $dataJson['subscrib'] = true;
            } else {
                //add netframe action
                event(new NewAction(
                    'like',
                    $data['profile_id'],
                    $data['profile_type'],
                    auth()->guard('web')->user()->id,
                    'user'
                ));

                //insert notification
                event(new SocialAction($profile->users_id, $profile->id, get_class($profile), 'likeProfile'));
            }
        }

        return response()->json($dataJson);
    }

    /**
     *
     * @param string
     * @return JsonResponse
     */
    public function postPintop()
    {
        $dataJson = array();
        // formate string to json and decode json to array
        $data = request()->get('postData');

        $pintop = new NewsFeed();

        $post = $data['type_post']::find($data['id_post']);
        if (!$this->Acl->getRights(strtolower($post->posts[0]->author_type), strtolower($post->posts[0]->author_id))) {
            return response(view('errors.403'), 403);
        }

        //get target pin top post
        $newsFeed = NewsFeed::where('instances_id', '=', session('instanceId'))
            ->where('author_id', '=', $data['id_foreign'])
            ->where('author_type', '=', $data['type_foreign'])
            ->where('post_id', '=', $data['id_post'])
            ->where('post_type', '=', $data['type_post'])
            ->first();
        // prepare new state before reset all pin of profile posts
        $newsFeed->pintop = ($newsFeed->pintop == 1) ? 0 : 1;

        //delete existing pintop for this profile
        if ($pintop->pinTopExist($data['id_foreign'], $data['type_foreign'])) {
            NewsFeed::where('pintop', '=', 1)
                ->where('instances_id', '=', session('instanceId'))
                ->where('author_id', '=', $data['id_foreign'])
                ->where('author_type', '=', $data['type_foreign'])
                ->update(["pintop" => "0"]);
        }

        // save pin state
        $newsFeed->save();

        $dataJson['pinTop'] = true;
        return response()->json($dataJson);
    }

    /*
     *  Subscription on Profile
     *
     */
    public function postSubscribProfile()
    {
        $dataJson = array();
        $data = request()->get('postData');

        $elementClass = $data['profile_type'];
        if (!mb_ereg('App', $elementClass)) {
            $elementClass = 'App\\'.$elementClass;
        }

        $subscribedElement = $elementClass::find($data['profile_id']);

        if (class_basename($subscribedElement) == 'User'
            && $subscribedElement->instances->contains(session('instanceId'))) {
            $subscribedInstance = session('instanceId');
        } else {
            $subscribedInstance = $subscribedElement->instances_id;
        }

        // Security exit script
        if (!in_array(class_basename($subscribedElement), config('netframe.list_profile_follow'))
            || $subscribedInstance != session('instanceId')
            || (in_array(class_basename($subscribedElement), ['House', 'Community', 'Project', 'User'])
                && $subscribedElement->confidentiality == 0
                && !$this->Acl->getRights(strtolower(class_basename($subscribedElement)), $data['profile_id'], 5) )) {
            return response(view('errors.403'), 403);
        }

        $subscrib = new Subscription();
        $subscribExist = Subscription::existing($data['profile_id'], class_basename($subscribedElement));

        if ($subscribExist) {
            $dataJson['unsubscrib'] = true;
            $subscribExist->delete();
        } else {
            event(new SubscribeToProfile(auth()->guard('web')->user()->id, $data['profile_id'], $data['profile_type']));
            $dataJson['subscrib'] = true;
        }

        return response()->json($dataJson);
    }

    /**
     *  Return json format array for mapbox
     */
    public function getBigMapJson()
    {
        $profilesLocation = Netframe::getFluxMapbox();

        return response()->json($profilesLocation);
    }

    /**
     * Get Form HTML for edition user description
     *
     * @var int $id
     * @return string content HTML
     */
    public function getFormDescriptionUser($id)
    {
        // Variable $data for storage variable send to template
        $data = array();

        //if (!in_array($pageType, config('netframe.list_profile'))) {
        if (auth()->guard('web')->user()->id != $id) {
            //exit();
            return response(view('errors.403'), 403);
        }

        $data['user'] = User::find($id);

        return view('user.form-description', $data);
    }

    /**
     * Form send for update user description
     */
    public function postPublishDescriptionUser()
    {
        $data = array();

        // Variable needed for display page
        $data['user'] = $user = User::find(request()->get('id_user'));

        if (auth()->guard('web')->user()->id != request()->get('id_user')) {
            return response(view('errors.403'), 403);
        }

        $validator = validator(request()->all(), config('validation.user/description'));

        if ($validator->fails()) {
            // send back error message for validate form
            $data['errors'] = $validator->messages();
            $data['inputOld'] = request()->all();

            return response()->json(array(
                'view' => view('user.form-description', $data)->render(),
            ));
        } else {
            // Success post desctription
            $user->description = htmlentities(request()->get('description'));
            $user->save();
            event(new \App\Events\UserUpdatedEvent($user));

            return response()->json(array(
                'view' => view('user.form-description', $data)->render(),
                'redirect' => request()->get('httpReferer')
            ));
        }
    }

    /**
     * Get Form HTML for edition user description
     *
     * @var int $id
     * @return string content HTML
     */
    public function getFormTrainingUser($id)
    {
        // Variable $data for storage variable send to template
        $data = array();

        //if (!in_array($pageType, config('netframe.list_profile'))) {
        if (auth()->guard('web')->user()->id != $id) {
            //exit();
            return response(view('errors.403'), 403);
        }

        $data['user'] = User::find($id);

        return view('user.form-training', $data);
    }

    /**
     * Form send for update user description
     */
    public function postPublishTrainingUser()
    {
        $data = array();

        // Variable needed for display page
        $data['user'] = $user = User::find(request()->get('id_user'));

        if (auth()->guard('web')->user()->id != request()->get('id_user')) {
            return response(view('errors.403'), 403);
        }

        $validator = validator(request()->all(), config('validation.user/training'));

        if ($validator->fails()) {
            // send back error message for validate form
            $data['errors'] = $validator->messages();
            $data['inputOld'] = request()->all();

            return response()->json(array(
                'view' => view('user.form-training', $data)->render(),
            ));
        } else {
            // Success post desctription
            $user->training = htmlentities(request()->get('training'));
            $user->save();
            event(new \App\Events\UserUpdatedEvent($user));

            return response()->json(array(
                'view' => view('user.form-training', $data)->render(),
                'redirect' => request()->get('httpReferer')
            ));
        }
    }


    /**
     * Get Form HTML for share element
     *
     * @var int $idNewsFeed
     */
    public function getFormShare($idNewsFeed, $id = null)
    {

        $newsFeed = NewsFeed::findOrFail($idNewsFeed);
        $view = 'netframe.form-share';

        //check post author profile
        $author = $newsFeed->post->author;

        if ($newsFeed->instances_id != session('instanceId')
            || ($author->confidentiality == 0 && !BaseController::hasViewProfile($author))) {
            return view('errors.403');
            //return response(view('errors.403'), 403);
        }

        // Variable $data for storage variable send to template
        $data = array();

        if ($id != null) {
            $share = Share::findOrFail($id);

            if ($share->instances_id != session('instanceId')
                || !$this->Acl->getRights($share->author_type, $share->author_id)) {
                return response(view('errors.403'), 403);
            }

            $share->default_author = new \stdClass();
            $share->default_author->author_id = $share->posts()->first()->author->id;
            $share->default_author->author_type = strtolower(class_basename($share->posts()->first()->author));
            $share->true_author = new \stdClass();
            $share->true_author->author_id = $share->posts()->first()->true_author->id;
            $share->true_author->author_type = strtolower(class_basename($share->posts()->first()->true_author));

            $data['share'] = $share;
            $data['shareContent'] = $share->content;
            $data['shareId'] = $share->id;
            $data['edit'] = 1;

            //check if share is profile
            if (in_array(class_basename($share->post), config('netframe.shareProfilesTypes'))) {
                $data['profile'] = $share->post;
                $view = 'netframe.form-share-profile';
            } else {
                $data['post'] = $share->post->posts()->first();
            }
        } else {
            $share = new Share();
            $share->default_author = new \stdClass();
            $share->default_author->author_id = auth()->guard('web')->user()->id;
            $share->default_author->author_type = 'user';
            $share->true_author = new \stdClass();
            $share->true_author->author_id = auth()->guard('web')->user()->id;
            $share->true_author->author_type = 'user';

            $data['share'] = $share;
            $data['shareContent'] = '';
            $data['shareId'] = 0;
            $data['edit'] = 0;

            $data['post'] = NewsFeed::where('id', '=', $idNewsFeed)->get()->first();
        }

        if (isset($data['post']) && count($data['post']->post->medias) > 0) {
            $data['medias'] = $data['post']->post->medias;
        } else {
            $data['medias'] = [];
        }

        return view($view, $data);
    }

    /**
     * form to insert a share
     */
    public function postPublishShare()
    {
        if (!$this->Acl->getRights(request()->get('author_type'), request()->get('author_id'), 4)
            || !$this->Acl->getRights(request()->get('true_author_type'), request()->get('true_author_id'), 4)) {
            return response(view('errors.403'), 403);
        }

        $data = array();
        $share = Share::findOrNew(request()->get('id'));

        $validator = validator(request()->all(), config('validation.share'));

        # send back variable using in view
        $newsfeed = NewsFeed::findOrFail(request()->get('id_newsfeed'));

        //check post author profile
        $author = $newsfeed->post->author;

        if ($newsfeed->instances_id != session('instanceId')
            || ($author->confidentiality == 0
            && !BaseController::hasViewProfile($author))) {
            return response(view('errors.403'), 403);
        }

        $data['post'] = $newsfeed;
        $data['edit'] = request()->get('edit');

        if (in_array(class_basename($newsfeed->post), config('netframe.sharePostsTypes'))) {
            if (count($data['post']->post->medias) > 0) {
                $data['medias'] = $data['post']->post->medias;
            } else {
                $data['medias'] = [];
            }

            if ($validator->fails()) {
                $data['errors'] = $validator->messages();
                $data['inputOld'] = request()->all();

                return response()->json(array(
                    'view' => view('netframe.form-share', $data)->render(),
                ));
            } elseif (request()->get('edit') == 1) {
                $share = Share::findOrFail(request()->get('id_share'));

                if ($share->instances_id != session('instanceId')
                    || !$this->Acl->getRights($share->author_type, $share->author_id)) {
                    return response(view('errors.403'), 403);
                }

                $share->content = request()->get('content');
                $share->author_id = request()->get('true_author_id');
                $share->author_type = "App\\".studly_case(request()->get('true_author_type'));
                $share->save();

                // modify authors fields for newsfeed insert
                $share->confidentiality = 1;
                $share->author_id = request()->get('author_id');
                $share->author_type = "App\\".studly_case(request()->get('author_type'));
                $share->true_author_id = request()->get('true_author_id');
                $share->true_author_type = "App\\".studly_case(request()->get('true_author_type'));
                event(new NewPost("share", $share, request()->get('id_share')));

                return response()->json(array(
                    'viewContent' => view('page.post-content', [
                        'post' => $share->posts()->first(),
                        'unitPost' => false
                    ])->render(),
                    'targetId' => '#Share-'.class_basename($share->author).'-'.$share->id,
                    'replaceContent' => 1,
                    'closeModal' => 1
                ));
            } else {
                $share = new Share();
                $share->users_id = auth()->guard('web')->user()->id;
                $share->instances_id = session('instanceId');
                $share->author_id = request()->get('true_author_id');
                $share->author_type = "App\\".studly_case(request()->get('true_author_type'));
                $share->post_id = $newsfeed->post->id;
                $share->post_type = get_class($newsfeed->post);
                $share->news_feed_id = request()->get('id_newsfeed');
                $share->language = \Lang::locale();
                $share->media_id = request()->get('media_id');
                $share->content = htmlentities(request()->get('content'));

                $previewJson = [];
                $previewJson['media_id'] = request()->get('preview_media_id');
                $previewJson['author_id'] = $newsfeed->post->author->id;
                $previewJson['author_type'] = get_class($newsfeed->post->author);
                $previewJson['author_name'] = $newsfeed->post->author->getNameDisplay();
                $previewJson['content'] = request()->get('preview_content');

                $share->parameters = json_encode($previewJson);
                $share->save();

                // modify authors fields for newsfeed insert
                $share->confidentiality = 1;
                $share->author_id = request()->get('author_id');
                $share->author_type = "App\\".studly_case(request()->get('author_type'));
                $share->true_author_id = request()->get('true_author_id');
                $share->true_author_type = "App\\".studly_case(request()->get('true_author_type'));

                //insert newsfeed and netframeaction
                event(new NewPost("share", $share));
                event(new CheckUserTag($share, $share->content));

                //implement share counter
                $newsfeed->timestamps = false;
                $newsfeed->share = $newsfeed->share + 1;
                $newsfeed->save();

                //insert interest
                if ($share->post->tags != null) {
                    $interestElement = config('interests.equivalence.'.class_basename($share->post));
                    event(new InterestAction(
                        auth()->guard('web')->user(),
                        $share->post->tags,
                        $interestElement . '.comment'
                    ));
                }

                //check if sharer != author and push notification
                $notifJson = [
                    'post_type'      => get_class($newsfeed->post),
                    'post_id'        => $newsfeed->post->id,
                    'postPreview'    => substr(request()->get('preview_content'), 0, 50),
                    'idNewsFeed'     => request()->get('id_newsfeed'),
                    ];

                if (auth()->guard('web')->user()->id != $newsfeed->post->users_id) {
                    $notif = new Notif();
                    $notif->instances_id = session('instanceId');
                    $notif->author_id = $newsfeed->post->users_id;
                    $notif->author_type = 'App\\User';
                    $notif->type = 'share';
                    $notif->user_from = auth()->guard('web')->user()->id;
                    $notif->parameter = json_encode($notifJson);
                    $notif->save();
                }


                return response()->json(array(
                    'redirect' => request()->header('referer'),
                ));
            }
        }
    }

    /**
     * Get Form HTML for publish a playlist element
     *
     * @var int $idNewsFeed
     */
    public function getFormPublishPlaylist($idPlaylist, $edit = 0)
    {
        // Variable $data for storage variable send to template
        $data = array();

        $data = array();
        $data['playlist'] = Playlist::findOrFail($idPlaylist);
        $data['edit'] = $edit;

        return view('netframe.form-publish-playlist', $data);
    }

    /**
     * form to insert a share
     */
    public function postPublishPlaylist()
    {
        if (!$this->Acl->getRights(request()->get('author_type'), request()->get('author_id'))) {
            return response(view('errors.403'), 403);
        }

        $data = array();
        $data['edit'] = request()->get('edit');

        $validator = validator(request()->all(), config('validation.playlist/publish'));

        # send back variable using in view
        $playlist = Playlist::findOrFail(request()->get('id_playlist'));
        $data['playlist'] = $playlist;

        if ($validator->fails()) {
            $data['errors'] = $validator->messages();
            $data['inputOld'] = request()->all();

            return response()->json(array(
                'view' => view('netframe.form-publish-playlist', $data)->render(),
            ));
        } else {
            // Success post news
            $playlist->content = request()->get('content');
            $playlist->save();

            $playlist->author_id = request()->get('author_id');
            $playlist->author_type = studly_case(request()->get('author_type'));
            $playlist->confidentiality = 1;

            //insert newsfeed and netframeaction
            $id = (request()->get('edit') == 1) ? $playlist->id : null;
            event(new NewPost("playlist", $playlist, $id));

            return response()->json(array(
                //'view' => view('netframe.form-share', $data)->render(),
                'redirect' => request()->header('referer'),
            ));
        }
    }

    /**
     * Get Form HTML for share profile
     *
     * @var int $idNewsFeed
     */
    public function getFormShareProfile($profileType, $profileId, $id = null)
    {
        // Variable $data for storage variable send to template
        $data = array();
        //$profile = $profileType->find($profileId);
        //$profile = call_user_func(array('App\\'.$profileType, 'find'), $profileId);
        $profileModel = Profile::gather(strtolower($profileType));
        $profile = $profileModel::find($profileId);

        $data['media'] = null;

        if ($profile->instances_id != session('instanceId')
            || ($profile->confidentiality == 0 && !BaseController::hasViewProfile($profile))) {
            return response(view('errors.403'), 403);
        }

        if ($id != null) {
            $share = Share::findOrFail($id);

            $share->default_author = new \stdClass();
            $share->default_author->author_id = $share->posts()->first()->author->id;
            $share->default_author->author_type = strtolower(class_basename($share->posts()->first()->author));
            $share->true_author = new \stdClass();
            $share->true_author->author_id = $share->posts()->first()->true_author->id;
            $share->true_author->author_type = strtolower(class_basename($share->posts()->first()->true_author));

            $data['share'] = $share;
            $data['shareContent'] = $share->content;
            $data['shareId'] = $share->id;
            $data['edit'] = 1;
            $data['profile'] = $profile;
        } else {
            $share = new Share();
            $share->default_author = new \stdClass();
            $share->default_author->author_id = auth()->guard('web')->user()->id;
            $share->default_author->author_type = 'user';
            $share->true_author = new \stdClass();
            $share->true_author->author_id = auth()->guard('web')->user()->id;
            $share->true_author->author_type = 'user';
            $data['share'] = $share;
            $data['shareContent'] = '';
            $data['shareId'] = 0;
            $data['edit'] = 0;

            $data['profile'] = $profile;
        }

        return view('netframe.form-share-profile', $data);
    }

    /**
     * form to insert a profile share
     */
    public function postPublishShareProfile()
    {
        if (!$this->Acl->getRights(request()->get('author_type'), request()->get('author_id'), 4)
            || !$this->Acl->getRights(request()->get('true_author_type'), request()->get('true_author_id'), 4)) {
            return response(view('errors.403'), 403);
        }

        $data = array();
        $data['media'] = null;
        $data['edit'] = request()->get('edit');
        $share = Share::findOrNew(request()->get('id'));

        $validator = validator(request()->all(), config('validation.shareProfile'));

        # send back variable using in view
        $profile = call_user_func(array(request()->get('profileType'), 'find'), request()->get('profileId'));

        if ($profile->instances_id != session('instanceId')
            || ($profile->confidentiality == 0 && !BaseController::hasViewProfile($profile))
            || !$this->Acl->getRights(request()->get('author_type'), request()->get('author_id'), 4)
            || !$this->Acl->getRights(request()->get('true_author_type'), request()->get('true_author_id'), 4)
            ) {
            return response(view('errors.403'), 403);
        }

        $data['profile'] = $profile;

        if (in_array(class_basename($profile), config('netframe.shareProfilesTypes'))) {
            if ($validator->fails()) {
                $data['errors'] = $validator->messages();
                $data['inputOld'] = request()->all();
                return response()->json(array(
                    'view' => view('netframe.form-share-profile', $data)->render(),
                ));
            } elseif (request()->get('edit') == 1) {
                $share = Share::findOrFail(request()->get('id_share'));

                if ($share->instances_id != session('instanceId')
                    || !$this->Acl->getRights($share->author_type, $share->author_id, 4)
                    || !$this->Acl->getRights(
                        $share->posts()->first()->true_author_type,
                        $share->posts()->first()->true_author_id,
                        4
                    )
                    ) {
                    return response(view('errors.403'), 403);
                }

                $share->content = request()->get('content');
                $share->author_id = request()->get('true_author_id');
                $share->author_type = "App\\".studly_case(request()->get('true_author_type'));
                $share->save();

                // modify authors fields for newsfeed insert
                $share->confidentiality = 1;
                $share->author_id = request()->get('author_id');
                $share->author_type = "App\\".studly_case(request()->get('author_type'));
                $share->true_author_id = request()->get('true_author_id');
                $share->true_author_type = "App\\".studly_case(request()->get('true_author_type'));
                event(new NewPost("share", $share, request()->get('id_share')));

                return response()->json(array(
                    'viewContent' => view('page.post-content', [
                        'post' => $share->posts()->first(),
                        'unitPost' => false
                    ])->render(),
                    'targetId' => '#Share-'.class_basename($share->author).'-'.$share->id,
                    'replaceContent' => 1,
                    'closeModal' => 1
                ));
            } else {
                $share = new Share();
                $share->users_id = auth()->guard('web')->user()->id;
                $share->instances_id = session('instanceId');
                $share->author_id = request()->get('true_author_id');
                $share->author_type = "App\\".studly_case(request()->get('true_author_type'));
                $share->post_id = $profile->id;
                $share->post_type = get_class($profile);
                $share->language = \Lang::locale();
                $share->content = htmlentities(request()->get('content'));

                $previewJson = [];
                $previewJson['media_id'] = '';
                $previewJson['author_id'] = $profile->id;
                $previewJson['author_type'] = get_class($profile);
                $previewJson['author_name'] = $profile->getNameDisplay();
                $previewJson['content'] = '';

                $share->parameters = json_encode($previewJson);
                $share->save();

                // modify authors fields for newsfeed insert
                $share->confidentiality = 1;
                $share->author_id = request()->get('author_id');
                $share->author_type = "App\\".studly_case(request()->get('author_type'));
                $share->true_author_id = request()->get('true_author_id');
                $share->true_author_type = "App\\".studly_case(request()->get('true_author_type'));

                //insert newsfeed and netframeaction
                event(new NewPost("share", $share));
                event(new CheckUserTag($share, $share->content));

                //implement share counter
                $profile->share = $profile->share + 1;
                $profile->save();

                //insert interest
                if ($profile->tags != null) {
                    $interestElement = config('interests.equivalence.'.class_basename($profile));
                    event(new InterestAction(
                        auth()->guard('web')->user(),
                        $profile->tags,
                        $interestElement . '.comment'
                    ));
                }

                //Notification to owner multiple notification (house, community, project, parnter)
                //get users to notify
                $users = $profile->users()->where('roles_id', '<=', 2)->get();
                $arrayNotification[] = $profile->user->id;

                foreach ($users as $user) {
                    if ($profile->user->id != $user->id) {
                        $arrayNotification[] = $user->id;
                    }
                }


                //check if sharer != author and push notification
                $notifJson = [
                    'profileId'      => $profile->id,
                    'profileType'        => get_class($profile),
                    'idShare'    => $share->id,
                ];

                foreach ($arrayNotification as $idUserNotif) {
                    if ($idUserNotif != auth()->guard('web')->user()->id) {
                        $notif = new Notif();
                        $notif->instances_id = session('instanceId');
                        $notif->author_id = $idUserNotif;
                        $notif->author_type = 'App\\User';
                        $notif->type = 'shareProfile';
                        $notif->user_from = auth()->guard('web')->user()->id;
                        $notif->parameter = json_encode($notifJson);
                        $notif->save();
                    }
                }

                return response()->json(array(
                    'closeModal' => true,
                ));
            }
        }
    }

    /**
     * Get Form HTML for share media
     *
     * @var int $idNewsFeed
     */
    public function getFormShareMedia($mediaId, $id = null)
    {
        // Variable $data for storage variable send to template
        $data = array();
        $media = Media::find($mediaId);
        $data['media'] = $media;

        $author = $media->author[0];

        if ($media->instances_id != session('instanceId')
            || ($author->confidentiality == 0 && !BaseController::hasViewProfile($author))) {
            return response(view('errors.403'), 403);
        }

        if ($id != null) {
            $share = Share::findOrFail($id);

            $share->default_author = new \stdClass();
            $share->default_author->author_id = $share->posts()->first()->author->id;
            $share->default_author->author_type = strtolower(class_basename($share->posts()->first()->author));
            $share->true_author = new \stdClass();
            $share->true_author->author_id = $share->posts()->first()->true_author->id;
            $share->true_author->author_type = strtolower(class_basename($share->posts()->first()->true_author));

            $data['share'] = $share;

            $data['shareContent'] = $share->content;
            $data['shareId'] = $share->id;
            $data['edit'] = 1;
        } else {
            $share = new Share();
            $share->default_author = new \stdClass();
            $share->default_author->author_id = auth()->guard('web')->user()->id;
            $share->default_author->author_type = 'user';
            $share->true_author = new \stdClass();
            $share->true_author->author_id = auth()->guard('web')->user()->id;
            $share->true_author->author_type = 'user';

            $data['share'] = $share;

            $data['shareContent'] = '';
            $data['shareId'] = 0;
            $data['edit'] = 0;
        }

        return view('netframe.form-share-media', $data);
    }

    /**
     * View a PDF with viewer library
     */
    public function getPDFViewer()
    {
        return view('netframe.pdf-viewer');
    }

    /**
     * form to insert a profile share
     */
    public function postPublishShareMedia()
    {
        if (!$this->Acl->getRights(request()->get('author_type'), request()->get('author_id'), 4)
            || !$this->Acl->getRights(request()->get('true_author_type'), request()->get('true_author_id'), 4)) {
            return response(view('errors.403'), 403);
        }

        $data = array();
        $data['edit'] = request()->get('edit');
        $share = Share::findOrNew(request()->get('id'));

        $validator = validator(request()->all(), config('validation.shareMedia'));

        # send back variable using in view
        $media = Media::find(request()->get('mediaId'));
        $data['media'] = $media;

        if ($media->instances_id != session('instanceId')) {
            return response(view('errors.403'), 403);
        }

        $profileOwner = $media->author->first();

        if ($validator->fails()) {
            $data['errors'] = $validator->messages();
            $data['inputOld'] = request()->all();

            $share = new Share();
            $share->default_author = new \stdClass();
            $share->default_author->author_id = request()->get('author_id');
            $share->default_author->author_type = request()->get('author_type');
            $share->true_author = new \stdClass();
            $share->true_author->author_id = request()->get('true_author_id');
            $share->true_author->author_type = request()->get('true_author_type');

            $data['share'] = $share;
            $data['shareContent'] = request()->get('content');

            return response()->json(array(
                'view' => view('netframe.form-share-media', $data)->render(),
            ));
        } elseif (request()->get('edit') == 1) {
            $share = Share::findOrFail(request()->get('id_share'));

            if ($share->instances_id != session('instanceId')
                || !$this->Acl->getRights($share->author_type, $share->author_id)) {
                return response(view('errors.403'), 403);
            }

            $share->content = request()->get('content');
            $share->author_id = request()->get('true_author_id');
            $share->author_type = "App\\".studly_case(request()->get('true_author_type'));
            $share->save();

            // modify authors fields for newsfeed insert
            $share->confidentiality = 1;
            $share->author_id = request()->get('author_id');
            $share->author_type = "App\\".studly_case(request()->get('author_type'));
            $share->true_author_id = request()->get('true_author_id');
            $share->true_author_type = "App\\".studly_case(request()->get('true_author_type'));
            event(new NewPost("share", $share, request()->get('id_share')));

            return response()->json(array(
                'viewContent' => view('page.post-content', [
                    'post' => $share->posts()->first(),
                    'unitPost' => false
                ])->render(),
                'targetId' => '#Share-'.$share->author_type.'-'.$share->id,
                'replaceContent' => 1,
                'closeModal' => 1
            ));
        } else {
            $share = new Share();
            $share->users_id = auth()->guard('web')->user()->id;
            $share->instances_id = session('instanceId');
            $share->author_id = request()->get('true_author_id');
            $share->author_type = "App\\".studly_case(request()->get('true_author_type'));
            $share->post_id = $media->id;
            $share->post_type = get_class($media);
            $share->language = \Lang::locale();
            $share->media_id = $media->id;
            $share->content = htmlentities(request()->get('content'));

            $previewJson = [];
            $previewJson['media_id'] = $media->id;
            $previewJson['author_id'] = $profileOwner->id;
            $previewJson['author_type'] = get_class($profileOwner);
            $previewJson['author_name'] = $profileOwner->getNameDisplay();
            $previewJson['content'] = '';

            $share->parameters = json_encode($previewJson);
            $share->save();

            // modify authors fields for newsfeed insert
            $share->confidentiality = 1;
            $share->author_id = request()->get('author_id');
            $share->author_type = "App\\".studly_case(request()->get('author_type'));
            $share->true_author_id = request()->get('true_author_id');
            $share->true_author_type = "App\\".studly_case(request()->get('true_author_type'));

            //insert newsfeed and netframeaction
            event(new NewPost("share", $share));
            event(new CheckUserTag($share, $share->content));

            //implement share counter
            $media->share = $media->share + 1;
            $media->save();

            // Notification to owner, check if single notification (user)
            // or multiple notification (house, community, project)
            if (class_basename($profileOwner) == 'User') {
                $arrayNotification = [$profileOwner->id];
            } else {
                //get users to notify
                $users = $profileOwner->users()->where('roles_id', '<=', 2)->get();
                $arrayNotification[] = $profileOwner->user->id;
                foreach ($users as $user) {
                    if ($profileOwner->user->id != $user->id) {
                        $arrayNotification[] = $user->id;
                    }
                }
            }

            //check if sharer != author and push notification
            $notifJson = [
                'mediaId'      => $media->id,
                'idShare'    => $share->id,
            ];

            foreach ($arrayNotification as $idUserNotif) {
                if ($idUserNotif != auth()->guard('web')->user()->id) {
                    $notif = new Notif();
                    $notif->instances_id = session('instanceId');
                    $notif->author_id = $idUserNotif;
                    $notif->author_type = 'App\\User';
                    $notif->type = 'shareMedia';
                    $notif->user_from = auth()->guard('web')->user()->id;
                    $notif->parameter = json_encode($notifJson);
                    $notif->save();
                }
            }

            return response()->json(array(
                'closeModal' => true,
            ));
        }
    }

    /**
     * Display form report abuse
     *
     * @param integer $authorId
     * @param integer $postId
     * @param string $postType
     * @return \Illuminate\View\View
     */
    public function getReportAbuse($authorId, $postId, $postType)
    {
        $data['postId'] = $postId;
        $data['postType'] = $postType;

        if ($postType != 'Media') {
             $newsFeed = NewsFeed::select('id', 'users_id')
                ->where('post_id', '=', $postId)
                ->where('post_type', '=', 'App\\'.$postType)->first();
             $data['newsFeedsId'] = $newsFeed->id;
             $data['authorId'] = $newsFeed->users_id;
        } else {
            $data['newsFeedsId'] = 0;
            $data['authorId'] = Media::find($postId)->users_id;
        }

        $reportVerif = new ReportAbuse();
        $checkReport = $reportVerif->reportExist(
            auth()->guard('web')->user()->id,
            $authorId,
            'App\\'.$postId,
            $postType
        );

        if ($checkReport === false) {
            $data['messageInfo'] = true;
        }

        return view("netframe.form-reportabus", $data);
    }

    /**
     * Post processing form report abuse
     *
     * @param integer request()->get(users_id_property)
     * @param integer request()->get(post_id)
     * @param string request()->get(post_type)
     * @param string request()->get(type_abuse)
     *
     * @return void|\Illuminate\Http\JsonResponse
     */
    public function postReportAbuse()
    {
        // If type post abuse authorize or type abuse not exist, run exception
        if (!in_array(request()->get('post_type'), config('netframe.listItemTypeAbuse'))) {
            return response(view('errors.404'), 404);
        }

        $validator = validator(request()->all(), config('validation.abuse'));

        if (!$validator->fails()) {
            $reportVerif = new ReportAbuse();
            $checkReport = $reportVerif->reportExist(
                auth()->guard('web')->user()->id,
                request()->get('users_id_property'),
                request()->get('post_id'),
                'App\\'.request()->get('post_type'),
                request()->get('type_abuse')
            );

            if ($checkReport === false) {
                // You have already reported this
                $data['messageInfo'] = true;
            } else {
                $data['messageSuccess'] = true;
                // Create a new report abuse
                if ($checkReport == null) {
                    $report = new ReportAbuse();
                    $report->instances_id = session('instanceId');
                    $report->users_id_property = request()->get('users_id_property');
                    $report->post_id = request()->get('post_id');
                    $report->post_type = 'App\\'.request()->get('post_type');
                    $report->news_feeds_id = request()->get('newsfeed');
                    $report->type_abuse = request()->get('type_abuse');
                    $report->number = 1;
                    $report->save();
                    $report->user()->attach(auth()->guard('web')->user()->id);
                } else { // Update report abuse
                    $updateReport = ReportAbuse::find($checkReport->report_abuses_id);
                    $updateReport->number = $updateReport->number +1;
                    $updateReport->save();
                    $updateReport->user()->attach(auth()->guard('web')->user()->id);
                }
            }

            return response()->json(array(
                    'view' => view('netframe.form-reportabus', $data)->render(),
            ));
        } else {
            $data = array(
                'authorId' => request()->get('users_id_property'),
                'postId' => request()->get('post_id'),
                'postType' => request()->get('post_type'),
                'newsfeed' => request()->get('newsfeed'),
                'messages' => $validator->messages(),
            );

            return response()->json(array(
                'view' => view('netframe.form-reportabus', $data)->render(),
            ));
        }
    }

    /**
     * store gmt gap into session
     */
    public function postSetGmt()
    {
        // GMT gap in seconds
        $gmtGap = - intval(request()->get('gmt')) * 60;
        $gmtZone = request()->get('timezone');

        //session(['gmtGap' => $gmtGap]);
        //session(['timeZone' => $gmtZone]);
    }

    public function workspaceHome()
    {
        $dataUser = User::findOrFail(auth()->guard('web')->user()->id);
        $timelinePref = $dataUser->getParameter('timelinePref');
        if ($timelinePref == null) {
            $dataUser->setParameter('timelinePref', 'timeline');
            $timelinePref = 'timeline';
        }

        $instance = Instance::find(session('instanceId'));
        $app24h = Application::where('slug', '=', '24h')->first();

        if (($app24h != null && $instance->apps->contains($app24h->id)) || $app24h == null) {
            switch ($timelinePref) {
                case 'timeline':
                    return redirect()->route('user.timeline');
                    break;

                case 'anynews':
                    return redirect()->route('netframe.anynews');
                    break;
            }
        } else {
            return redirect()->route('user.timeline');
        }
    }

    /*
     * User portal
     */
    public function portal()
    {
        $data = [
            'dataUser' => auth()->user(),
        ];

        // add notifications
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

        return view('portal.home', $data);
    }

    /**
     * get all news from newsfeed
     */
    public function anyNews($date_time_last = null)
    {
        $config = config('netframe');

        $data = [];

        // set user main feed preference
        $dataUser = User::findOrFail(auth()->guard('web')->user()->id);
        $data['rights'] = $this->Acl->getRights('user', $dataUser->id);

        // set user main feed preference
        $dataUser->setParameter('timelinePref', 'anynews');

        $data['unitPost'] = false;
        $data['dataUser'] = $dataUser;

        $data['newsfeed'] = NewsFeed::select('news_feeds.*')
            ->where('news_feeds.post_type', '!=', 'App\TaskTable')
            ->where('news_feeds.instances_id', '=', session('instanceId'))
            ->where('author_type', '!=', 'App\\Channel')
            ->where(function ($where) use ($date_time_last) {
                if ($date_time_last !== null) {
                    $where->where('news_feeds.created_at', '<', $date_time_last);
                }
            })
            ->where('confidentiality', '=', 1)
            ->where('post_type', '!=', 'NetframeAction')
            ->where('active', '=', '1')
            ->orderBy('news_feeds.created_at', 'desc')
            ->take($config['number_post'])
            ->with(['post', 'author', 'post.author', 'post.comments'])
            ->get();

        /*
            dump($data['newsfeed']);
        die();
        */

        //prepare right
        $data['newProfiles'] = User::lastValidated(8);

        /*
        $medias = New Media();
        $data['newMedias'] = $medias->lastNetframeMedias(4);
        */

        $events = new TEvent();
        $data['newEvents'] = $events->nextOrLast(2);
        $data['lastNews'] = NewsFeed::lastNews('News', 2);
        $data['lastActions'] = NewsFeed::lastNews('NetframeAction', 2);
        $data['calendarView'] = 'allEvents';

        if (request()->isMethod('GET')) {
            return View('netframe.tout-netframe', $data);
        } elseif (request()->isMethod('POST')) {
            $data['withLoader'] = true;
            $view = view('page.post-container', $data)->render();
            return response()->json(['view' => $view]);
        }
    }

    public function loadSvg($name)
    {
        $view = view('macros.svg-icons.'.$name)->render();

        $response = new Response();
        $response->headers->set('Content-Type', 'image/svg+xml');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Cache-Control', 'post-check=0, pre-check=0', false);
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $name.'.svg'));
        $response->setContent($view);
        return $response;
    }

    public function likers($elementType, $elementId)
    {
        switch ($elementType) {
            case 'Media':
                $model = new Media();
                break;
            case 'Share':
                $model = new Share();
                break;
            case 'NetframeAction':
                $model = new NetframeAction();
                break;
            case 'Offer':
                $model = new Offer();
                break;
            case 'News':
                $model = new News();
                break;
            case 'TEvent':
                $model = new TEvent();
                break;
            case 'Comment':
                $model = new Comment();
                break;
            case 'House':
                $model = new House();
                break;
            case 'Community':
                $model = new Community();
                break;
            case 'Project':
                $model = new Project();
                break;
        }
        $element = $model::find($elementId);


        $likers = $element->liked()
                ->with(['liker'])
                ->orderBy('emojis_id')
                ->get();

        $emojis = $element->liked()
                ->groupBy('emojis_id')
                ->select('*', \DB::raw('count(*) as total'))
                ->orderBy(\DB::raw('total', 'DESC'))
                ->get();
        // dd($likers[1]->emoji);
        $data['likers'] = $likers;
        // $ids = Instance::find(session('instanceId'))->getParameter('like_buttons');
        // if($ids!=null)
        //     $emojis = Emoji::findMany(json_decode($ids,true));
        // else
        //     $emojis = Emoji::limit(5)->get();
        $data['emojis'] = $emojis;
        return view('netframe.likers', $data);
    }

    public function viewers($elementType, $elementId)
    {
        switch ($elementType) {
            case 'NewsFeed':
                $model = new NewsFeed();
                break;
            case 'Media':
                $model = new Media();
                break;
        }
        $element = $model::find($elementId);


        $viewers = $element->views()->groupBy('views.users_id')->with('user')->get();
        $data['viewers'] = $viewers;
        return view('netframe.viewers', $data);
    }

    public function tagPeople(Request $request)
    {
        $query = request()->get('term');
        $request->merge(['query' => request()->get('term')]);

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
                'value' => $user->getNameDisplay(),
                'image' => $user->profileImageSrc(),
                'uid' => 'user:'.$user->id,
            ];
        }

        return response()->json($returnResult);
    }
}
