<?php

use App\Profile;
use App\Netframe;

/**
 * $array = list profiles of connected user
 * $inputTarget = array with name of hidden fields for id and type profile
 * $userPermit = true/false to disable user profile in list
 */
HTML::macro('publishAs', function (
    $targetForm,
    $arrayProfiles,
    $inputTarget,
    $userPermit,
    $oldProfile = null,
    $concernedProfile = null
) {
    if (isset($arrayProfiles['allProfiles'])) {
        $netframeProfiles = $arrayProfiles['allProfiles'];
    } else {
        $netframeProfiles = $arrayProfiles;
    }

    $dataForm = [
        'targetForm' => $targetForm,
        'profiles' => $netframeProfiles,
        'inputTarget' => $inputTarget,
        'oldProfile' => $oldProfile,
        'userPermit' => $userPermit,
        'housePermit' => true,
        'communityPermit' => true,
        'projectPermit' => true,
    ];

    if (isset($arrayProfiles['userChannels'])) {
        $dataForm['channels'] = $arrayProfiles['userChannels'];
    }
    if ($oldProfile != null) {
        $oldProfileClass = Profile::gather($oldProfile['type']);
        $oldProfileObject = $oldProfileClass::find($oldProfile['id']);
        $dataForm['oldProfileObject'] = $oldProfileObject;

        /*
        if(session('godMode')){
            // override profiles with profile creator informations
            -->> need to implement user owner of element
            $dataForm['profiles'] = Netframe::getProfiles($oldProfileObject->user_id);
        }
        */
    }

    if ($concernedProfile != null) {
        if (session()->has('profileAuth.'.$concernedProfile.'By')) {
            foreach (session('profileAuth.'.$concernedProfile.'By') as $asBy => $autorization) {
                $dataForm[$asBy.'Permit'] = $autorization;
            }
        }
    }

    return view('macros.publish-as', $dataForm)->render();
});

Form::macro('date', function ($name, $value, $class = '', $id = '') {
    $html = '<input type="date" name="'.$name.'" value="'.$value.'" class="'.$class.'" id="'.$id.'" />';
    return $html;
});

Form::macro('time', function ($name, $value, $class = '', $id = '') {
    $html = '<input type="time" name="'.$name.'" value="'.$value.'" class="'.$class.'" id="'.$id.'" />';
    return $html;
});

/**
 * Macro to display a datepicker input.
 */
Form::macro('datepicker', function ($name, $value = null) {

    if ($value instanceof \DateTime) {
        $value = $value->format('m/d/Y');
    }

    return <<<EOD
<div class="input-group date">
  <input type="text" class="form-control" value="$value" name="$name">
  <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
</div>
EOD;
});

    /**
     * Macro to display a timepicker input.
     */
    Form::macro('timepicker', function ($name, $value = null) {

        if ($value instanceof \DateTime) {
            $value = $value->format('H:i');
        }

        return <<<EOD
<div class="input-group clockpicker">
    <input type="text" class="form-control" value="$value" name="$name">
    <span class="input-group-addon">
        <span class="glyphicon glyphicon-time"></span>
    </span>
</div>
EOD;
    });
