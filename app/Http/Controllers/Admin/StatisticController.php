<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use \Carbon\Carbon;

class StatisticController extends Controller
{
    public function getMainStats()
    {
        if (!request()->has('end_date')) {
            $ref_date = Carbon::now();
            $end_date = $ref_date->toDateString();
            $ref_date->subMonth(1);
            $ref_date->toDateString();
            $start_date = $ref_date->toDateString();
        } else {
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
        }

        $data = [];

        //get daily stats
        $dailyStats = \TrackingReport::select('libelle as jour', 'value')
            ->where('libelle', '>=', $start_date)
            ->where('libelle', '<=', $end_date)
            ->where('period_type', '=', 'daily')
            ->where('type', '=', 'nb_user_jour')
            ->orderBy('libelle', 'desc')
            ->get();

        $data['subView'] = 'nb-conn-jour';
        $data['dailyStats'] = $dailyStats;
        $data['startDate'] = $start_date;
        $data['endDate'] = $end_date;

        return view('admin.statistics.home', $data);
    }

    public function getProfiles()
    {
        if (!request()->has('end_date')) {
            $ref_date = Carbon::now();
            $end_date = $ref_date->toDateString();
            $ref_date->subMonth(1);
            $ref_date->toDateString();
            $start_date = $ref_date->toDateString();
        } else {
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
        }

        $data = [];

        //get users stats
        $usersStats = \TrackingReport::select('libelle as period', 'value')
            ->where('libelle', '>=', $start_date)
            ->where('libelle', '<=', $end_date)
            ->where('period_type', '=', 'weekly')
            ->where('type', '=', 'nb_users')
            ->orderBy('period', 'desc')
            ->lists('value', 'period');

        //get users stats
        $talentsStats = \TrackingReport::select('libelle as period', 'value')
            ->where('libelle', '>=', $start_date)
            ->where('libelle', '<=', $end_date)
            ->where('period_type', '=', 'weekly')
            ->where('type', '=', 'nb_talents')
            ->orderBy('period', 'desc')
            ->lists('value', 'period');

        //make global stats
        $tabGlobalStats = [];
        foreach ($usersStats as $period => $value) {
            $periodLimits = explode('|', $period);
            $tabGlobalStats[] = [
                "period" => $period,
                "nbUsers" => $value,
                "nbTalents" => (isset($talentsStats[$period])) ? $talentsStats[$period] : '0',
            ];
        }

        $data['subView'] = 'profiles';
        $data['nbUsers'] = $usersStats;
        $data['nbTalents'] = $talentsStats;
        $data['profilesStatsGlobal'] = $tabGlobalStats;
        $data['startDate'] = $start_date;
        $data['endDate'] = $end_date;

        return view('admin.statistics.home', $data);
    }

    public function getRevisits()
    {
        if (!request()->has('periodType')) {
            $periodType = 'weekly';
        } else {
            $periodType = request()->get('periodType');
        }

        if (!request()->has('end_date')) {
            $ref_date = Carbon::now();
            $end_date = $ref_date->toDateString();
            $ref_date->subMonth(1);
            $ref_date->toDateString();
            $start_date = $ref_date->toDateString();
        } else {
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
        }

        $data = [];

        //get revisit stats
        $revisitStats1 = \TrackingReport::select('libelle as period', 'value')
            ->where('libelle', '>=', $start_date)
            ->where('libelle', '<=', $end_date)
            ->where('period_type', '=', $periodType)
            ->where('type', '=', 'nb_connect_1')
            ->orderBy('libelle', 'desc')
            ->lists('value', 'period');

        $revisitStats2 = \TrackingReport::select('libelle as period', 'value')
            ->where('libelle', '>=', $start_date)
            ->where('libelle', '<=', $end_date)
            ->where('period_type', '=', $periodType)
            ->where('type', '=', 'nb_connect_2')
            ->orderBy('libelle', 'desc')
            ->lists('value', 'period');

        $revisitStatsMore2 = \TrackingReport::select('libelle as period', 'value')
            ->where('libelle', '>=', $start_date)
            ->where('libelle', '<=', $end_date)
            ->where('period_type', '=', $periodType)
            ->where('type', '=', 'revisit_2')
            ->orderBy('libelle', 'desc')
            ->lists('value', 'period');

        $revisitStatsMore5 = \TrackingReport::select('libelle as period', 'value')
            ->where('libelle', '>=', $start_date)
            ->where('libelle', '<=', $end_date)
            ->where('period_type', '=', $periodType)
            ->where('type', '=', 'revisit_5')
            ->orderBy('libelle', 'desc')
            ->lists('value', 'period');

        //make global stats
        $tabGlobalStats = [];
        foreach ($revisitStatsMore2 as $period => $value) {
            $periodLimits = explode('|', $period);
            $tabGlobalStats[] = [
                "period" => $periodLimits[0],
                "1connexion" => (isset($revisitStats1[$period])) ? round($revisitStats1[$period]) : '0',
                "2connexion" => (isset($revisitStats2[$period])) ? round($revisitStats2[$period]) : '0',
                "2Mconnexion" => (isset($revisitStatsMore2[$period])) ? round($revisitStatsMore2[$period]) : '0',
                "5Mconnexion" => (isset($revisitStatsMore5[$period])) ? round($revisitStatsMore5[$period]) : '0'
            ];
        }

        $data['subView'] = 'revisit';
        $data['periodType'] = $periodType;
        $data['revisitStats1'] = $revisitStats1;
        $data['revisitStats2'] = $revisitStats2;
        $data['revisitStatsMore2'] = $revisitStatsMore2;
        $data['revisitStatsMore5'] = $revisitStatsMore5;
        $data['revisitStatsGlobal'] = $tabGlobalStats;
        $data['startDate'] = $start_date;
        $data['endDate'] = $end_date;

        return view('admin.statistics.home', $data);
    }

    public function getPostsMedias()
    {
        if (!request()->has('periodType')) {
            $periodType = 'weekly';
        } else {
            $periodType = request()->get('periodType');
        }

        if (!request()->has('end_date')) {
            $ref_date = Carbon::now();
            $end_date = $ref_date->toDateString();
            $ref_date->subMonth(1);
            $ref_date->toDateString();
            $start_date = $ref_date->toDateString();
        } else {
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
        }

        $data = [];

        //get media stats
        $mediaTotal = \TrackingReport::select('libelle as period', 'value')
            ->where('libelle', '>=', $start_date)
            ->where('libelle', '<=', $end_date)
            ->where('period_type', '=', $periodType)
            ->where('type', '=', 'medias_total')
            ->orderBy('libelle', 'desc')
            ->take(10)
            ->lists('value', 'period');

        $mediaXemeConn = \TrackingReport::select('libelle as period', 'value')
            ->where('libelle', '>=', $start_date)
            ->where('libelle', '<=', $end_date)
            ->where('period_type', '=', $periodType)
            ->where('type', '=', 'medias_total_after_first_conn')
            ->orderBy('libelle', 'desc')
            ->take(10)
            ->lists('value', 'period');

        //get posts stats
        $postTotal = \TrackingReport::select('libelle as period', 'value')
            ->where('libelle', '>=', $start_date)
            ->where('libelle', '<=', $end_date)
            ->where('period_type', '=', $periodType)
            ->where('type', '=', 'posts_total')
            ->orderBy('libelle', 'desc')
            ->take(10)
            ->lists('value', 'period');

        $postXemeConn = \TrackingReport::select('libelle as period', 'value')
            ->where('libelle', '>=', $start_date)
            ->where('libelle', '<=', $end_date)
            ->where('period_type', '=', $periodType)
            ->where('type', '=', 'posts_total_after_first_conn')
            ->orderBy('libelle', 'desc')
            ->take(10)
            ->lists('value', 'period');

        //make global stats
        $tabGlobalStats = [];
        foreach ($mediaTotal as $period => $value) {
            $periodLimits = explode('|', $period);
            $tabGlobalStats[] = [
                "period" => $periodLimits[0],
                "mediaTotal" => (isset($mediaTotal[$period])) ? round($mediaTotal[$period]) : '0',
                "mediaXemeConn" => (isset($mediaXemeConn[$period])) ? round($mediaXemeConn[$period]) : '0',
                "postTotal" => (isset($postTotal[$period])) ? round($postTotal[$period]) : '0',
                "postXemeConn" => (isset($postXemeConn[$period])) ? round($postXemeConn[$period]) : '0',
            ];
        }

        $data['subView'] = 'posts-medias';
        $data['periodType'] = $periodType;
        $data['mediaTotal'] = $mediaTotal;
        $data['mediaXemeConn'] = $mediaXemeConn;
        $data['postTotal'] = $postTotal;
        $data['postXemeConn'] = $postXemeConn;
        $data['postMediaStatsGlobal'] = $tabGlobalStats;
        $data['startDate'] = $start_date;
        $data['endDate'] = $end_date;

        return view('admin.statistics.home', $data);
    }

    public function getTopUsers($periodType = 'week')
    {
        if (!request()->has('periodType')) {
            $periodType = 'weekly';
        } else {
            $periodType = request()->get('periodType');
        }

        if (!request()->has('end_date')) {
            $ref_date = Carbon::now();
            $end_date = $ref_date->toDateString();
            $ref_date->subMonth(1);
            $ref_date->toDateString();
            $start_date = $ref_date->toDateString();
        } else {
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
        }

        $data = [];

        $refPeriod = \TrackingReport::select('created_at')
            ->where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date)
            ->where('period_type', '<=', $periodType)
            ->where('type', '=', '50_users_most_connected')
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->first();

        $start_date = date('Y-m-d', strtotime($refPeriod->created_at));
        switch ($periodType) {
            case "weekly":
                $ref_date = Carbon::create(
                    date('Y', strtotime($start_date)),
                    date('m', strtotime($start_date)),
                    date('d', strtotime($start_date)),
                    0
                );
                $ref_date->addWeek(1);
                $ref_date->toDateString();
                $end_date = $ref_date->toDateString();
                break;

            case "monthly":
                $ref_date = Carbon::create(
                    date('Y', strtotime($start_date)),
                    date('m', strtotime($start_date)),
                    date('d', strtotime($start_date)),
                    0
                );
                $ref_date->addMonth(1);
                $ref_date->toDateString();
                $end_date = $ref_date->toDateString();
                break;
        }

        $mostConnected = \TrackingReport::select('libelle', 'value')
            ->where('period_type', '=', $periodType)
            ->where('type', '=', '50_users_most_connected')
            ->where('created_at', '=', $refPeriod->created_at)
            ->orderBy('value', 'desc')
            ->take(50)
            ->get();

        $mostActions = \TrackingReport::select('libelle', 'value')
            ->where('period_type', '=', $periodType)
            ->where('type', '=', '20_users_most_actions')
            ->where('created_at', '=', $refPeriod->created_at)
            ->orderBy('value', 'desc')
            ->take(20)
            ->get();

        $mostMedias = \TrackingReport::select('libelle', 'value')
            ->where('period_type', '=', $periodType)
            ->where('type', '=', '20_users_most_medias')
            ->where('created_at', '=', $refPeriod->created_at)
            ->orderBy('value', 'desc')
            ->take(20)
            ->get();

        $data['subView'] = 'top-users';
        $data['refPeriod'] = $refPeriod->created_at;
        $data['mostConnected'] = $mostConnected;
        $data['mostActions'] = $mostActions;
        $data['mostMedias'] = $mostMedias;
        $data['periodType'] = $periodType;
        $data['startDate'] = $start_date;
        $data['endDate'] = $end_date;
        return view('admin.statistics.home', $data);
    }

    public function getActions()
    {
        if (!request()->has('periodType')) {
            $periodType = 'weekly';
        } else {
            $periodType = request()->get('periodType');
        }

        if (!request()->has('end_date')) {
            $ref_date = Carbon::now();
            $end_date = $ref_date->toDateString();
            $ref_date->subMonth(1);
            $ref_date->toDateString();
            $start_date = $ref_date->toDateString();
        } else {
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
        }

        $data = [];

        //actions Total
        $actionTotal = \TrackingReport::select('libelle as period', 'value')
            ->where('period_type', '=', $periodType)
            ->where('type', '=', 'average_action_users_total')
            ->orderBy('libelle', 'desc')
            ->take(10)
            ->lists('value', 'period');

        $actionTotalWclip = \TrackingReport::select('libelle as period', 'value')
            ->where('period_type', '=', $periodType)
            ->where('type', '=', 'average_action_users_total_w_clip')
            ->orderBy('libelle', 'desc')
            ->take(10)
            ->lists('value', 'period');

        //actions Xeme conn
        $actionXemeConn = \TrackingReport::select('libelle as period', 'value')
            ->where('period_type', '=', $periodType)
            ->where('type', '=', 'average_action_users_after_first_conn')
            ->orderBy('libelle', 'desc')
            ->take(10)
            ->lists('value', 'period');

        $actionXemeConnWclip = \TrackingReport::select('libelle as period', 'value')
            ->where('period_type', '=', $periodType)
            ->where('type', '=', 'average_action_users_after_first_conn_w_clip')
            ->orderBy('libelle', 'desc')
            ->take(10)
            ->lists('value', 'period');

        //make global stats
        $tabGlobalStats = [];
        foreach ($actionTotal as $period => $value) {
            $periodLimits = explode('|', $period);
            $tabGlobalStats[] = [
                "period" => $periodLimits[0],
                "actionTotal" => isset($actionTotal[$period])
                    ? round($actionTotal[$period])
                    : '0',
                "actionTotalWclip" => isset($actionTotalWclip[$period])
                    ? round($actionTotalWclip[$period])
                    : '0',
                "actionXemeConn" => isset($actionXemeConn[$period])
                    ? round($actionXemeConn[$period])
                    : '0',
                "actionXemeConnWclip" => isset($actionXemeConnWclip[$period])
                    ? round($actionXemeConnWclip[$period])
                    : '0'
            ];
        }

        $data['subView'] = 'actions';
        $data['periodType'] = $periodType;
        $data['actionTotal'] = $actionTotal;
        $data['actionTotalWclip'] = $actionTotalWclip;
        $data['actionXemeConn'] = $actionXemeConn;
        $data['actionXemeConnWclip'] = $actionXemeConnWclip;
        $data['actionsStatsGlobal'] = $tabGlobalStats;
        $data['startDate'] = $start_date;
        $data['endDate'] = $end_date;

        return view('admin.statistics.home', $data);
    }

    public function getLogs()
    {
        $data = [];

        $data['subView'] = 'logs';
        $data['reportsPdf'] = $this->getReports();

        return view('admin.statistics.home', $data);
    }

    /**
     * download report pdf
     */
    public function report($reportName)
    {
        $reportsDirectory = config('admin.reportsDirectory');
        $file = $reportsDirectory.'/'.$reportName.'.PDF';
        if (\File::exists($file)) {
            $reportContent = \File::get($reportsDirectory . '/' . $reportName . '.PDF');
            return response()->download(
                $reportsDirectory . '/' . $reportName . '.PDF',
                $reportName . '.PDF',
                ['Content-Type: application/pdf']
            );
        }
    }

    /**
     * return all pdf reports from datastudio
     * @return boolean
     */
    private function getReports()
    {
        $reportsDirectory = config('admin.reportsDirectory');

        $reports = \File::files($reportsDirectory);
        $reports = array_reverse($reports);

        $reportsFiles = [];
        foreach ($reports as $report) {
            $finalName = preg_replace_callback('~('.$reportsDirectory.'/(.*?)\.PDF)~i', function ($matches) {
                return $matches[2];
            },
            $report);
            if ($finalName != 'RPT1') {
                $reportsFiles[] = $finalName;
            }
        }

        return $reportsFiles;
    }
}
