<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;

/**
 *
 * @author julien
 *
 *
 */
class OfferController extends BaseController
{

    public function __construct()
    {
        $this->middleware('checkAuth');
        parent::__construct();
    }

    public function searchOffer()
    {
        $keywords = trim(request()->get('keywords'));
        $offerType = request()->get('offer_type');
        $distance = request()->get('distance');
        $lat = request()->get('latitude');
        $lng = request()->get('longitude');
        if ($lat == '' && $lng == '') {
            $lat = session("lat");
            $lng = session("lng");
            $distance = 35000;
        }

        if ($keywords != '') {
            $against = str_replace(' ', '*', '*'.$keywords.'*');
            $compareRank = '>';
        } else {
            $against = '';
            $compareRank = '>=';
        }

        $offers = Offer::select(array(
                \DB::raw("offers.*"),
                \DB::raw("(2*(MATCH(name) AGAINST('" . $against . "' IN BOOLEAN MODE)) + (MATCH(content) AGAINST('"
                    . $against . "' IN BOOLEAN MODE))) AS `rank`")
            ))
            ->addSelect(
                \DB::raw('( 3959 * acos( cos( radians('
                    . $lat . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $lng
                    . ') ) + sin( radians(' . $lat . ') ) * sin( radians( latitude ) ) ) ) as truedistance')
            )
            ->where(function ($where) {
                if (request()->isMethod('POST') && request()->has('last_time')) {
                    $where->where('created_at', '<', request()->get('last_time'));
                }
            })
            ->where(function ($typeSearch) use ($offerType) {
                if (!empty($offerType)) {
                    if (is_array($offerType)) {
                        $typeSearch->whereIn('offer_type', $offerType);
                    } else {
                        $typeSearch->where('offer_type', '=', $offerType);
                    }
                }
            })
            ->where(function ($whereAgainst) use ($against) {
                if ($against != '') {
                    $whereAgainst->orWhereRaw("MATCH(name) AGAINST('".$against."' IN BOOLEAN MODE)")
                        ->orWhereRaw("MATCH(content) AGAINST('".$against."' IN BOOLEAN MODE)");
                }
            })
            ->having('truedistance', '<=', $distance)
            ->having('rank', $compareRank, '0')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $data = array();
        if (request()->has('last_time')) {
            $data['autoScroll'] = 1;
        } else {
            $data['autoScroll'] = 0;
        }
        $data['offers'] = $offers;

        return response()->json(array(
            'view' => $view = view('offers.offers-results', $data)->render(),
        ));
    }

    public function marketplace()
    {
        $offers = Offer::select()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $offersType = config('netframe.offersType');
        $offersTypeChoice = config('netframe.offersTypeChoice');

        $data = array();
        $data['apiKeyGoogle'] = config('external-api.googleApi.key');
        $data['searchDistance'] = 35000;
        $data['offers'] = $offers;
        $data['offersType'] = $offersType;
        $data['offersTypeChoice'] = $offersTypeChoice;
        $data['autoScroll'] = 0;

        return view('offers.marketplace', $data);
    }
}
