<?php
namespace App\Http\Controllers\Channel;

use App\Http\Controllers\BaseController;
use \App\Helpers\Lib\Acl;
use App\Channel;
use App\News;
use App\NewsFeed;
use App\Media;
use App\Profile;
use App\Events\NewPost;
use App\Events\PostChannel;
use App\Events\DeleteChannelPost;
use App\Events\CheckUserTag;

class MessagesController extends BaseController
{
    private $channel_id = null;

    public function __construct()
    {
        $this->middleware('checkAppActive:channel');
        $this->middleware('checkAuth');
        parent::__construct();
    }

    public function read($messageId)
    {
    }

    public function getPost($channel_id, $post_id)
    {
        $channel = Channel::find($channel_id);
        if ($channel->confidentiality == 0  || auth()->guard('web')->user()->channels->contains($channel_id)) {
            $channel = auth()->guard('web')->user()->channels()->where('id', '=', $channel_id)->first();
        }

        $post = $channel->posts()
            ->where('post_id', '=', $post_id)
            ->where('post_type', '=', 'App\\News')
            ->with(['post', 'post.comments', 'user:id,name,firstname,profile_media_id'])
            ->first();

        $dataPostView = [];
        $dataPostView['unitPost'] = false;
        $dataPostView['rights'] = $this->Acl->getRights('App\Channel', $channel_id);

        $dataPostView['post'] = $post;
        $postView = view('channel.partials.post-content', $dataPostView)->render();

        $data = [];
        $data['returnCode'] = 'success';
        $data['viewContent'] = $postView;
        return response()->json($data);
    }

    public function delete($postId)
    {
        $posts = NewsFeed::find($postId);
        $post = $posts->post;

        if (!BaseController::hasRights($post) && !BaseController::hasRights($posts)) {
            return response(view('errors.403'), 403);
        }

        $posts->delete();

        // Run event to broadcast delete
        event(new DeleteChannelPost($posts->author_id, $posts->id, $post));

        return response()->json(array(
            'delete' => true,
            'targetId' => "#".class_basename($posts->post)."-" . class_basename($posts->author) . '-' . $posts->post_id
        ));
    }

    public function emojis()
    {
        return response()->json(\App\EmojisGroup::with('emojis')->get());
    }

    public function posting($post_id = null, $channel_id = null)
    {
        $post_type = 'news';

        if ($channel_id != null) {
            $this->channel_id = $channel_id;
        } else {
            $this->channel_id = request()->get('channel_id');
        }

        $channel = Channel::findOrFail($this->channel_id);
        $channel = Channel::findOrFail($this->channel_id);
        if ($channel->instances_id != session('instanceId')) {
            return response()->json([], 403);
        }

        if (request()->isMethod('POST')) {
            $post_type = request()->get('post_type');
            $layout_type = request()->get('layout_type');

            if (request()->has('_token')) {
                $postOnId = $this->channel_id;
                $postOnType = 'channel';
                $postAsId = auth()->guard('web')->user()->id;
                $postAsType = 'user';

                if (!$this->Acl->getRights($postOnType, $postOnId, 4)
                    || !$this->Acl->getRights($postAsType, $postAsId, 3)) {
                    return response()->json([], 403);
                }
            }
            switch ($post_type) {
                case 'news':
                default:
                    $data = $this->news();
                    break;
            }
            return response()->json($data);

            $data['modal'] = (isset($data['modal'])) ? $data['modal'] : false;
            $data['form_id'] = 'form-post-'.rand();
            $data['channel_id'] = $this->channel_id;
            $return = [
                'returnCode' => $data['return_code'],
                'returnMessage' => $data['return_message'],
                'modal' => $data['modal'],
                'typePost' => $post_type,
                'post' => $data['post']
            ];
            if (isset($data['viewContent'])) {
                $return['viewContent'] = $data['viewContent'];
                $return['targetId'] = $data['targetId'];
                $return['date'] = $data['date'];
            }
        } elseif (request()->isMethod('GET')) {
            switch ($post_type) {
                case 'news':
                default:
                    $data = $this->news($post_id);
                    break;
            }

            $data['channel_id'] = $this->channel_id;
            $data['modal'] = true;
            $data['form_id'] = 'form-post-'.rand();
            return  view('channel.post.form', $data)->render();
        }
    }

    private function news($post_id = null)
    {
        $sub_view = 'news';
        $data = [];
        $data['modal'] = request()->get('modal');
        $data['return_code'] = 'success';
        $data['return_message'] = '';

        if (request()->has('_token')) { // validate form
            if (!empty(request()->get('mediasIds')) || !empty(request()->get('linksIds'))) {
                $validator = validator(request()->all(), config('validation.channel/newsPostWithMedias'));
            } else {
                $validator = validator(request()->all(), config('validation.channel/newsPost'));
            }
            // send back error message for validate form
            $data['errors'] = $validator->messages();
            $data['mediasIds'] = request()->get('mediasIds');
            $data['linksIds'] = request()->get('linksIds');

            if ($validator->fails()) {
                $post = new News();
                $post->id = (request()->has('id')) ? request()->get('id') : null;
                $post->users_id = auth()->guard('web')->user()->id;
                $post->content = request()->get('content');
                $post->confidentiality = 1;
                $post->disable_comments = 0;
                $post->language = \Lang::locale();

                $data['return_code'] = 'error';
            } else {
                $oldMediasList = [];
                if (request()->has('id')) {
                    $post = News::findOrFail(request()->get('id'));
                    if (!$this->Acl->getRights($post->author_type, $post->author_id)) {
                        return response()->json([], 403);
                    }
                    $data['return_message'] = $successMessage = 'successUpdate';
                    $idNewsFeed = request()->get('id');

                    foreach ($post->medias as $media) {
                        $oldMediasList[] = $media->id;
                    }
                } else {
                    if (!$this->Acl->getRights('channel', $this->channel_id, 4)) {
                        return response()->json([], 403);
                    }
                    $post = new News();
                    $data['return_message'] = $successMessage = 'successInsert';
                    $idNewsFeed = null;
                }

                $post->users_id = auth()->guard('web')->user()->id;
                $post->instances_id = session('instanceId');
                $post->author_id = auth()->guard('web')->user()->id;
                $post->author_type = "App\\User";
                $post->content = request()->get('content');
                $post->confidentiality = 1;
                $post->disable_comments = 0;
                $post->language = \Lang::locale();
                $post->save();


                // Save the medias
                $medias = \App\Helpers\StringHelper::toArray(request()->get('mediasIds'));
                $post->medias()->detach();

                // detach old medias
                foreach ($oldMediasList as $oldMedia) {
                    $post->author->medias()->detach($oldMedia);
                }

                $mediaFolder = $post->author->getDefaultFolder('__posts_medias');

                foreach ($medias as $mediaId) {
                    $post->medias()->attach($mediaId);
                    $post->author->medias()->attach($mediaId);
                }
                //place media linked state
                Media::whereIn('id', $medias)->update(['linked' => 1]);

                // save the links
                $links = \App\Helpers\StringHelper::toArray(request()->get('linksIds'));
                $post->links()->detach();

                foreach ($links as $linkId) {
                    $post->links()->attach($linkId);
                }

                // Run event register in table news_feeds
                $post->author_id = $this->channel_id;
                $post->author_type = "App\\Channel";
                $post->true_author_id = auth()->guard('web')->user()->id;
                $post->true_author_type = "App\\User";
                event(new NewPost("news", $post, $idNewsFeed, $medias, $oldMediasList));
                event(new CheckUserTag($post, $post->content));

                //implement post view to add or replace in newsfeed
                $data['date'] = $post->created_at->format('Y-m-d');
                if ($data['modal']) {
                    $data['targetId'] = '#News-Channel-'.$post->id;
                } else {
                    $data['targetId'] = '.feed-Channel-'.$this->channel_id;
                }

                //attach message to user for this channel
                $channel = Channel::find($this->channel_id);
                $channel->touch();
                $usersId = $channel->users()->whereStatus(1)->pluck('id');

                if (!request()->has('id')) {
                    $userMessages = [];
                    foreach ($usersId as $userId) {
                        $read = ($userId == auth()->guard('web')->user()->id) ? 1 : 0;
                        $userMessages[] = [
                            'channels_id' => $this->channel_id,
                            'news_feeds_id' => $post->posts()->first()->id,
                            'users_id' => $userId,
                            'read' => $read,
                            'created_at' => $post->created_at,
                            'updated_at' => $post->created_at,
                        ];
                    }

                    \DB::table('channels_has_news_feeds')->insert($userMessages);

                    //REDIS NOTIFICATION ADD POST
                    $postChan = News::find($post->id)->toArray();
                    broadcast(new PostChannel($this->channel_id, $postChan))->toOthers();
                } else {
                    //REDIS NOTIFICATION UPDATE POST
                    broadcast(new PostChannel($this->channel_id, $post))->toOthers();
                }
                $post = News::find($post->id);

                return ['post' => $post, 'return_message' => $data['return_message']];

                //reinit vars for empty form
                $post = new News();
                $data['mediasIds'] = null;
                $data['linksIds'] = null;
            }
        } elseif (request()->has('id') || $post_id != null) { // creating form to update existing post
            $id_post = (request()->has('id')) ? request()->has('id') : $post_id;
            $post = News::findOrFail($id_post);

            //check rights
            if (!$this->Acl->getRights($post->author_type, $post->author_id)) {
                return response()->json([], 403);
            }

            $listMedia = array();
            foreach ($post->medias as $media) {
                $listMedia[] = $media->id;
            }
            $data['mediasIds'] =  (!empty($listMedia)) ? implode(',', $listMedia) : null;

            $listLinks = array();
            foreach ($post->links as $link) {
                $listLink[] = $link->id;
            }
            $data['linksIds'] = (!empty($listLink)) ? implode(',', $listLink) : null;
        } else { // creating form for new post
            $post = new News();
        }

        $data['post'] = $post;
        $data['sub_view'] = $sub_view;
        return $data;
    }
}
