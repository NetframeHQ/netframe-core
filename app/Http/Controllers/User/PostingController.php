<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use Validator;
use App\News;
use App\Offer;
use App\TEvent;
use App\Media;
use App\Events\NewPost;
use App\Events\InterestAction;
use App\Profile;
use App\Events\CheckUserTag;

class PostingController extends BaseController
{

    public function __construct()
    {
        $this->middleware('checkAuth');
        parent::__construct();
    }

    /**
     * manage posting forms
     * input post is array with post_type vars depending type
     * mayout_type contain inline or modal
     */
    public function posting($post_type = null, $post_id = null)
    {
        /*
        $post_vars = request()->get('post');
        $init_form = (null != request()->get('init_form')) ? true : false;
        */

        if (request()->isMethod('POST')) {
            $post_type = request()->get('post_type');
            $layout_type = request()->get('layout_type');


            if (request()->has('_token')) {
                $postOnId = request()->get('id_foreign');
                $postOnType = request()->get('type_foreign');
                $postAsId = request()->get('id_foreign_as');
                $postAsType = request()->get('type_foreign_as');

                if ((!request()->has('id')
                        && !$this->Acl->getRights($postOnType, $postOnId, 4)
                        || !$this->Acl->getRights($postAsType, $postAsId, 3))
                    && (request()->has('id') && !$this->Acl->getRights($postOnType, $postOnId, 3))) {
                    return response(view('errors.403'), 403);
                }
            }

            switch ($post_type) {
                case 'news':
                default:
                    $data = $this->news();
                    $post_type = 'news';
                    break;

                case 'event':
                    $data = $this->event();
                    break;

                case 'offer':
                    $data = $this->offer();
                    break;
            }

            $data['modal'] = (isset($data['modal'])) ? $data['modal'] : false;
            $data['form_id'] = 'form-post-'.rand();
            $data['typePost'] = $post_type;
            $return = [
                'view' => view('posting.posting-container', $data)->render(),
                'returnCode' => $data['return_code'],
                'returnMessage' => $data['return_message'],
                'modal' => $data['modal'],
                'typePost' => $post_type,
                'displayMap' => (isset($data['display_map'])) ? $data['display_map'] : false
            ];

            if (isset($data['viewContent'])) {
                $return['viewContent'] = $data['viewContent'];
                if (isset($data['newPost']) && $data['newPost'] == true) {
                    $return['targetId'] = "#newsFeed";
                } else {
                    $return['targetId'] = $data['targetId'];
                }
            }

            if (isset($data['autoFireModal'])) {
                $return['autoFireModal'] = $data['autoFireModal'];
            }

            return response()->json($return);
        } elseif (request()->isMethod('GET')) {
            switch ($post_type) {
                case 'news':
                default:
                    $data = $this->news($post_id);
                    break;

                case 'event':
                    $data = $this->event($post_id);
                    break;

                case 'offer':
                    $data = $this->offer($post_id);
                    break;
            }

            $data['modal'] = true;
            $data['form_id'] = 'form-post-'.rand();
            $data['typePost'] = $post_type;
            return  view('posting.posting-container', $data)->render();

            // preparation for in place editing
            /*
            return  response()->json([
                'view' => view('posting.posting-container', $data)->render(),
            ]);
            */
        }
    }

    private function news($post_id = null)
    {
        $sub_view = 'news';
        $data = [];
        $data['modal'] = request()->get('modal');
        $data['return_code'] = 'success';
        $data['return_message'] = '';

        if (request()->has('hideControls')) {
            $data['hideControls'] = true;
        }

        if (request()->has('_token')) { // validate form
            if (!empty(request()->get('mediasIds')) || !empty(request()->get('linksIds'))) {
                $validator = validator(request()->all(), config('validation.page/newsPostWithMedias'));
            } else {
                $validator = validator(request()->all(), config('validation.page/newsPost'));
            }
            // send back error message for validate form
            $data['errors'] = $validator->messages();
            $data['mediasIds'] = request()->get('mediasIds');
            $data['linksIds'] = request()->get('linksIds');
            $confidentiality = (request()->has('confidentiality')) ? request()->get('confidentiality') : 1;

            if ($validator->fails()) {
                $post = new News();
                $post->id = (request()->has('id')) ? request()->get('id') : null;
                $post->users_id = auth()->guard('web')->user()->id;
                $post->id_foreign = request()->get('id_foreign');
                $post->type_foreign = studly_case(request()->get('type_foreign'));
                $post->id_foreign_as = request()->get('id_foreign_as');
                $post->type_foreign_as = studly_case(request()->get('type_foreign_as'));
                $post->content = request()->get('content');
                $post->confidentiality = $confidentiality;
                $post->disable_comments = request()->get('disable_comments');
                $post->language = \Lang::locale();

                $post->default_author = new \stdClass();
                $post->default_author->author_id = request()->get('id_foreign');
                $post->default_author->author_type = request()->get('type_foreign');
                $post->true_author = new \stdClass();
                $post->true_author->author_id = request()->get('id_foreign_as');
                $post->true_author->author_type = request()->get('type_foreign_as');

                $post->formTags = \App\Helpers\TagsHelper::getFromForm(request()->get('tags'));
                $post->formTagsSelecteds = request()->get('tags');

                $data['return_code'] = 'error';
            } else {
                $oldMediasList = [];
                if (request()->has('id')) {
                    $post = News::findOrFail(request()->get('id'));
                    if (!$this->Acl->getRights($post->posts[0]->author_type, $post->posts[0]->author_id)) {
                        return response(view('errors.403'), 403);
                    }
                    $data['return_message'] = $successMessage = 'successUpdate';
                    $idNewsFeed = request()->get('id');

                    $oldTags = $post->tags;

                    foreach ($post->medias as $media) {
                        $oldMediasList[] = $media->id;
                    }
                } else {
                    if (!$this->Acl->getRights(request()->get('type_foreign'), request()->get('id_foreign'), 4)) {
                        return response(view('errors.403'), 403);
                    }
                    $post = new News();
                    $data['return_message'] = $successMessage = 'successInsert';
                    $idNewsFeed = null;
                    $data['newPost'] = true;
                }

                //check if private profile to set confidentiality to private
                if (in_array(request()->get('type_foreign'), ['house', 'project', 'community'])) {
                    $profileModel = Profile::gather(request()->get('type_foreign'));
                    $profile = $profileModel::find(request()->get('id_foreign'));
                    if ($profile->confidentiality == 0) {
                        $confidentiality = 0;
                    }
                }

                $post->users_id = auth()->guard('web')->user()->id;
                $post->instances_id = session('instanceId');
                $post->author_id = request()->get('id_foreign_as');
                $post->author_type = "App\\".studly_case(request()->get('type_foreign_as'));
                $post->content = htmlentities(request()->get('content'));
                $post->confidentiality = $confidentiality;
                if (request()->has('disable_comments')) {
                    $post->disable_comments = request()->get('disable_comments');
                } else {
                    $post->disable_comments = 0;
                }
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
                    if ($mediaFolder != null) {
                        $post->author->medias()->attach($mediaId, ['medias_folders_id' => $mediaFolder]);
                    }
                }

                //place media linked state
                Media::whereIn('id', $medias)->update(['linked' => 1, 'confidentiality'=> $confidentiality]);

                // Save the tags for medias
                $mediasObj = Media::whereIn('id', $medias)->get();
                foreach ($mediasObj as $media) {
                    \App\Helpers\TagsHelper::attachPostedTags(request()->get('tags'), $media);
                }

                // save the links
                $links = \App\Helpers\StringHelper::toArray(request()->get('linksIds'));
                $post->links()->detach();

                foreach ($links as $linkId) {
                    $post->links()->attach($linkId);
                }

                // Run event register in table news_feeds
                $post->author_id = request()->get('id_foreign');
                $post->author_type = "App\\".studly_case(request()->get('type_foreign'));
                $post->true_author_id = request()->get('id_foreign_as');
                $post->true_author_type = "App\\".studly_case(request()->get('type_foreign_as'));
                event(new NewPost("news", $post, $idNewsFeed, $medias, $oldMediasList));
                event(new CheckUserTag($post, $post->content));

                // Save the tags
                \App\Helpers\TagsHelper::attachPostedTags(request()->get('tags'), $post);

                //insert interest
                if (!request()->has('id')) {
                    $tags = $post->tags;
                } else {
                    $post->load('tags');
                    $tags = \App\Helpers\TagsHelper::compareTags($oldTags, $post->tags);
                }

                if ($post->tags != null) {
                    event(new InterestAction(auth()->guard('web')->user(), $post->tags, 'post.create'));
                }

                //implement post view to add or replace in newsfeed
                $data['viewContent'] = view(
                    'page.post-content',
                    ['post' => $post->posts()->first(), 'unitPost' => false]
                )->render();

                if ($data['modal']) {
                    $data['targetId'] = '#News-'.class_basename($post->posts()->first()->author).'-'.$post->id;
                } else {
                    $data['targetId'] = '.feed-'.class_basename($post->author).'-'.$post->author_id;
                }

                $data['autoFireModal'] = view(
                    'posting.success',
                    ['successMessage' => 'News.'.$successMessage]
                )->render();

                //reinit vars for empty form
                $post = new News();
                $data['mediasIds'] = null;
                $data['linksIds'] = null;
            }
        } elseif (request()->has('id') || $post_id != null) { // creating form to update existing post
            $id_post = (request()->has('id')) ? request()->has('id') : $post_id;
            $post = News::findOrFail($id_post);

            //check rights
            if (!$this->Acl->getRights($post->posts[0]->author_type, $post->posts[0]->author_id)) {
                return response(view('errors.403'), 403);
            }

            $listMedia = array();
            foreach ($post->medias as $media) {
                $listMedia[] = $media->id;
            }
            $data['mediasIds'] = (!empty($listMedia)) ? implode(',', $listMedia) : null;

            $listLinks = array();
            foreach ($post->links as $link) {
                $listLink[] = $link->id;
            }
            $data['linksIds'] = (!empty($listLink)) ? implode(',', $listLink) : null;
        } else { // creating form for new post
            $post = new News();
            $post->default_author = new \stdClass();
            $post->default_author->author_id = request()->get('default_author_id');
            $post->default_author->author_type = request()->get('default_author_type');
        }

        $data['post'] = $post;
        $data['sub_view'] = $sub_view;

        return $data;
    }

    private function event($post_id = null)
    {
        $sub_view = 'event';
        $data = [];
        $data['modal'] = request()->get('modal');

        $data['return_code'] = 'success';
        $data['return_message'] = '';
        if (request()->has('hideControls')) {
            $data['hideControls'] = true;
        }

        if (request()->has('_token')) { //updating existing element
            $validationRules = [
                'title' => 'required',
                'date' => 'required|date|date_format:Y-m-d',
                'placeSearch' => 'sometimes',
                'date_end' => 'nullable|date|date_format:Y-m-d',
            ];

            $startTime = [
                'time' => (strlen(request()->get('time')) > 5)
                    ? 'required|date_format:H:i:s'
                    : 'required|date_format:H:i'
            ];
            $endTime = [
                'time_end' => (strlen(request()->get('time_end')) > 5)
                    ? 'required|date_format:H:i:s'
                    : 'required|date_format:H:i'
            ];

            if (!request()->exists('all_day')) {
                $validationRules = array_merge($validationRules, $startTime);
            }

            if (request()->get('date_end') != null && !request()->exists('all_day')) {
                $validationRules = array_merge($validationRules, $endTime);
            }

            //custom rule
            if (request()->get('date_end') != null && request()->get('date') != null) {
                if (request()->get('date') > request()->get('date_end')) {
                    $endDate = ['date_end' => 'required|date|date_format:Y-m-d|after:'.request()->get('date')];
                    $validationRules = array_merge($validationRules, $endDate);
                }

                if (!request()->exists('all_day')
                    && request()->get('date') == request()->get('date')
                    && request()->get('time') > request()->get('time_end')) {
                    $endTime = [
                        'time_end' => (strlen(request()->get('time_end')) > 5)
                            ? 'required|date_format:H:i:s|after:' . request()->get('time')
                            : 'required|date_format:H:i|after:' . request()->get('time')
                    ];
                    $validationRules = array_merge($validationRules, $endTime);
                }
            }

            $validator = validator(request()->all(), $validationRules);
            // send back error message for validate form
            $data['errors'] = $validator->messages();
            $data['mediasIds'] = request()->get('mediasIds');
            $data['linksIds'] = request()->get('linksIds');

            $confidentiality = (request()->has('confidentiality')) ? request()->get('confidentiality') : 1;

            if ($validator->fails()) {
                $post = new TEvent();
                $post->id = (request()->has('id')) ? request()->get('id') : null;
                $post->users_id = auth()->guard('web')->user()->id;
                $post->id_foreign = request()->get('id_foreign');
                $post->type_foreign = studly_case(request()->get('type_foreign'));
                $post->id_foreign_as = request()->get('id_foreign_as');
                $post->type_foreign_as = studly_case(request()->get('type_foreign_as'));
                $post->title = request()->get('title');
                $post->description = request()->get('description');
                $post->date = request()->get('date');
                $post->time = request()->get('time');
                $post->date_end = request()->get('date_end');
                $post->time_end = request()->get('time_end');
                $post->latitude = request()->get('latitude');
                $post->longitude = request()->get('longitude');
                $post->location = \App\Helpers\LocationHelper::getLocation($post->latitude, $post->longitude);
                $post->confidentiality = $confidentiality;
                $post->disable_comments = request()->get('disable_comments');
                $post->language = \Lang::locale();

                $post->default_author = new \stdClass();
                $post->default_author->author_id = request()->get('id_foreign');
                $post->default_author->author_type = request()->get('type_foreign');
                $post->true_author = new \stdClass();
                $post->true_author->author_id = request()->get('id_foreign_as');
                $post->true_author->author_type = request()->get('type_foreign_as');

                $post->formTags = \App\Helpers\TagsHelper::getFromForm(request()->get('tags'));
                $post->formTagsSelecteds = request()->get('tags');

                $data['return_code'] = 'error';
                if (!empty(request()->get('placeSearch'))) {
                    $data['display_map'] = true;
                }
            } else {
                $oldMediasList = [];

                if (request()->has('id')) {
                    $post = TEvent::findOrFail(request()->get('id'));
                    if (!$this->Acl->getRights($post->posts[0]->author_type, $post->posts[0]->author_id)) {
                        return response(view('errors.403'), 403);
                    }
                    $data['return_message'] = $successMessage = 'successUpdate';
                    $idNewsFeed = request()->get('id');

                    $oldTags = $post->tags;

                    foreach ($post->medias as $media) {
                        $oldMediasList[] = $media->id;
                    }
                } else {
                    if (!$this->Acl->getRights(request()->get('type_foreign'), request()->get('id_foreign'), 4)) {
                        return response(view('errors.403'), 403);
                    }
                    $post = new TEvent();
                    $data['return_message'] = $successMessage = 'successInsert';
                    $idNewsFeed = null;
                    $data['newPost'] = true;
                }

                //check if private profile to set confidentiality to private
                if (in_array(request()->get('type_foreign'), ['house', 'project', 'community'])) {
                    $profileModel = Profile::gather(request()->get('type_foreign'));
                    $profile = $profileModel::find(request()->get('id_foreign'));
                    if ($profile->confidentiality == 0) {
                        $confidentiality = 0;
                    }
                }

                $post->users_id = auth()->guard('web')->user()->id;
                $post->instances_id = session('instanceId');
                $post->author_id = request()->get('id_foreign_as');
                $post->author_type = "App\\".studly_case(request()->get('type_foreign_as'));
                $post->title = htmlentities(request()->get('title'));
                $post->description = htmlentities(request()->get('description'));
                $post->date = request()->get('date');
                if (request()->get('time') != '' && !request()->exists('all_day')) {
                    // compute gmt dates
                    $utcDate = \App\Helpers\DateHelper::convertToLocalUTC(
                        request()->get('date') . ' ' . request()->get('time')
                    );

                    $post->time = $utcDate['time'];
                    $post->start_date = $utcDate['datetime'];
                } else {
                    $post->time = null;
                }

                if (request()->get('date_end') != '') {
                    $post->date_end = request()->get('date_end');
                } else {
                    $post->date_end = null;
                }
                if (request()->get('time_end') != '' && !request()->exists('all_day')) {
                    $utcDate = \App\Helpers\DateHelper::convertToLocalUTC(
                        request()->get('date_end') . ' ' . request()->get('time_end')
                    );

                    $post->time_end = $utcDate['time'];
                    $post->end_date = $utcDate['datetime'];
                } else {
                    $post->time_end = null;
                }

                $post->latitude = request()->get('latitude');
                $post->longitude = request()->get('longitude');
                $post->location = \App\Helpers\LocationHelper::getLocation($post->latitude, $post->longitude);
                $post->confidentiality = $confidentiality;
                if (request()->has('disable_comments')) {
                    $post->disable_comments = request()->get('disable_comments');
                } else {
                    $post->disable_comments = 0;
                }
                //$post->language = \Lang::locale();
                $post->save();

                // Save the medias
                $medias = \App\Helpers\StringHelper::toArray(request()->get('mediasIds'));
                $post->medias()->detach();

                //detach old medias
                foreach ($oldMediasList as $oldMedia) {
                    $post->author->medias()->detach($oldMedia);
                }

                $mediaFolder = $post->author->getDefaultFolder('__posts_medias');

                foreach ($medias as $mediaId) {
                    $post->medias()->attach($mediaId);
                    if ($mediaFolder != null) {
                        $post->author->medias()->attach($mediaId, ['medias_folders_id' => $mediaFolder]);
                    }
                }
                //place media linked state
                Media::whereIn('id', $medias)->update(['linked' => 1, 'confidentiality'=> $confidentiality]);

                // Save the tags for medias
                $mediasObj = Media::whereIn('id', $medias)->get();
                foreach ($mediasObj as $media) {
                    \App\Helpers\TagsHelper::attachPostedTags(request()->get('tags'), $media);
                }

                // save the links
                $links = \App\Helpers\StringHelper::toArray(request()->get('linksIds'));
                $post->links()->detach();

                foreach ($links as $linkId) {
                    $post->links()->attach($linkId);
                }

                // Run event register in table news_feeds
                $post->author_id = request()->get('id_foreign');
                $post->author_type = "App\\".studly_case(request()->get('type_foreign'));
                $post->true_author_id = request()->get('id_foreign_as');
                $post->true_author_type = "App\\".studly_case(request()->get('type_foreign_as'));
                event(new NewPost("TEvent", $post, $idNewsFeed, $medias, $oldMediasList));
                event(new CheckUserTag($post, $post->description));

                // Save the tags
                \App\Helpers\TagsHelper::attachPostedTags(request()->get('tags'), $post);

                //insert interest
                if (!request()->has('id')) {
                    $tags = $post->tags;
                } else {
                    $post->load('tags');
                    $tags = \App\Helpers\TagsHelper::compareTags($oldTags, $post->tags);
                }

                if ($post->tags != null) {
                    event(new InterestAction(auth()->guard('web')->user(), $post->tags, 'post.create'));
                }

                //implement post view to add or replace in newsfeed
                $data['viewContent'] = view(
                    'page.post-content',
                    ['post' => $post->posts()->first(), 'unitPost' => false]
                )->render();

                if ($data['modal']) {
                    $data['targetId'] = '#TEvent-'.class_basename($post->posts()->first()->author).'-'.$post->id;
                } else {
                    $data['targetId'] = '.feed-'.class_basename($post->author).'-'.$post->author_id;
                }

                $data['autoFireModal'] = view(
                    'posting.success',
                    ['successMessage' => 'TEvent.'.$successMessage]
                )->render();

                //reinit vars for empty form
                $post = new TEvent();
                $data['mediasIds'] = null;
                $data['linksIds'] = null;
            }
        } elseif (request()->has('id') || $post_id != null) { // creating form to update existing post
            $id_post = (request()->has('id')) ? request()->has('id') : $post_id;
            $post = TEvent::findOrFail($id_post);
            if (!$this->Acl->getRights($post->posts[0]->author_type, $post->posts[0]->author_id)) {
                return response(view('errors.403'), 403);
            }

            if ($post->time == null) {
                $post->all_day = 1;
            }

            // transform times to local time
            if ($post->start_date != null) {
                $dateLocal = \App\Helpers\DateHelper::convertFromLocalUTC($post->start_date);
                $post->start_date = $dateLocal['datetime'];
                $post->time = $dateLocal['time'];
            }

            if ($post->end_date != null) {
                $dateLocal = \App\Helpers\DateHelper::convertFromLocalUTC($post->end_date);
                $post->end_date = $dateLocal['datetime'];
                $post->time_end = $dateLocal['time'];
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
            $post = new TEvent();
            $post->default_author = new \stdClass();
            $post->default_author->author_id = request()->get('default_author_id');
            $post->default_author->author_type = request()->get('default_author_type');
        }

        $data['post'] = $post;
        $data['sub_view'] = $sub_view;

        return $data;
    }

    private function offer($post_id = null)
    {
        $sub_view = 'offer';
        $data = [];
        $data['modal'] = request()->get('modal');
        $data['return_code'] = 'success';
        $data['return_message'] = '';
        if (request()->has('hideControls')) {
            $data['hideControls'] = true;
        }

        $offersTypeChoice = config('netframe.offersTypeChoice');
        $fieldsOffer = config('validation.offer');
        $data['offersType'] = config('netframe.offersType');
        $data['offersTypeChoice'] = $offersTypeChoice;
        $data['multiSelect'] = 'single';
        $data['typeType'] = 'type';

        if (request()->has('_token')) { //updating existing element
            $validator = validator(request()->all(), $fieldsOffer);
            // send back error message for validate form
            $data['errors'] = $validator->messages();
            $data['mediasIds'] = request()->get('mediasIds');
            $data['linksIds'] = request()->get('linksIds');
            $confidentiality = (request()->has('confidentiality')) ? request()->get('confidentiality') : 1;

            if ($validator->fails()) {
                $post = new Offer();
                foreach ($fieldsOffer as $field => $attributes) {
                    $post->$field = request()->get($field);
                }
                $post->id = (request()->has('id')) ? request()->get('id') : null;

                if (!empty(request()->get('placeSearch'))) {
                    $data['display_map'] = true;
                }

                $post->id_foreign_as = request()->get('id_foreign_as');
                $post->type_foreign_as = studly_case(request()->get('type_foreign_as'));
                $post->formTags = \App\Helpers\TagsHelper::getFromForm(request()->get('tags'));
                $post->formTagsSelecteds = request()->get('tags');
                $post->disable_comments = request()->get('disable_comments');
                $post->confidentiality = $confidentiality;
                $post->offer_type = request()->get('offer_type');

                $post->default_author = new \stdClass();
                $post->default_author->author_id = request()->get('id_foreign');
                $post->default_author->author_type = request()->get('type_foreign');
                $post->true_author = new \stdClass();
                $post->true_author->author_id = request()->get('id_foreign_as');
                $post->true_author->author_type = request()->get('type_foreign_as');

                $data['return_code'] = 'error';
                $data['offer'] = $post;
            } else {
                $oldMediasList = [];

                if (request()->has('id')) {
                    $post = Offer::findOrFail(request()->get('id'));
                    if (!$this->Acl->getRights($post->posts[0]->author_type, $post->posts[0]->author_id)) {
                        return response(view('errors.403'), 403);
                    }
                    $data['return_message'] = $successMessage = 'successUpdate';
                    $idNewsFeed = request()->get('id');

                    $oldTags = $post->tags;

                    foreach ($post->medias as $media) {
                        $oldMediasList[] = $media->id;
                    }
                } else {
                    if (!$this->Acl->getRights(request()->get('type_foreign'), request()->get('id_foreign'), 4)) {
                        return response(view('errors.403'), 403);
                    }
                    $post = new Offer();
                    $data['return_message'] = $successMessage = 'successInsert';
                    $idNewsFeed = null;
                    $data['newPost'] = true;
                }

                //check if private profile to set confidentiality to private
                if (in_array(request()->get('type_foreign'), ['house', 'project', 'community'])) {
                    $profileModel = Profile::gather(request()->get('type_foreign'));
                    $profile = $profileModel::find(request()->get('id_foreign'));
                    if ($profile->confidentiality == 0) {
                        $confidentiality = 0;
                    }
                }

                $post->users_id = auth()->guard('web')->user()->id;
                $post->instances_id = session('instanceId');
                $post->author_id = request()->get('id_foreign_as');
                $post->author_type = "App\\".studly_case(request()->get('type_foreign_as'));
                if (request()->has('disable_comments')) {
                    $post->disable_comments = request()->get('disable_comments');
                } else {
                    $post->disable_comments = 0;
                }

                foreach ($fieldsOffer as $field => $attributes) {
                    if ($field == 'placeSearch') {
                        $post->location = request()->get($field);
                    } else {
                        $post->$field = htmlentities(request()->get($field));
                    }
                }

                if (request()->get('start_at') == '') {
                    unset($post->start_at);
                }
                if (request()->get('stop_at') == '') {
                    unset($post->stop_at);
                }
                $post->save();
                $post->confidentiality = $confidentiality;



                //insert interest
                if (!request()->has('id')) {
                    $tags = $post->tags;
                } else {
                    $post->load('tags');
                    $tags = \App\Helpers\TagsHelper::compareTags($oldTags, $post->tags);
                }

                if ($post->tags != null) {
                    event(new InterestAction(auth()->guard('web')->user(), $post->tags, 'post.create'));
                }

                // Save the medias
                $medias = \App\Helpers\StringHelper::toArray(request()->get('mediasIds'));
                $post->medias()->detach();

                //detach old medias
                foreach ($oldMediasList as $oldMedia) {
                    $post->author->medias()->detach($oldMedia);
                }

                $mediaFolder = $post->author->getDefaultFolder('__posts_medias');

                foreach ($medias as $mediaId) {
                    $post->medias()->attach($mediaId);
                    if ($mediaFolder != null) {
                        $post->author->medias()->attach($mediaId, ['medias_folders_id' => $mediaFolder]);
                    }
                }
                //place media linked state
                Media::whereIn('id', $medias)->update(['linked' => 1, 'confidentiality'=> $confidentiality ]);

                // Save the tags for medias
                $mediasObj = Media::whereIn('id', $medias)->get();
                foreach ($mediasObj as $media) {
                    \App\Helpers\TagsHelper::attachPostedTags(request()->get('tags'), $media);
                }

                // save the links
                $links = \App\Helpers\StringHelper::toArray(request()->get('linksIds'));
                $post->links()->detach();

                foreach ($links as $linkId) {
                    $post->links()->attach($linkId);
                }

                // Run event register in table news_feeds
                $post->author_id = request()->get('id_foreign');
                $post->author_type = "App\\".studly_case(request()->get('type_foreign'));
                $post->true_author_id = request()->get('id_foreign_as');
                $post->true_author_type = "App\\".studly_case(request()->get('type_foreign_as'));
                event(new NewPost("Offer", $post, $idNewsFeed, $medias, $oldMediasList));
                event(new CheckUserTag($post, $post->content));

                // Save the tags
                \App\Helpers\TagsHelper::attachPostedTags(request()->get('tags'), $post);

                //implement post view to replace in newsfeed
                $data['viewContent'] = view(
                    'page.post-content',
                    ['post' => $post->posts()->first(), 'unitPost' => false]
                )->render();
                if ($data['modal']) {
                    $data['targetId'] = '#Offer-' . class_basename($post->posts()->first()->author) . '-' . $post->id;
                } else {
                    $data['targetId'] = '.feed-' . class_basename($post->author) . '-' . $post->author_id;
                }

                $data['autoFireModal'] = view(
                    'posting.success',
                    ['successMessage' => 'Offer.' . $successMessage]
                )->render();

                //reinit vars for empty form
                $post = new Offer();
                $data['mediasIds'] = null;
                $data['linksIds'] = null;
            }
        } elseif (request()->has('id') || $post_id != null) { // creating form to update existing post
            $id_post = (request()->has('id')) ? request()->has('id') : $post_id;
            $post = Offer::findOrFail($id_post);
            if (!$this->Acl->getRights($post->posts[0]->author_type, $post->posts[0]->author_id)) {
                return response(view('errors.403'), 403);
            }

            /*
            foreach($offersTypeChoice as $choice=>$type){
                if( in_array($post->offer_type, $type) ){
                    $post->offer_type = $choice;
                }
            }
            */

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
            $post = new Offer();
            $post->default_author = new \stdClass();
            $post->default_author->author_id = request()->get('default_author_id');
            $post->default_author->author_type = request()->get('default_author_type');
        }

        $data['post'] = $post;
        $data['offer'] = $post;
        $data['sub_view'] = $sub_view;

        return $data;
    }
}
