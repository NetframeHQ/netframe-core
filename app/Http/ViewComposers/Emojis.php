<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\EmojisGroup;

class Emojis
{


    public function __construct()
    {
    }

    public function compose(View $view)
    {
        $getData = $view->getData();
        $additionalClass = (isset($getData['additionalClass'])) ? $getData['additionalClass'] : '';

        $fieldTarget = $getData['emojiTarget'];

        //$emojisGroups = EmojisGroup::orderBy('order')->with('emojis')->get();
        $emojisGroups = EmojisGroup::orderBy('order')->get();
        try {
            $emojisFile = file(storage_path('emojis.json'));
            $emojis = json_decode($emojisFile[0], true);
        } catch (\Exception $e) {
            $emojis = [];
        }

        $currentGroup = rand();

        return $view->with('emojisGroups', $emojisGroups)
                    ->with('emojis', $emojis)
                    ->with('fieldTarget', $fieldTarget)
                    ->with('currentGroup', $currentGroup)
                    ->with('additionalClass', $additionalClass);
    }
}
