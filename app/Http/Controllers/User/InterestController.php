<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Interest;
use App\Community;
use App\House;
use App\Project;

/**
 *
 * @author julien
 *
 *  Controller for messages Ajax processing in application
 *
 */
class InterestController extends BaseController
{

    public function __construct()
    {
        $this->middleware('checkAuth');
    }

    /**
     * Manage interests
     *
     */
    public function settings()
    {
        if (request()->isMethod('POST')) {
            $validator = validator(request()->all(), config('validation.interests/add'));

            if ($validator->fails()) {
                //return $View->withErrors($validator);
                $jsonData = array();
                $jsonData['edit'] = false;
                $jsonData['error'] = trans('interests.selectCat');
                return response()->json($jsonData);
            } else {
                $interest = new Interest();
                $interest->users_id = auth()->guard('web')->user()->id;
                $interest->ref_subjects_id = intval(request()->get('subject'));
                $interest->ref_categories_id = intval(request()->get('category'));
                $interest->weight = '5';
                $new = $interest->addInterest(1.2);

                //implement new interest in interest session
                if ($new) {
                    $jsonData = array();
                    $jsonData['edit'] = true;
                    $jsonData['newInterest'] = $this->getViewUnitInterest($interest);
                    $jsonData['newSuggests'] = $this->getViewSuggests();
                    return response()->json($jsonData);
                }
            }
        }

        $data = array();
        $interests = Interest::where('users_id', '=', auth()->guard('web')->user()->id)
            ->orderBy('weight', 'desc')
            ->take(12)
            ->get();
        $data['interests'] = $interests;
        $data['discover'] = (count($interests) > 0) ? $data['discover'] = $this->getProfiles($interests) : '';

        return view('interests.settings', $data);
    }

    /**
     * Get View for a new interest
     *
     * @return HTML View interests/unitInterest.blade.php
     */
    public function getViewUnitInterest($interest)
    {
        $data = array();
        $data['interest'] = $interest;
        return view('interests.unit-interest', $data)->render();
    }

     /**
     * Get View for reload suggests after adding new interest
     *
     * @return HTML View interests/unitInterest.blade.php
     */
    public function getViewSuggests()
    {
        $data = array();
        $data['discover'] = $this->getProfiles(
            Interest::where('users_id', '=', auth()->guard('web')->user()->id)
            ->orderBy('weight', 'desc')
            ->take(12)
            ->get()
        );
        return view('interests.suggests', $data)->render();
    }

    /**
     *
     * @param int $interestId
     * @throws AccessDeniedException
     * simulate delete with a very low weight
     */
    public function delete($interestId)
    {
        $interest = Interest::findOrFail($interestId);

        if ($interest->users_id !== auth()->guard('web')->user()->id) {
            throw new AccessDeniedException();
        }
        $interest->weight = 0.5;
        $interest->save();

        return response()->json(array(
            'delete' => true,
            'targetId' => "#interest-".$interestId
        ));
    }

    public function getProfiles($interests)
    {
        $result = array();

        $queryProjects = Project::select()->where('active', '=', 1);
        foreach ($interests as $interest) {
            $queryProjects->orWhere(function ($query) use ($interest) {
                $query->where('ref_subjects_id', '=', $interest->ref_subjects_id)
                ->Where('ref_categories_id', '=', $interest->ref_categories_id);
            });
        }
        $result['project'] = $queryProjects->take(12)->get();

        $queryHouses = House::select()->where('active', '=', 1);
        foreach ($interests as $interest) {
            $queryHouses->orWhere(function ($query) use ($interest) {
                $query->where('ref_subjects_id', '=', $interest->ref_subjects_id)
                ->Where('ref_categories_id', '=', $interest->ref_categories_id);
            });
        }
        $result['house'] = $queryHouses->take(12)->get();

        $queryCommunity = Community::select()->where('active', '=', 1);
        foreach ($interests as $interest) {
            $queryCommunity->orWhere(function ($query) use ($interest) {
                $query->where('ref_subjects_id', '=', $interest->ref_subjects_id)
                ->Where('ref_categories_id', '=', $interest->ref_categories_id);
            });
        }
        $result['community'] = $queryCommunity->take(12)->get();

        return $result;
    }

    public static function convertToIdArray($interests)
    {
        $array = array();
        foreach ($interests as $interest) {
            $array[] = $interest->ref_categories_id;
        }
        return $array;
    }
}
