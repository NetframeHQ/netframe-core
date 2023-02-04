<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\EmojisGroup;

class EmojisController extends BaseController
{
    public function list()
    {
        $emojisGroups = EmojisGroup::orderBy('order')->with('emojis')->get();

        $data = [];
        $data['emojis'] = $emojisGroups;
        return json_encode($emojisGroups);
    }

    public function emojis()
    {

        $emojisFile = file(storage_path('emojis.json'));
        $emojis = json_decode($emojisFile[0], true);

        $data = [];
        $data['emojis'] = $emojis[request()->get('groupId')];

        return response()->json($data);

        /*
        $groupId = request()->get('groupId');
        $group = EmojisGroup::findOrFail($groupId);
        $emojis = $group->emojis()->select('value')->get();

        $data = [];
        $data['emojis'] = $emojis;
        return response()->json($data);
        */
    }
}
