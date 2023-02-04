<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Calendar;
use App\Profile;
use App\TEvent;
use App\GoogleCalendar;
use App\OutlookCalendar;
use App\CalendarApi;

class CalendarController extends BaseController
{

    public function __construct()
    {
        $this->middleware('checkAuth');

        parent::__construct();
    }

    public function home($profileType = null, $profileId = null)
    {

        $data = [];

        if ($profileType != null && $profileId != null) {
            $profileModel = Profile::gather($profileType);
            $profile = $profileModel::find($profileId);

            // check rights
            if ($profile->instances_id != session('instanceId')
                || ($profile->confidentiality == 0 && !BaseController::hasViewProfile($profile))) {
                return response(view('errors.403'), 403);
            }

            $data['profile'] = $profile;
        }

        return view('calendar.calendar', $data);
    }

    public function loadDates($type, $profile_type = null, $profile_id = null)
    {
        if ($type == 'profile' && $profile_type != 'user') {
            $profileModel = Profile::gather($profile_type);
            $profile = $profileModel::find($profile_id);
            if ($profile->instances_id != session('instanceId')
                || ($profile->confidentiality == 0 && !BaseController::hasViewProfile($profile))) {
                return response(view('errors.403'), 403);
            }
        }

        $start = request()->get('start');
        $end = request()->get('end');

        // format dates and times
        $startArray = explode(' ', $start);
        $endArray = explode(' ', $end);

        if (!isset($startArray[1])) {
            $startArray[1] = '00:00:00';
        }

        if (!isset($endArray[1])) {
            $endArray[1] = '00:00:00';
        }

        $calendar = new Calendar(request()->get('start'), request()->get('end'));
        $dataCalendar = $calendar->getEvents($type, $profile_type, $profile_id);

        $user = auth()->guard('web')->user();
        $dataCalendar = json_decode($dataCalendar);

        if ($type == 'profile' && $profile_type == 'user') {
            $calendars = $user->calendars();
            foreach ($calendars as $calendar) {
                $events = $calendar->getEvents($start, $end);
                $dataCalendar = array_merge($dataCalendar, $events);
            }

            //tasks
            $projects = $user->projects;
            foreach ($projects as $project) {
                //add condition to return only current user's tasks
                $tasks = $project->tasks()
                    ->join('workflows', 'workflows.id', 'tables_rows.workflows_id')
                    ->where('workflows.users_id', $user->id)
                    ->whereDate('deadline', '>=', date_format(date_create($start), 'Y-m-d'))
                    ->whereDate('deadline', '<=', date_format(date_create($end), 'Y-m-d'))
                    ->get();
                $events = [];
                foreach ($tasks as $task) {
                    $events[] = [
                        'url' => url()->route('task.project', ['project'=>$task->tables_tasks_id]) ,
                        'title' => $task->name,
                        'start' => $task->deadline,
                        // 'end' => $eventDate->date_end,
                        'color' => "black",
                        'allDay' => false,
                    ];
                }
                $dataCalendar = array_merge($dataCalendar, $events);
            }
        }

        $dataCalendar = json_encode($dataCalendar);
        // \Log::info($dataCalendar);
        return $dataCalendar;
    }

    public function import()
    {
        $data = array();
        $google = new GoogleCalendar();
        $outlook = new OutlookCalendar();
        $data = [
            'google_calendar' => $google->getAuthUrl(),
            'outlook' => $outlook->getAuthUrl(),
        ];

        session(['landingCalendarPage' => url()->previous()]);

        return view('calendar.import', $data);
    }

    public function export()
    {
        $data = array();
        $google = new GoogleCalendar();
        $outlook = new OutlookCalendar();
        $data = [
            'google_calendar' => $google->getAuthUrl(),
            'outlook' => $outlook->getAuthUrl(),
        ];
        $user = auth()->guard('web')->user();
        $data['calendars'] = $user->calendars();
        return view('calendar.export', $data);
    }

    public function launchExport($id, $email)
    {
        $calendar = CalendarApi::find($id, $email);
        if (isset($calendar)) {
            $calendar->export();
        }
        return redirect()->route('calendar.home');
    }

    public function calendarAuthorize($type)
    {
        if (request()->get('code')!=null) {
            $user = auth()->guard('web')->user();
            $client = new CalendarApi();
            if ($type=='google') {
                $client->type = CalendarApi::GOOGLE;
            } elseif ($type=='outlook') {
                $client->type = CalendarApi::OUTLOOK;
            }
            $client->code = request()->get('code');
            $client->users_id = $user->id;
            $client->email = $client->auth();
            $client->save();
        }
        return response(
            '<script>window.opener.location.replace(\'' . session('landingCalendarPage')
                . '\', \'_self\');window.self.close()</script>'
        );
    }

    public function synchronize($event_id = null)
    {
        $user = auth()->guard('web')->user();
        $calendars = $user->calendars();
        $data['calendars'] = $calendars;
        $data['event_id'] = $event_id;
        $user = auth()->guard('web')->user();
        if (request()->isMethod('POST')) {
            $postData = request()->get('postData');
            $email = $postData['email'];
            $id = $postData['event_id'];
            $event = TEvent::find($id);
            foreach ($calendars as $calendar) {
                if ($calendar->email == $email) {
                    // modify timezone
                    dump($event);
                    $event->convertToUtc();
                    dump($event);


                    $calendar->send($event);
                    break;
                }
            }
            return response()->json(['email'=>$email]);
        }
        return view('calendar.synchronize', $data);
    }
}
