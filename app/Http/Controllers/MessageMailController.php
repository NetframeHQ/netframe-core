<?php
namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Predis\Session\SessionHandler;
use AppRepository\TypePost;
use App\MessageMail;
use App\MessageGroup;

/**
 *
 *
 *  Controller for messages Ajax processing in application
 *
 */
class MessageMailController extends BaseController
{
    public function __construct()
    {
        $this->middleware('checkAuth');
        parent::__construct();
    }

    /**
     * Get all messages page
     * @return string content HTML
     */
    public function inbox()
    {
        $data = array();

        //get all messages for user

        $messagesGroups = MessageGroup::getGroups();

        $tabMessages = array();

        foreach ($messagesGroups as $group) {
            $groupMail = new \stdClass();

            foreach (session('acl') as $profile => $ids) {
                foreach ($ids as $idProfile => $role) {
                    if ($group->sender_id == $idProfile && $group->sender_type == 'App\\'.studly_case($profile)) {
                        $groupMail->profile_foreign = $group->receiver;
                        $groupMail->profile_to = $group->sender;
                    } else {
                        $groupMail->profile_foreign = $group->sender;
                        $groupMail->profile_to = $group->receiver;
                    }
                }
            }

            //$groupMail->messages = MessageMail::getMessages(1,$group)->first();

            $groupMail->lastMessages = $group->messages()->orderBy('created_at', 'desc')->take(1)->first();
            $groupMail->type = $group->type;
            $groupMail->updated_at = $group->updated_at;
            //$groupMail->unread = MessageMail::getMessages(0,$group)->count();
            $groupMail->unread = MessageMail::where('instances_id', '=', session('instanceId'))
                ->where('messages_mail_group_id', '=', $group->id)
                ->where('receiver_type', '=', get_class($groupMail->profile_to))
                ->where('receiver_id', '=', $groupMail->profile_to->id)
                ->where('read', '=', '0')
                ->count();
            $tabMessages[] = $groupMail;
            unset($groupMail);
        }

        $data['messagesGroups'] = $tabMessages;
        $data['profile'] = auth()->guard('web')->user();
        $data['types'] = config('messages.types');

        return view('messages.inbox', $data);
    }

    public function infinitebox()
    {
        $data = array();

        //get all messages for user

        $messagesGroups = MessageGroup::getGroups(request()->get('last_time'));
        $tabMessages = array();

        foreach ($messagesGroups as $group) {
            $groupMail = new \stdClass();

            foreach (session('acl') as $profile => $ids) {
                foreach ($ids as $idProfile => $role) {
                    if ($group->sender_id == $idProfile && $group->sender_type == studly_case($profile)) {
                        $groupMail->profile_foreign = $group->receiver;
                        $groupMail->profile_to = $group->sender;
                    } else {
                        $groupMail->profile_foreign = $group->sender;
                        $groupMail->profile_to = $group->receiver;
                    }
                }
            }

            //$groupMail->messages = MessageMail::getMessages(1,$group)->first();

            $groupMail->lastMessages = $group->messages()->orderBy('created_at', 'desc')->take(1)->first();
            $groupMail->type = $group->type;
            $groupMail->updated_at = $group->updated_at;
            //$groupMail->unread = MessageMail::getMessages(0,$group)->count();
            $groupMail->unread = MessageMail::where('instances_id', '=', session('instanceId'))
                ->where('messages_mail_group_id', '=', $group->id)
                ->where('receiver_type', '=', get_class($groupMail->profile_to))
                ->where('receiver_id', '=', $groupMail->profile_to->id)
                ->where('read', '=', '0')
                ->count();
            $tabMessages[] = $groupMail;
            unset($groupMail);
        }

        $data['messagesGroups'] = $tabMessages;

        $data['types'] = config('messages.types');

        return response()->json([
            'view' => view('messages.partials.feed-list', $data)->render(),
        ]);
    }

    /**
     * get sent messages of a user
     * @param unknown $onlySent
     */
    public function outbox()
    {

        $data = array();
        $tabMessages = array();

        foreach (session('acl') as $profile => $ids) {
            foreach ($ids as $idProfile => $role) {
                $messages = MessageMail::where('instances_id', '=', session('instanceId'))
                    ->where('sender_id', '=', $idProfile)
                    ->where('sender_type', '=', studly_case($profile))
                    ->get();
                foreach ($messages as $message) {
                    $tabMessages[] = ['msgDate' => $message->created_at, 'msgObject' => $message];
                }
            }
        }
        rsort($tabMessages);

        $data['messages'] = $tabMessages;

        return view('messages.outbox', $data);
    }

    /**
     * Get Form HTML for message post
     *
     * @var string $pageType
     * @var int $id
     * @var int $type 1: normal message, 2 : donate message
     * @return string content HTML
     *
     */
    public function getFormMessage($type_to, $id_to, $type_from = 'user', $id_from = null, $type = 1)
    {
        // Variable $data for storage variable send to template
        $data = array();

        //if (!in_array($pageType, config('netframe.list_profile'))) {
        /*
        if(!$this->Acl->getRights($pageType, $id)){
            return response(view('errors.403'), 403);
        }
        */

        $data['idForeignTo'] = $id_to;
        $data['typeForeignTo'] = $type_to;
        $data['idForeignFrom'] = ($id_from != null) ? $id_from : auth()->guard('web')->user()->id;
        $data['typeForeignFrom'] = $type_from;
        $data['types'] = config('messages.types');
        $data['type'] = $type;
        $data['overrideType'] = 0;
        $data['offerId'] = (request()->has('offerId')) ? request()->get('offerId') : 0;

        return view('messages.form-post', $data);
    }

    /**
     * Get Form HTML for message post for answers
     *
     * @var string $pageType
     * @var int $id
     * @var int $type 1: normal message, 2 : donate message
     * @return string content HTML
     */
    public function getFormMessageAnswer($type_to, $id_to, $type_from = 'user', $id_from = '', $type = 1)
    {
        // Variable $data for storage variable send to template
        $data = array();

        //if (!in_array($pageType, config('netframe.list_profile'))) {
        /*
         if(!$this->Acl->getRights($pageType, $id)){
            return response(view('errors.403'), 403);
         }
        */

        $data['idForeignTo'] = $id_to;
        $data['typeForeignTo'] = $type_to;
        $data['idForeignFrom'] = $id_from;
        $data['typeForeignFrom'] = $type_from;
        $data['types'] = config('messages.types');
        $data['type'] = $type;
        $data['overrideType'] = 2;
        $data['offerId'] = (request()->has('offerId')) ? request()->get('offerId') : 0;

        return view('messages.form-post', $data);
    }

    /**
     *  Form send publish post in ajax
     *
     */
    public function postMessagePost()
    {
        if (auth()->guard('web')->check()) {
            $data = array();

            // Variable needed for display page
            $data['receiver_id'] = request()->get('receiver_id');
            $data['receiver_type'] = request()->get('receiver_type');
            $data['type'] = request()->get('type');
            $data['overrideType'] = request()->get('overrideType');
            $data['offerId'] = request()->get('offerId');
            $data['types'] = config('messages.types');
            $data['inputOld'] = request()->all();


            /**
             *  @TODO include non blacklist verification
             *  */

            if (!$this->Acl->getRights(request()->get('sender_type'), request()->get('sender_id'))) {
                return response(view('errors.403'), 403);
            }

            $validator = validator(request()->all(), config('validation.message/newsMessage'));

            if ($validator->fails()) {
                // send back error message for validate form

                $data['errors'] = $validator->messages();
                if (request()->has('feedId')) {
                    //Return redirect()->route('messages_feed',['feedId'=>request()->get('feedId')]);
                    $data['feedId'] = request()->get('feedId');
                    return response()->json(array(
                        'formView' => view('messages.form-answer', $data)->render(),
                    ));
                } else {
                    return response()->json(array(
                        'view' => view('messages.form-post', $data)->render(),
                    ));
                }
            } else {
                // Success post message
                $message = new MessageMail();

                $message->instances_id = session('instanceId');
                $message->users_id = auth()->guard('web')->user()->id;
                $message->receiver_id = request()->get('receiver_id');
                $message->receiver_type = 'App\\'.studly_case(request()->get('receiver_type'));
                $message->content = htmlentities(request()->get('content'));
                $message->offers_id = $data['offerId'];
                $message->sender_id = request()->get('sender_id');
                $message->sender_type = 'App\\'.studly_case(request()->get('sender_type'));
                /*
                if (request()->get('type') == 5 || request()->get('type') == 6) {
                    $message->sender_id = $message->receiver_id;
                    $message->sender_type = $message->receiver_type;
                }
                */

                //check if message group exists
                $group = MessageGroup::checkOrCreate($message, $data['type']);
                if (request()->get('type') == 5 || request()->get('type') == 6) {
                    $message->sender_id = request()->get('sender_id');
                    $message->sender_type = 'App\\'.studly_case(request()->get('sender_type'));
                }

                $message->messages_mail_group_id = $group->id;
                $message->save();

                //mark as read all messages of feed
                /*
                $messages = MessageMail::where('instances_id', '=', session('instanceId'))
                    ->where('messages_mail_group_id', '=', $group->id)
                    ->where('receiver_id', '=', request()->get('sender_id'))
                    ->where('receiver_type', '=', 'App\\'.studly_case(request()->get('sender_type')))
                    ->update(['read' => 1]);
                */

                $data['message'] = $message;

                if (request()->has('feedId')) {
                    $data['feedId'] = request()->get('feedId');
                    $data['inputOld']['content'] = '';

                    return response()->json([
                        'success' => true,
                        'formView' => view('messages.form-answer', $data)->render(),
                        'messageView' => view('messages.partials.unit-message', $data)->render()
                    ]);
                } else {
                    return response()->json(array(
                        'view' => view('messages.form-post-success', $data)->render(),
                        'waitCloseModal' => '2000'
                    ));
                }
            }
        }
    }

    public function getAllFeed($feedId, $lastId = 0)
    {
        $data = array();
        $nbDisplayed = config('messages.nbDisplayed');

        $feedInit = MessageMail::where('messages_mail_group_id', '=', $feedId)
                ->orderBy('created_at', 'asc');

        if ($lastId == 0) {
            $totalMessages = $feedInit->count();
            $skip = ($totalMessages > $nbDisplayed) ? $totalMessages - $nbDisplayed : 0;
            $feed = $feedInit->skip($skip)->take($nbDisplayed)->get();
            $data['totalMessages'] = $totalMessages;
            $data['nbDisplayed'] = $nbDisplayed;
            $view = 'messages.feed-container';
        } else {
            $feed = $feedInit->where('id', '<', $lastId);
            $totalMessages = $feedInit->count();
            $skip = ($totalMessages > $nbDisplayed) ? $totalMessages - $nbDisplayed : 0;
            $feed = $feedInit->skip($skip)->take($nbDisplayed)->get();
            $view = 'messages.feed-messages';
        }

        // update read parameter
        MessageMail::where('messages_mail_group_id', '=', $feedId)
            ->where(function ($w) {
                foreach (session('acl') as $profile => $ids) {
                    foreach ($ids as $idProfile => $role) {
                        $w->orWhere(function ($ow) use ($profile, $idProfile) {
                            $ow->where('receiver_type', '=', 'App\\'.studly_case($profile))
                               ->where('receiver_id', '=', $idProfile);
                        });
                    }
                }
            })
            ->update(['read' => 1]);

        foreach (session('acl') as $profile => $ids) {
            $firstMessage = $feed;
            $myProfile = '';

            if (count($feed) > 0) {
                foreach ($ids as $idProfile => $role) {
                    if ($firstMessage->first()->sender_id == $idProfile
                        && $firstMessage->first()->sender_type == 'App\\'.studly_case($profile)) {
                        $id_profile_foreign = $firstMessage->first()->sender_id;
                        $type_profile_foreign = $firstMessage->first()->sender_type;
                        $id_profile_to = $firstMessage->first()->receiver_id;
                        $type_profile_to = $firstMessage->first()->receiver_type;

                        $data['profile_foreign'] = $firstMessage->first()->sender;
                        $data['profile_to'] = $firstMessage->first()->receiver;
                    } else {
                        $id_profile_foreign = $firstMessage->first()->receiver_id;
                        $type_profile_foreign = $firstMessage->first()->receiver_type;
                        $id_profile_to = $firstMessage->first()->sender_id;
                        $type_profile_to = $firstMessage->first()->sender_type;

                        $data['profile_foreign'] = $firstMessage->first()->receiver;
                        $data['profile_to'] = $firstMessage->first()->sender;
                    }

                    foreach ($feed as $message) {
                        if ($message->receiver_id == $idProfile && $message->receiver_type == studly_case($profile)) {
                            $message->read = 1;
                            $message->save();
                        }
                    }
                }
                $myProfile = get_class($data['profile_foreign']).'-'.$data['profile_foreign']->id;
            }
        }


        $offer = null;

        if (count($feed) > 0) {
            $data['idForeignTo'] = $data['profile_to']->id;
            $data['typeForeignTo'] = $data['profile_to']->getType();
            $data['idForeignFrom'] = $data['profile_foreign']->id;
            $data['typeForeignFrom'] = $data['profile_foreign']->getType();

            if (!empty($feed[0]->offers_id)) {
                $offer = $feed[0]->offer;
            }
        }

        //$data['profile_foreign'] = \Profile::gather($type_profile_foreign)->get()->find($id_profile_foreign);
        //$data['profile_to'] = \Profile::gather($type_profile_to)->get()->find($id_profile_to);
        $data['myProfile'] = $myProfile;
        $data['feed'] = $feed;
        $data['offer'] = $offer;
        $data['feedId'] = $feedId;
        $data['type'] = MessageGroup::find($feedId)->type;
        $data['overrideType'] = 2;
        $data['types'] = config('messages.types');

        //return view('messages.feed', $data);
        return response()->json(array(
            'view' => view($view, $data)->render(),
        ));
    }

    public static function listContacts()
    {
        $user = auth()->guard('web')->user();

        $contacts = array();
        $contact2 = array();
        $contactsUnique = array();

        //get friends
        $friends = $user->friendsList();
        foreach ($friends as $friend) {
            $contact = new \stdClass();
            $contact->type = 'User';
            $contact->id = $friend->id;
            $contact->display = $friend->getNameDisplay();

            if (!isset($contactsUnique[$contact->type][$contact->id])) {
                $contactsUnique[$contact->type][$contact->id] = 1;
                $contacts[] = $contact;
                $contact2[] = [ 'id'=> $contact->type.'-'.$contact->id, 'text' => $contact->display];
            }
        }

        //get subscriptions
        $subscriptions = $user->subscriptionsList()->get();
        foreach ($subscriptions as $subscription) {
            $profile = $subscription->profile;
            $contact = new \stdClass();
            $contact->type = get_class($profile);
            $contact->id = $profile->id;
            $contact->display = $profile->getNameDisplay();

            if (!isset($contactsUnique[$contact->type][$contact->id])) {
                $contactsUnique[$contact->type][$contact->id] = 1;
                $contacts[] = $contact;
                $contact2[] = [ 'id'=> $contact->type.'-'.$contact->id, 'text' => $contact->display];
            }
        }

        //get already contacted
        //get all messages for user
        $messagesGroups = MessageGroup::getGroups();
        $tabMessages = array();

        foreach ($messagesGroups as $group) {
            $groupMail = new \stdClass();

            foreach (session('acl') as $profile => $ids) {
                foreach ($ids as $idProfile => $role) {
                    if ($group->sender_id == $idProfile && $group->sender_type == studly_case($profile)) {
                        $contactMail = $group->receiver;
                    } else {
                        $contactMail = $group->sender;
                    }
                }
            }
            $contact = new \stdClass();
            $contact->type = get_class($contactMail);
            $contact->id = $contactMail->id;
            $contact->display = $contactMail->getNameDisplay();

            if (!isset($contactsUnique[$contact->type][$contact->id])) {
                $contactsUnique[$contact->type][$contact->id] = 1;
                $contacts[] = $contact;
                $contact2[] = [ 'id'=> $contact->type.'-'.$contact->id, 'text' => $contact->display];
            }
        }

        return json_encode($contact2);
    }

    /**
     * mark all mesages of user as read
     */
    public function markAllRead()
    {
        foreach (session('acl') as $profile => $ids) {
            foreach ($ids as $idProfile => $role) {
                $mesaggesRead = MessageMail::where('instances_id', '=', session('instanceId'))
                    ->where('receiver_id', '=', $idProfile)
                    ->where('receiver_type', '=', 'App\\'.studly_case($profile))
                    ->update(['read' => 1]);
            }
        }
        return redirect()->route('messages_inbox');
    }

    public function newMessage()
    {
        return response()->json([
            'view' => view('components.messages.new-with-list')->render(),
        ]);
    }
}
