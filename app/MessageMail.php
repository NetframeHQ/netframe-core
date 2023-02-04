<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageMail extends Model
{

    protected $table = "messages_mail";

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

    public function messageGroup()
    {
        //return $this->hasOne('MessageGroup', 'id', 'messages_mail_group_id');
        return $this->belongsTo('App\MessageGroup');
    }

    public function offer()
    {
        //return $this->hasOne('Offer', 'id', 'offers_id');
        return $this->belongsTo('App\Offer', 'offers_id', 'id');
    }

    public static function getUnreadNotification()
    {
       /*
        $result = MessageMail::getMessages(0);
       if($result){
           return count($result);
       } else {
           return 0;
       }
       */
        if (session('acl') !== null) {
            $query = MessageMail::where('read', '=', 0);
            $query->where(function ($query2) {
                foreach (session('acl') as $profile => $ids) {
                    foreach ($ids as $idProfile => $role) {
                        if ($role < 3) {
                            $query2->orWhere(function ($query3) use ($idProfile, $profile) {
                                $query3->where('messages_mail.receiver_id', '=', $idProfile)
                                ->where('messages_mail.receiver_type', '=', "App\\".ucfirst($profile));
                            });
                        }
                    }
                }
            })
            ->leftJoin('messages_mail_group as mmg', 'mmg.id', '=', 'messages_mail.messages_mail_group_id');
            //->where('mmg.type', '!=', '5');

            $result = $query->count();
            return($result);
        } else {
            return 0;
        }
    }

    public static function hasMessages()
    {
        if (session('acl') !== null) {
            $query = MessageMail::where(function ($query2) {
                foreach (session('acl') as $profile => $ids) {
                    foreach ($ids as $idProfile => $role) {
                        if ($role < 3) {
                            $query2->orWhere(function ($query3) use ($idProfile, $profile) {
                                $query3->where('messages_mail.receiver_id', '=', $idProfile)
                                ->where('messages_mail.receiver_type', '=', "App\\".ucfirst($profile));
                            });
                        }
                    }
                }
            })
            ->leftJoin('messages_mail_group as mmg', 'mmg.id', '=', 'messages_mail.messages_mail_group_id');
            //->where('mmg.type', '!=', '5');

            return ($query->count() > 0) ? true : false;
        } else {
            return false;
        }
    }

    public static function getMessages($read = 1, $groupMessage = null)
    {
        if (session('acl') !== null) {
            $query = MessageMail::where('read', '<=', $read);
            $query->where(function ($query2) use ($groupMessage) {
                if (null === $groupMessage) {
                    foreach (session('acl') as $profile => $ids) {
                        foreach ($ids as $idProfile => $role) {
                            if ($role < 3) {
                                $query2->orWhere(function ($query3) use ($idProfile, $profile) {
                                    $query3->where('receiver_id', '=', $idProfile)
                                    ->where('receiver_type', '=', $profile);
                                });
                            }
                        }
                    }
                } else {
                    $query2->where('messages_mail_group_id', '=', $groupMessage->id);
                }
            });

            $query->orderBy('updated_at', 'desc');
            $result = $query->get();
            return($result);
        } else {
            return false;
        }
    }

    public function getLast()
    {
    }
}
