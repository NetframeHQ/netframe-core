<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageGroup extends Model
{

    protected $table = "messages_mail_group";

    protected $fillable = [];


    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    /**
     * morph relation with message author (profile)
     */
    public function sender()
    {
        return $this->morphTo();
    }

    /**
     * morph relation with message receveiver (profile)
     */
    public function receiver()
    {
        return $this->morphTo();
    }

    /**
     * relation with messages
     */
    public function messages()
    {
        return $this->hasMany('App\MessageMail', 'messages_mail_group_id', 'id');
    }

    /**
     * check or create new group
     */
    public static function checkOrCreate($message, $typeMessage)
    {
        $checkGroup = MessageGroup::select()
            ->where('instances_id', '=', session('instanceId'))
            ->where(function ($wg) use ($message, $typeMessage) {
                $wg->orWhere(function ($query2) use ($message, $typeMessage) {
                    $query2->where('receiver_id', '=', $message->receiver_id)
                    ->where('receiver_type', '=', $message->receiver_type)
                    ->where('sender_id', '=', $message->sender_id)
                    ->where('sender_type', '=', $message->sender_type)
                    ->where('type', '=', $typeMessage);
                })
                ->orWhere(function ($query2) use ($message, $typeMessage) {
                    $query2->where('receiver_id', '=', $message->sender_id)
                    ->where('receiver_type', '=', $message->sender_type)
                    ->where('sender_id', '=', $message->receiver_id)
                    ->where('sender_type', '=', $message->receiver_type)
                    ->where('type', '=', $typeMessage);
                });
            });

        if ($checkGroup->count() == 0) {
            $newGroup = new MessageGroup();
            $newGroup->instances_id = session('instanceId');
            $newGroup->sender_id = $message->sender_id;
            $newGroup->sender_type = $message->sender_type;
            $newGroup->receiver_id = $message->receiver_id;
            $newGroup->receiver_type = $message->receiver_type;
            $newGroup->type = $typeMessage;
            $newGroup->save();

            return $newGroup;
        } else {
            //update date group
            $msgGroup = $checkGroup->get()->first();
            $msgGroup->updated_at = date('Y-m-d H:i:s');
            $msgGroup->save();

            return $msgGroup;
        }
    }

    /**
     * return all mails feed of a user and his profiles
     */
    public static function getGroups($beforeDate = null)
    {
        $listMails = MessageGroup::select()
            ->where('instances_id', '=', session('instanceId'))
            ->where(function ($where) {
                foreach (session('acl') as $profile => $ids) {
                    foreach ($ids as $idProfile => $role) {
                        if ($role < 3) {
                            $where->orWhere(function ($query2) use ($idProfile, $profile) {
                                $query2->where('receiver_id', '=', $idProfile)
                                ->where('receiver_type', '=', 'App\\'.$profile);
                            });
                            $where->orWhere(function ($query2) use ($idProfile, $profile) {
                                $query2->where('sender_id', '=', $idProfile)
                                ->where('sender_type', '=', 'App\\'.$profile);
                            });
                        }
                    }
                }
            })
            ->where(function ($whereD) use ($beforeDate) {
                if ($beforeDate != null) {
                    $whereD->where('updated_at', '<', $beforeDate);
                }
            })
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get();

        return $listMails;
    }
}
