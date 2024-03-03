<?php

namespace App\Helpers;

use App\User;

class StatsHelper
{
    public static function computeTopusers($topUsersComputes)
    {
        $topUsers = [];
        foreach ($topUsersComputes as $topUsersCompute) {
            foreach ($topUsersCompute as $topUserCompute) {
                if (!isset($topUsers[$topUserCompute->users_id])) {
                    $topUsers[$topUserCompute->users_id] = 0;
                }
                $topUsers[$topUserCompute->users_id] += $topUserCompute->score;
            }
        }
        // get 10 firsts users
        $topUsers = array_slice(array_keys($topUsers), 0, 10);
        $returnTopusers = [];
        foreach ($topUsers as $user_id) {
            $returnTopusers[$user_id] = User::find($user_id);
        }
        return $returnTopusers;
    }
}
