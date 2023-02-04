<?php
namespace App\Http\Controllers\Instance;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\BoardingDemand;
use App\Mail\ManualBoarding;
use App\Repository\SearchRepository2;
use App\Instance;
use App\Profile;
use App\Boarding;
use App\User;
use App\Events\AddProfile;
use App\Netframe;
use App\Application;
use App\Subscription;
use App\House;
use App\Project;
use App\Community;
use App\Channel;
use App\Group;
use App\BillingInfos;
use Carbon\Carbon;
use App\Events\SubscribeToProfile;
use Lang;
use \App\Helpers\Lib\Acl;
use App\UsersGroup;
use Illuminate\Support\Facades\Log;
use App\UserNotification;
use App\Events\UserUpdateEvent;

class InstanceController extends BaseController
{

    public function __construct(SearchRepository2 $searchRepository)
    {
        $this->middleware('instanceManager');

        parent::__construct();

        $this->searchRepository = $searchRepository;
    }

    public function parameters($parameter = null)
    {
        $data = [];
        $instance = Instance::find(session('instanceId'));
        $data['instance'] = $instance;

        if ($parameter == null) {
            $parameter = 'boarding';
        }

        return view('instances.'.$parameter, $data);
    }

    public function subscription($action = null)
    {
        $instance = Instance::find(session('instanceId'));
        if ($action) {
            $accountent = $instance->accountents()->where('id', $action)->first();
            /*$accountent->instances()->detach();*/
            $instance->accountents()->detach($accountent);
            $accountent->delete();
            return redirect()->route('instance.subscription');
        }
        $data = [];
        $billingOffer = $instance->getParameter('billing_offer');
        $instanceQuota = config('billing.offer.'.$billingOffer);
        $instanceMediaSize = $instance->getMediaSize();

        $infos = $instance->billing_infos()->where('type', '=', 'card')->first();
        $card = '';
        if (isset($infos)) {
            $cardInfos = json_decode($infos->value, true);
            $number = $cardInfos['number'];
            $card = substr($number, 0, 4) . 'XXXXXXXX' . substr($number, 12)
                . ' exp : ' . $cardInfos['expiry-month'] . '/' . $cardInfos['expiry-year'];
        }

        $data['card'] = $card;

        $data['instance'] = $instance;
        $data['billingOffer'] = $billingOffer;
        $data['instanceQuota'] = $instanceQuota;
        $data['instanceMediaSize'] = round($instanceMediaSize / 1024 / 1024 / 1024);

        if (request()->isMethod('POST')) {
            if (request()->has('delegate_access')) {
                $rule = ['email' => 'required|email|unique:accountents'];
                $inputPost = array(
                    'email' => trim(request()->get('email'))
                );
                $validation = validator($inputPost, $rule);
                if ($validation->fails()) {
                    return redirect()->route('instance.subscription')
                                     ->withErrors($validation)
                                     ->withInput();
                } else {
                    return \App::call(
                        'App\Http\Controllers\Accountent\AuthController@newAccount',
                        ['email'=>request()->get('email'), 'route'=>'instance.subscription']
                    );
                }
            } elseif (request()->has('delete')) {
                $instance->billing_infos()->where('type', '=', 'card')->delete();
                return redirect()->route('instance.subscription');
            } else {
                $rule = array(
                    'card_number' => 'required|numeric',
                    'card_expiry' => 'required|max:5|min:5',
                    'card_crypto' => 'required|max:3|min:3',
                );
                $inputPost = array(
                    'card_number' => trim(request()->get('card_number')),
                    'card_expiry' => trim(request()->get('card_expiry')),
                    'card_crypto' => trim(request()->get('card_crypto')),
                );
                $validation = validator($inputPost, $rule);
                if ($validation->fails()) {
                    return redirect()->route('instance.subscription')
                                     ->withErrors($validation)
                                     ->withInput();
                }
                $instance = Instance::find(session('instanceId'));

                // create card token and stripe customer
                $expiry_date = request()->get('card_expiry');
                $month = substr($expiry_date, 0, 2);
                $year = substr($expiry_date, 3);

                $card = [
                    'number' => request()->get('card_number'),
                    'expiry-month' => $month,
                    'expiry-year' => $year,
                    'crypto' => request()->get('card_crypto'),
                ];

                $stripeInstance = $instance->billing_infos()->where('type', '=', 'stripe_customer')->first();

                if ($stripeInstance != null) {
                    $stripeInfos = json_decode($stripeInstance->value);
                    $customerId = $stripeInfos->customerID;
                } else {
                    $customerId = null;
                }

                $stripeCustomer = \App\Helpers\StripeHelper::createCustomer($card, $customerId);

                if ($stripeCustomer['result'] == 'success') {
                    if ($stripeInstance == null) {
                        $stripeInstance = new BillingInfos();
                        $stripeInstance->instances_id = session('instanceId');
                        $stripeInstance->type = 'stripe_customer';
                    }
                    $stripeInstance->value = json_encode($stripeCustomer['infos']);
                    $stripeInstance->save();

                    // test if card exists, if yes update
                    $cardInfos = BillingInfos::firstOrCreate([
                        'instances_id' => session('instanceId'),
                        'type' => 'card'
                    ]);

                    $cardInfos->type = 'card';
                    $cardInfos->value = json_encode([
                        'name' => 'card',
                        'number' => request()->get('card_number'),
                        'expiry-month' => $month,
                        'expiry-year' => $year,
                        'crypto' => request()->get('card_crypto')
                    ]);
                    $cardInfos->instances_id = session('instanceId');
                    $cardInfos->save();

                    // verify if instance is in free mode
                    if ($instance->getParameter('billing_offer') == 'free') {
                        $instance->setParameter('billing_offer', 'normal');
                    }

                    \Artisan::call('generate:bills');
                } else {
                    $errorMessage = $stripeCustomer['error']->stripeCode;
                    $data['errorCb'] = $errorMessage;
                    return view('instances.subscription', $data);
                }

                return redirect()->route('instance.subscription');
            }
        }

        return view('instances.subscription', $data);
    }

    public function boarding($action = null)
    {
        $data = [];
        $instance = Instance::find(session('instanceId'));
        if ($action != null) {
            switch ($action) {
                case "generate-key":
                    $key = $instance->getParameter('boarding_invite_key', true);
                    $key->parameter_value = str_random(50);
                    $key->save();
                    $data['result'] = "newKeyGenerated";
                    break;

                case "disable-key":
                    $key = $instance->getParameter('boarding_on_key_disable', true);
                    $newValue = ($key->parameter_value == 1) ? 0 : 1;
                    $key->parameter_value = $newValue;
                    $key->save();
                    break;

                case "consent-charter":
                    if (request()->has('consent_charter') && request()->get('consent_charter') == 1) {
                        $instance->setParameter('local_consent_state', true);
                        $instance->setParameter('local_consent_content', request()->get('consent_charter_content'));
                    } else {
                        $instance->setParameter('local_consent_state', false);
                    }
                    break;

                case "":
                    break;
            }
        }

        $localConsentState = $instance->getParameter('local_consent_state');
        $localConsentContent = $instance->getParameter('local_consent_content');

        $data['localConsentState'] = $localConsentState;
        $data['localConsentContent'] = $localConsentContent;

        $data['instance'] = $instance;

        return view('instances.boarding', $data);
    }

    public function profiles($profileType)
    {
        $profileAddUserUrl = 'instance.create';
        $profileInviteUserUrl = 'instance.invite';
        $profileEditUrl = '';
        $instance = Instance::find(session('instanceId'));

        switch ($profileType) {
            case 'houses':
                $profileEditUrl = 'house.edit';
                break;

            case 'projects':
                $profileEditUrl = 'project.edit';
                break;

            case 'communities':
                $profileEditUrl = 'community.edit';
                break;
        }

        if (request()->isMethod('GET')) {
            // get main field order
            if ($profileType == 'projects') {
                $fieldOrder = 'title';
            } else {
                $fieldOrder = 'name';
            }

            $profiles = $instance->$profileType()->orderBy($fieldOrder, 'asc')->paginate(10);
            $fromSearch = 0;
        } elseif (request()->isMethod('POST')) {
            if (request()->has('search')) {
                $profilesMatch = config('instances.searchProfiles');
                $targetsProfiles = [
                    $profilesMatch[$profileType] => 1
                ];

                $query = request()->get('query');
                $loadFilters = (request()->has('loadFilters')) ? request()->get('loadFilters') : true ;
                $hashtag = (request()->has('$hashtag')) ? request()->get('$hashtag') : '';
                $placeSearch = (request()->has('placeSearch')) ? request()->get('placeSearch') : '';
                $byInterests = (request()->has('byInterests') && request()->get('byInterests') == 1) ? 1 : 0;
                $this->searchRepository->route = 'search_results';
                $this->searchRepository->targetsProfiles = $targetsProfiles;
                $this->searchRepository->toggleFilter = false;
                $this->searchRepository->byInterests = $byInterests;
                $this->searchRepository->newProfile = 0;
                $this->searchRepository->inviteProfile = 'instance';
                $this->searchRepository->search_limit = 100;

                $searchParameters = $this
                    ->searchRepository
                    ->initializeConfig('search_results', $targetsProfiles, false, $byInterests);
                $results = $this->searchRepository->search($searchParameters, $targetsProfiles);
                $profiles = $results[0];
                $fromSearch = 1;
            } else {
                // @ TODO check if this code is still used
                $rule = ['email' => 'required|email|unique:users'];
                $inputPost = array(
                    'email' => trim(request()->get('email'))
                );
                $validation = validator($inputPost, $rule);
                if ($validation->fails()) {
                    return redirect()->route('instance.profiles', ['profileType' => $profileType])
                    ->withErrors($validation)
                    ->withInput();
                } else {
                    //create a invited user
                    $user = new \App\User;
                    $user->email = request()->get('email');
                    $user->visitor = true;

                    $tokenPassword = uniqid(uniqid(), true);
                    $passwordTimeout = date('Y-m-d H:i:s', strtotime('+'.config('auth.timeout_password').' hours'));

                    $user->password_token = $tokenPassword;
                    $user->password_timeout = $passwordTimeout;
                    $user->slug = uniqid();
                    $user->name = Lang::get('instances.profiles.guest');
                    $user->lang = auth()->guard('web')->user()->lang;
                    $user->save();
                    //add to current instance
                    $user->instances()->attach(session('instanceId'), ['roles_id'=>5]);

                    $data = array(
                        "user" => $user,
                        "instance"  => $instance,
                        "url" => url()->route('auth.remindPassword', ['token'=>$user->password_token])
                    );
                    //Send Mail
                    Mail::to($user->email)->send(new \App\Mail\RegisterVisitor($data));
                    return redirect()->route('instance.profiles', ['profileType' => $profileType]);
                }
            }
        }

        if ($profileType == 'users' && $fromSearch == 1) {
            $newProfiles = [];
            foreach ($profiles as $profile) {
                $newProfiles[] = $instance->users->where('id', '=', $profile->id)->first();
            }
            $profiles = $newProfiles;
        }

        $data = [];
        $data['instance'] = $instance;
        $data['profileType'] = $profileType;
        $data['profiles'] = $profiles;
        $data['profileEditUrl'] = $profileEditUrl;
        $data['$profileAddUserUrl'] = $profileAddUserUrl;
        $data['$profileInviteUserUrl'] = $profileInviteUserUrl;
        $data['fromSearch'] = $fromSearch;
        return view('instances.profiles', $data);
    }

    public function edit($id)
    {
        $user = User::find($id);
        if (request()->isMethod('POST')) {
            $rules = ['email'=> 'required|email|unique:users',
                'name' => 'required',
                'firstname' => 'required'
            ];
            $validator = validator(request()->all(), $rules);
            $hasError = false;
            $errors = [];
            if ($validator->fails()) {
                $errors = $validator->getMessageBag()->toArray();
                if ($user->email==request()->get('email') && array_key_exists("email", $errors)) {
                    unset($errors['email']);
                }
                if (count($errors)>0) {
                    $hasError = true;
                }
            }
            if (!$hasError) {
                if ($user) {
                    $user->email = request()->get("email");
                    $user->name = request()->get("name");
                    $user->firstname = request()->get("firstname");
                    $user->update();
                    return response()->json([
                        'waitCloseModal' => 3000,
                        'view' => view('instances.partials.success-edit')->render(),
                        'success' => $user->id,
                        'name' => $user->getNameDisplay()
                    ]);
                } else {
                    return redirect('404');
                }
            } else {
                return response()->json(['errors' => $errors]);
            }
        }
        return view('instances.partials.edit', ['user'=>$user]);
    }

    public function manage($profileType, $id)
    {
        $instance = Instance::find(session('instanceId'));

        switch ($profileType) {
            case 'houses':
                $profileModel = Profile::gather('house');
                break;

            case 'projects':
                $profileModel = Profile::gather('project');
                break;

            case 'communities':
                $profileModel = Profile::gather('community');
                break;
        }
        $profile = $profileModel::find($id);
        $members = $profile->users;
        $membersIds = $profile->users()->pluck('id')->toArray();

        $fromSearch = 0;

        if (request()->isMethod('POST')) {
            if (request()->has('search')) {
                $profilesMatch = config('instances.searchProfiles');
                $targetsProfiles = [
                    $profilesMatch['users'] => 1
                ];

                $query = request()->get('query');
                $loadFilters = (request()->has('loadFilters')) ? request()->get('loadFilters') : true ;
                $hashtag = (request()->has('$hashtag')) ? request()->get('$hashtag') : '';
                $placeSearch = (request()->has('placeSearch')) ? request()->get('placeSearch') : '';
                $byInterests = (request()->has('byInterests') && request()->get('byInterests') == 1) ? 1 : 0;
                $this->searchRepository->route = 'search_results';
                $this->searchRepository->targetsProfiles = $targetsProfiles;
                $this->searchRepository->toggleFilter = false;
                $this->searchRepository->byInterests = $byInterests;
                $this->searchRepository->newProfile = 0;
                $this->searchRepository->inviteProfile = 'instance';
                $this->searchRepository->search_limit = 100;

                $searchParameters = $this
                    ->searchRepository
                    ->initializeConfig('search_results', $targetsProfiles, false, $byInterests);
                $results = $this->searchRepository->search($searchParameters, $targetsProfiles);
                $profiles = $results[0];
                $fromSearch = 1;
            } else {
                $role = request()->get('role');
                $members = request()->get('users');
                foreach ($members as $userId) {
                    $resAttach = $profile->users()->attach($userId, ['roles_id' => $role, 'status' => 1]);
                }

               // force user check rights
                User::whereIn('id', $members)->update(['check_rights' => 1]);

                return redirect()->route('instance.manage', ['profileType'=>$profileType, 'id'=>$id]);
            }
        } else {
            $profiles = $instance->activeUsers()->whereNotIn('id', $membersIds)->orderBy('name', 'asc')->paginate(10);
        }

        return view('instances.manage', [
            'profile' => $profile,
            'profiles' => $profiles,
            'members' => $members,
            'membersIds' => $membersIds,
            'profileType' => $profileType,
            'profileId' => $id,
            'fromSearch' => $fromSearch
        ]);
    }

    public function manageRights($id)
    {
        $instance = Instance::find(session('instanceId'));
        $user = User::find($id);

        $houses = $instance->houses()->where('active', true)->orderBy('name', 'asc')->paginate(10);
        foreach ($houses as $house) {
            $userWithPivot = $house->users()->where('users_id', $id)->first();
            if ($userWithPivot) {
                $house->member = $userWithPivot;
                $house->status = $userWithPivot->pivot->status;
                $house->role = $userWithPivot->pivot->roles_id;
            } else {
                $house->member = $user;
                $house->status = -1;
                $house->role = -1;
            }
        }

        $projects = $instance->projects()->where('active', true)->orderBy('title', 'asc')->paginate(10);
        foreach ($projects as $project) {
            $userWithPivot = $project->users()->where('users_id', $id)->first();
            if ($userWithPivot) {
                $project->member = $userWithPivot;
                $project->status = $userWithPivot->pivot->status;
                $project->role = $userWithPivot->pivot->roles_id;
            } else {
                $project->member = $user;
                $project->status = -1;
                $project->role = -1;
            }
        }

        $communities = $instance->communities()->where('active', true)->orderBy('name', 'asc')->paginate(10);
        foreach ($communities as $community) {
            $userWithPivot = $community->users()->where('users_id', $id)->first();
            if ($userWithPivot) {
                $community->member = $userWithPivot;
                $community->status = $userWithPivot->pivot->status;
                $community->role = $userWithPivot->pivot->roles_id;
            } else {
                $community->member = $user;
                $community->status = -1;
                $community->role = -1;
            }
        }

        $channels = $instance->channels()->where('active', true)->orderBy('name', 'asc')->paginate(10);
        foreach ($channels as $channel) {
            $userWithPivot = $channel->users()->where('users_id', $id)->first();
            if ($userWithPivot) {
                $channel->member = $userWithPivot;
                $channel->status = $userWithPivot->pivot->status;
                $channel->role = $userWithPivot->pivot->roles_id;
            } else {
                $channel->member = $user;
                $channel->status = -1;
                $channel->role = -1;
            }
        }

        return view('instances.manage-rights', [
            'houses' => $houses,
            'projects' => $projects,
            'communities' => $communities,
            'channels' => $channels,
            'user' => $user
        ]);
    }

    public function getUsers()
    {
        $query = request()->get('q');
        $targetsProfiles = ['user' => 1];

        $this->searchRepository->route = 'search_results';
        $this->searchRepository->targetsProfiles = $targetsProfiles;
        $this->searchRepository->toggleFilter = false;
        $this->searchRepository->byInterests = 0;
        $this->searchRepository->newProfile = 0;

        $searchParameters = $this->searchRepository->initializeConfig('search_results', $targetsProfiles, false, 0);
        $results = $this->searchRepository->search($searchParameters, $targetsProfiles);

        $returnResult = [];
        foreach ($results[0] as $user) {
            $returnResult[] = [
                'id' => $user->id,
                'text' => $user->getNameDisplay(),
                'image' => ($user->profileImage != null) ? $user->profileImage->getUrl() : "",
                'online' => ($user->isOnline()) ? 'status-online' : 'status-offline',
            ];
        }

        return response()->json(['results' => $returnResult]);
    }

    public function create()
    {
        $instance = Instance::find(session('instanceId'));
        $data = [];
        $data['customFields'] = $customFields = json_decode($instance->getParameter('custom_user_fields'), true) ?: [];
        if (request()->isMethod('POST')) {
            if (request()->has('import')) {
                $rules = ['file' =>'required|mimes:csv,txt'];
                $validation = validator(request()->all(), $rules);
                if ($validation->fails()) {
                    return redirect()->route('instance.create', ['page'=>'import'])
                    ->withErrors($validation)
                    ->withInput();
                } else {
                    $file_handle = fopen(request()->file('file'), 'r');
                    $i=0;
                    $email = $name = $firstname = -1;
                    $users = [];
                    $echecs = [];
                    while (!feof($file_handle)) {
                        $text[] = fgetcsv($file_handle, 0, ";");
                        if ($i==0) {
                            foreach ($text[0] as $key => $title) {
                                if (strtolower($title) == strtolower(
                                    Lang::get("instances.create.cols.email")
                                )) {
                                    $email = $key;
                                } elseif (strtolower($title) == strtolower(
                                    Lang::get("instances.create.cols.name")
                                )) {
                                    $name = $key;
                                } elseif (strtolower($title) == strtolower(
                                    Lang::get("instances.create.cols.firstname")
                                )) {
                                    $firstname = $key;
                                }
                                /*else{
                                    \Session::flash('error.file.title', $title);
                                    return redirect()->route('instance.create');
                                }*/
                            }
                            if ($email==-1) {
                                \Session::flash('error.header', "email");
                            } elseif ($name==-1) {
                                \Session::flash('error.header', "name");
                            } elseif ($firstname==-1) {
                                \Session::flash('error.header', "firstname");
                            }
                            if ($email==-1 || $name==-1 || $firstname==-1) {
                                return redirect()->route('instance.create', ['page'=>'import']);
                            }
                        } else {
                            $aLine['email'] = $text[$i][$email];
                            $aLine['firstname'] = $text[$i][$firstname];
                            $aLine['name'] = $text[$i][$name];
                            $users[] = $aLine;
                            $rules = ['email'=> 'required|email|unique:users',
                                'name' => 'required',
                                'firstname' => 'required'
                            ];
                            $validation = validator($aLine, $rules);
                            if ($validation->fails()) {
                                if ($aLine['firstname']!=null && $aLine['name']!=null && $aLine['email']!=null) {
                                    $echecs[] = $i;
                                }
                            } else {
                                $user = $this->createUser($aLine['firstname'], $aLine['name'], $aLine['email']);
                            }
                            /*$user = User::create([
                                'name' => request()->get('name']),
                                'firstname' => request()->get('firstnamename']),
                                'email' => request()->get('email']),
                            ]);
                            event(new \App\Events\UserUpdatedEvent($user));*/
                        }
                        $i++;
                    }
                    fclose($file_handle);
                    $errors = array_map(function ($aLine, $key) use ($echecs) {
                        return in_array($key, $echecs) ? $aLine : null;
                    }, $text, array_keys($text));
                    if (!empty($echecs)) {
                        \Session::flash('error.file', $errors);
                    } else {
                        $pass = array_map(function ($aLine, $key) use ($echecs) {
                            return !in_array($key, $echecs) ? $aLine : null;
                        }, $text, array_keys($text));
                        \Session::flash('success.file', $pass);
                    }
                    return redirect()->route('instance.create', ['page'=>'import']);
                }
            } else {
                $rules = ['email'=> 'required|email|unique:users',
                    'name' => 'required',
                    'firstname' => 'required'
                ];
                $validation = validator(request()->all(), $rules);
                if ($validation->fails()) {
                    return redirect()->route('instance.create')
                    ->withErrors($validation)
                    ->withInput();
                } else {
                    $firstname = request()->get('firstname');
                    $name = request()->get('name');
                    $email = request()->get('email');
                    $user = $this->createUser($firstname, $name, $email);

                    if (request()->has('custom_field')) {
                        $customsFieldsValues = request()->get('custom_field');
                        foreach ($customFields as $slug => $value) {
                            if (isset($customsFieldsValues[$slug])) {
                                $user->setParameter('custom_user_field_' . $slug, $customsFieldsValues[$slug]);
                            } else {
                                $user->deleteParameter('custom_user_field_' . $slug);
                            }
                        }
                    }

                    \Session::flash('status', '');
                    return redirect()->route('instance.create');
                }
            }
        }
        return view('instances.create', $data);
    }

    private function createUser($firstname, $name, $email)
    {
        $instance = Instance::find(session('instanceId'));

        $user = new User();

        $userPassword = str_random(10);
        $user->ip = \App\Helpers\SessionHelper::getIp();
        $user->lang = \Lang::getLocale();
        $user->firstname = $firstname;
        $user->name = $name;
        $user->email = trim($email);
        $user->password = bcrypt($userPassword);
        $user->gdpr_agrement = 0;
        $user->modal_gdpr = 1;
        $user->confidentiality = 1;

        // Generate Uniq Key for slug profile
        $uniqKey = uniqid();

        // Check if uniq id in slug exist
        if (User::where('slug', '=', $uniqKey)->exists()) {
            $uniqKey = uniqid();
        }
        $user->slug = $uniqKey;
        $user->save();

        // on rattache l'utilisateur a l'instance /!\ a bien prendre l'id
        $user->instances()->attach(session('instanceId'), ['roles_id' => 5]);

        // create defaults
        $user->createDefaults($instance);

        // envoi du mail avec username et mot de passe
        \App::setLocale($user->lang);
        $instance = Instance::find(session('instanceId'));
        if (config('netframe.log_mails_data')) {
            Log::debug('Sending ' . request()->get('email') . ' invitation password ' . $userPassword);
        }
        Mail::to($user->email)->send(new ManualBoarding($user, $instance, $userPassword));
        return $user;
    }

    public function groups()
    {
        $rules = [
            'name' => 'required',
        ];
        $validation = validator(request()->all(), $rules);
        if ($validation->fails()) {
            /*return redirect()->route('instance.groups')
                ->withErrors($validation)
                ->withInput();*/
        } else {
            $group = new Group();
            $group->name = request()->get('name');
            $group->save();

            \Session::flash('group.created', Lang::get("instances.groups.success"));
            return redirect()->route('instance.groups');
        }
        return view('instances.groups');
    }

    public function visitors()
    {
        $data = [];
        $instance = Instance::find(session('instanceId'));
        if (request()->isMethod('GET')) {
            $visitors = $instance->users()->where('visitor', '1')->orderBy('created_at', 'desc')->paginate(10);
        } elseif (request()->isMethod('POST')) {
            $rule = ['email' => 'required|email|unique:users'];
            $inputPost = array(
                'email' => trim(request()->get('email'))
            );
            $validation = validator($inputPost, $rule);
            if ($validation->fails()) {
                return redirect()->route('instance.visitors')
                ->withErrors($validation)
                ->withInput();
            } else {
                //create a visitor
                $user = new \App\User;
                $user->email = request()->get('email');
                $user->visitor = true;

                $tokenPassword = uniqid(uniqid(), true);
                $passwordTimeout = date('Y-m-d H:i:s', strtotime('+'.config('auth.timeout_password').' hours'));

                $user->password_token = $tokenPassword;
                $user->password_timeout = $passwordTimeout;
                $user->slug = uniqid();
                $user->firstname = request()->get(trim('firstname'));
                $user->name = request()->get(trim('lastname'));
                $user->lang = auth()->guard('web')->user()->lang;
                $user->save();
                //add to current instance
                $user->instances()->attach(session('instanceId'), ['roles_id'=>5]);

                $data = array(
                    "user" => $user,
                    "instance"  => $instance,
                    "url" => url()->route('auth.remindPassword', ['token'=>$user->password_token])
                );
                //Send Mail
                Mail::to($user->email)->send(new \App\Mail\RegisterVisitor($data));

                return redirect()->route('instance.visitors');
            }
        }
        $data['visitors'] = $visitors;
        $data['instance'] = $instance;
        return view('instances.visitors', $data);
    }

    public function activation($profileType)
    {
        $instance = Instance::find(session('instanceId'));
        $profileId = request()->get('profileId');
        $profile = $instance->$profileType()->where('id', '=', $profileId)->first();
        if ($profile != null) {
            $newState = request()->get('stateTo');
            $profile->active = $newState;
            $profile->save();

            if (class_basename($profile) == 'User') {
                event(new UserUpdateEvent($profile));
            }

            if ($newState == 1) {
                $instance->newUser();
            }

            $view = view('join.member-card', ['profile' => $instance, 'member' => $profile])->render();

            return response()->json([
                'active' => $profile->active,
                'view' => $view,
            ]);
        }
    }

    public function invite()
    {
        $instance = Instance::find(session('instanceId'));
        $billingOffer = $instance->getParameter('billing_offer');
        $instanceQuota = config('billing.offer.'.$billingOffer.'.maxUsers');
        $userQuotaReach = ($instanceQuota > 0 && $instance->users->count() >= $instanceQuota) ? true : false;

        $data = [];
        $data['instance'] = $instance;
        $data['userQuotaReach'] = $userQuotaReach;

        if (request()->isMethod('POST') && !$userQuotaReach) {
            $validationRules = config('validation.boarding/invite');
            $this->validate(request(), $validationRules);

            $tabSendInvites = [];
            $tabSendInvites['sended'] = [];
            $tabSendInvites['notSended'] = [];
            for ($i=1; $i <= request()->get('nbFields'); $i++) {
                $email = request()->get('email'.$i);
                if ($email != null) {
                    // test email in boarding and user table for the current instance
                    $testBoarding = Boarding::where('email', '=', $email)
                        ->where('instances_id', '=', $instance->id)->first();
                    // $testUser = $instance->users()->where('email', '=', $email)->first();
                    $testUser = User::where('email', '=', $email)->first();

                    if ($testBoarding == null && $testUser == null) {
                        $tabSendInvites['sended'][] = $email;

                        //send invite
                        $slug = uniqid();
                        $boarding = new Boarding();
                        $boarding->instances_id = $instance->id;
                        $boarding->email = $email;
                        $boarding->slug = $slug;
                        $boarding->lang = auth()->guard('web')->user()->lang;
                        $boarding->save();
                        $boarding->emailKey = base64_encode($boarding->created_at . '|' . $boarding->id . '|' . $email);
                        $boarding->userFrom = auth()->guard('web')->user();
                        $boarding->boardingUrl = $instance->getUrl()
                            . '/boarding/key/' . $boarding->slug
                            . '/' . $boarding->emailKey;

                        //send mail
                        Mail::to($boarding->email)->send(new BoardingDemand($boarding));
                    } else {
                        $tabSendInvites['notSended'][] = $email;
                    }
                }
            }

            session()->flash('tabEmails', $tabSendInvites);

            if (request()->ajax()) {
                $data = ['sendInvit' => true];
                $view = view('welcome.modals.modal-invite', $data)->render();
                return response()->json([
                    'view' => $view,
                ]);
            }

            return redirect()->route('instance.invite');
        }

        return view('instances.invite', $data);
    }

    public function apps()
    {
        $data =[];
        $instance = Instance::find(session('instanceId'));
        $apps = Application::where('self_subscribe', '=', '1')->get();
        //$instancesApps = $instance->apps;

        if (request()->isMethod('POST')) {
            foreach ($apps as $app) {
                if (request()->get('app_'.$app->id) == 1) {
                    if (!$instance->apps->contains($app->id)) {
                        // attach
                        $instance->apps()->attach($app->id);
                    }
                } else {
                    if ($instance->apps->contains($app->id)) {
                        // detach
                        $instance->apps()->detach($app->id);
                    }
                }
            }
            return redirect()->route('instance.apps');
        }

        $data['instance'] = $instance;
        $data['apps'] = $apps;

        return view('instances.apps', $data);
    }

    public function rights($action = null)
    {
        $instance = Instance::find(session('instanceId'));

        $data = [];
        if (session('instanceMonoProfile')) {
            $rightsProfiles = config('instances.rightsMonoProfiles');
        } else {
            $rightsProfiles = config('instances.rightsProfiles');
        }
        $data['rightsProfiles'] = $rightsProfiles;
        $data['instanceRightsProfiles'] = json_decode($instance->getParameter('profile_profile'), true);
        if ($data['instanceRightsProfiles'] == null) {
            //load default rights
            $defaultRights = config('instances.default_config.profile_profile');
            $instance->setParameter('profile_profile', $defaultRights);
            $data['instanceRightsProfiles'] = json_decode($defaultRights, true);
        }

        switch ($action) {
            case "becomeGod":
                if (request()->isMethod('POST')) {
                    $credentials = [
                        "email" => auth()->guard('web')->user()->email,
                        "password" => request()->get(trim('password')),
                        "active" => 1
                    ];
                    if (auth()->guard('web')->attempt($credentials)) {
                        //load god mod
                        session([
                            "acl" => $this->godMode(),
                            "godMode" => 1
                        ]);
                        $action = '';
                    } else {
                        $action = 'godPassword';
                    }
                } else {
                    $action = 'godPassword';
                }

                break;

            case "disableGod":
                session([
                    "acl" => Netframe::getAcl(auth()->guard('web')->user()->id)
                ]);
                session()->forget('godMode');
                $action = '';
                break;

            case "updateTimelinePost":
                if (request()->get('ban_post_on_timeline') == "true") {
                    dump('enable');
                    $instance->setParameter('disable_post_on_timeline', 1);
                } else {
                    dump('disable');
                    $instance->setParameter('disable_post_on_timeline', 0);
                }
                return response()->json([
                    'result' => true,
                ]);
                break;

            case "authProfiles":
                $tabAuthProfiles = [];

                foreach ($rightsProfiles as $profiles => $profile) {
                    $tabAuthProfiles[$profile]['house'] = (request()->exists($profile.'-create-house')) ? 1 : 0;
                    $tabAuthProfiles[$profile]['community'] = (request()->exists($profile.'-create-community')) ? 1 : 0;
                    $tabAuthProfiles[$profile]['project'] = (request()->exists($profile.'-create-project')) ? 1 : 0;
                    $tabAuthProfiles[$profile]['channel'] = (request()->exists($profile.'-create-channel')) ? 1 : 0;
                }
                $instance->setParameter('profile_profile', json_encode($tabAuthProfiles));
                $data['instanceRightsProfiles'] = $tabAuthProfiles;

                $usersInstance = $instance->users;
                foreach ($usersInstance as $userInstance) {
                    $userInstance->check_rights = 1;
                    $userInstance->save();
                }

                /*
                $usersInstance = $instance->users()->pluck('id');
                $upUser = User::whereIn('id', $usersInstance)->update(['check_rights' => 1]);
                */

                $action = null;
                break;
        }

        $data['action'] = $action;
        $data['refusePostTimeline'] = ($instance->getParameter('disable_post_on_timeline') == 1) ? true : false;

        return view('instances.rights', $data);
    }

    public function autoSubscribe($profileType = null)
    {
        $instance = Instance::find(session('instanceId'));

        if (request()->isMethod('POST')) {
            if ($profileType != null && in_array($profileType, ['houses', 'projects', 'communities'])) {
                switch ($profileType) {
                    case 'houses':
                        $profileModel = Profile::gather('house');
                        break;

                    case 'projects':
                        $profileModel = Profile::gather('project');
                        break;

                    case 'communities':
                        $profileModel = Profile::gather('community');
                        break;
                }

                // get id of profiles already in auto subscribe
                $alreadyAuto = $instance->$profileType()->where('auto_subscribe', '=', 1)->pluck('id')->toArray();

                $postedIds = request()->get('autoProfiles');
                if (is_array($postedIds)) {
                    // make diff to treat only new auto subscribe profiles and in other side canceled auto subscribe
                    $newAutoSubscribe = array_diff($postedIds, $alreadyAuto);
                    $removeAutoSubscribe = array_diff($alreadyAuto, $postedIds);

                    // update auto subscribe parameter for selected profiles
                    $profileModel::whereIn('id', $removeAutoSubscribe)->update(['auto_subscribe' => 0]);
                    $profileModel::whereIn('id', $newAutoSubscribe)->update(['auto_subscribe' => 1]);

                    // update subscriptions for all users not subscribed
                    $allUsers = $instance->users()->pluck('id')->toArray();
                    foreach ($newAutoSubscribe as $profileId) {
                        $subscribed = $instance
                            ->users()
                            ->leftJoin('subscriptions', 'subscriptions.users_id', '=', 'users.id')
                            ->where('profile_type', '=', get_class($profileModel))
                            ->where('profile_id', '=', $profileId)
                            ->pluck('users.id')->toArray();
                        $diffUsers = array_diff($allUsers, $subscribed);
                        // insert diff unsers in subscription
                        $arrayNewSubscribe = [];
                        foreach ($diffUsers as $userId) {
                            $arrayNewSubscribe[] = [
                                'users_id' => $userId,
                                'instances_id' => $instance->id,
                                'profile_id' => $profileId,
                                'profile_type' => get_class($profileModel),
                                'level' => 1,
                                'confidentiality' => 1,
                                'created_at' => \Carbon\Carbon::now()->toDateString(),
                                'updated_at' => \Carbon\Carbon::now()->toDateString(),
                            ];
                        }
                        Subscription::insert($arrayNewSubscribe);
                    }
                }

                $data['display'] = 'profile';
                $profiles = $instance->$profileType()->where('confidentiality', '=', 1)->get();
                $data['profiles'] = $profiles;
                $data['success'] = true;
            } elseif ($profileType == 'channels') {
                $profileModel = Profile::gather('community');

                // get id of profiles already in auto subscribe
                $alreadyAuto = $instance->channels()->where('auto_subscribe', '=', 1)->pluck('id')->toArray();

                $postedIds = request()->get('autoProfiles');
                if (is_array($postedIds)) {
                    // make diff to treat only new auto subscribe profiles and in other side canceled auto subscribe
                    $newAutoSubscribe = array_diff($postedIds, $alreadyAuto);
                    $removeAutoSubscribe = array_diff($alreadyAuto, $postedIds);

                    // update auto subscribe parameter for selected profiles
                    Channel::whereIn('id', $removeAutoSubscribe)->update(['auto_subscribe' => 0]);
                    Channel::whereIn('id', $newAutoSubscribe)->update(['auto_subscribe' => 1]);

                    // update membership for all users not members
                    $allUsers = $instance->users()->pluck('id')->toArray();
                    foreach ($newAutoSubscribe as $profileId) {
                        $subscribed = $instance
                            ->users()
                            ->leftJoin('channels_has_users', 'channels_has_users.users_id', '=', 'users.id')
                            ->where('channels_id', '=', $profileId)
                            ->pluck('users.id')->toArray();
                        $diffUsers = array_diff($allUsers, $subscribed);

                        $channel = Channel::find($profileId);

                        // insert diff unsers in membership
                        foreach ($diffUsers as $userId) {
                            $channel->users()->attach($userId, ['roles_id' => '4', 'status' => 1]);
                        }
                    }
                }

                $data['display'] = 'profile';
                $profiles = $instance->channels()->where('confidentiality', '=', 1)->get();
                $data['profiles'] = $profiles;
                $data['success'] = true;
            }
        } else {
            $data = [];
            if ($profileType == null) {
                $data['display'] = 'intro';
            } else {
                $data['display'] = 'profile';

                $profiles = $instance->$profileType()->where('confidentiality', '=', 1)->where('active', '=', 1)->get();

                $data['profiles'] = $profiles;
            }
        }

        $data['profileType'] = $profileType;
        return view('instances.auto-subscribe', $data);
    }

    private function godMode()
    {
        $arrayAcl = array();
        $instance = Instance::find(session('instanceId'));

        //community_has_user --> add role id + role participant
        $communities = $instance->communities;
        foreach ($communities as $community) {
            $arrayAcl['community'][$community->id] = 1;
        }

        //house_has_user --> add role id + role participant
        $houses = $instance->houses;
        foreach ($houses as $house) {
            $arrayAcl['house'][$house->id] = 1;
        }

        //profils_has_project --> add role id + role participant + pour acl type_profil = user
        $projects = $instance->projects;
        foreach ($projects as $project) {
            $arrayAcl['project'][$project->id] = 1;
        }
        $arrayAcl['user'][auth()->guard('web')->user()->id] = 1;

        return $arrayAcl;
    }

    public function usersdata()
    {

        $instance = Instance::find(session('instanceId'));
        $usersdata = json_decode($instance->getParameter('custom_user_fields'), true) ?? [];
        $data['usersdata'] = $usersdata;
        $deleted = json_decode($instance->getParameter('custom_user_fields_deleted'), true) ?? [];
        $data['deleted'] = $deleted;
        if (request()->isMethod('POST')) {
            $combi = array();
            $names = request()->get('names');
            $inputs = request()->get('inputs');
            $slugs = request()->get('slugs');
            for ($i=0; $i < count($names); $i++) {
                if ($names[$i]!=null) {
                    $slug =  $slugs[$i] ?? Str::slug($names[$i], "-");
                    $combi[$slug] = ['name'=>$names[$i], 'type'=>$inputs[$i]];
                }
            }
            //save deleted fields
            $diff = array_diff(array_keys($usersdata), array_keys($combi));
            if (!empty($diff)) {
                $deleted = [];
                foreach ($diff as $val) {
                    $deleted[$val] = $usersdata[$val];
                }
                $before = json_decode($instance->getParameter("custom_user_fields_deleted"), true);
                $deleted = array_merge($before??[], $deleted);
                // check deleted fields have been or not recycled
                foreach ($combi as $slug => $value) {
                    unset($deleted[$slug]);
                }

                $instance->setParameter("custom_user_fields_deleted", json_encode($deleted));
            }
            //save fields
            $instance->setParameter("custom_user_fields", json_encode($combi));
            session()->flash('usersdata', true);
            return redirect()->route('instance.usersdata');
        }
        // dd($data);
        return view('instances.usersdata', $data);
    }

    public function deleteCustom()
    {
        if (request()->isMethod('POST')) {
            $data = request()->get('postData');
            $type = $data['type'];
            $slug = $data['slug'];
            $instance = Instance::find(session('instanceId'));
            $get = json_decode($instance->getParameter("custom_user_fields_deleted"), true);
            $next = $get[$slug];
            unset($get[$slug]);
            $instance->setParameter("custom_user_fields_deleted", json_encode($get));
            if ($type=='restore') {
                $customs = json_decode($instance->getParameter("custom_user_fields"), true);
                $customs[$slug] = $next;
                $instance->setParameter("custom_user_fields", json_encode($customs));
            } else {
                $users = $instance->users()->get();
                foreach ($users as $user) {
                    $user->deleteParameter("custom_user_field_".$slug);
                }
            }
        }
    }
}
