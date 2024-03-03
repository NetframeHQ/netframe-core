<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Database\CacheQueryBuilder;
use Carbon\Carbon;
use App\Helpers\ColorsHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Lang;

class Instance extends Model
{
    use CacheQueryBuilder;

    protected $table = "instances";
    protected $dates = ['begin_date'];
    protected $fillable = [];
    protected $type = 'instance';
    public $rolesLangKey = 'members.instanceRoles.';

    public function getType()
    {
        return $this->type;
    }

    public function admins()
    {
        return $this->belongsToMany('App\User', 'users_has_instances', 'instances_id', 'users_id')
            ->withPivot(['access_granted', 'roles_id'])
            ->wherePivotIn('roles_id', [1, 2])
            ->withTimestamps();
    }

    public function boardings()
    {
        return $this->hasMany('App\Boarding', 'instances_id', 'id');
    }

    public function apps()
    {
        return $this->belongsToMany('App\Application', 'instances_has_apps', 'instances_id', 'apps_id');
    }

    /*
     * return bool if app is active
     */
    public function appActive($appSlug)
    {
        $activeApp = $this->apps()->where('slug', '=', $appSlug)->first();
        return  ($activeApp != null) ? true : false;
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function billing_infos()
    {
        return $this->hasOne('App\BillingInfos', 'instances_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'users_has_instances', 'instances_id', 'users_id')
            ->withPivot(['access_granted', 'roles_id'])
            ->withTimestamps();
    }

    public function activeUsers()
    {
        return $this->belongsToMany('App\User', 'users_has_instances', 'instances_id', 'users_id')
            ->where('active', '=', 1)
            ->withPivot(['access_granted', 'roles_id'])
            ->withTimestamps();
    }

    public function virtualUsers()
    {
        return $this->hasMany('App\VirtualUser', 'instances_id', 'id');
    }

    public function workflowDetailsActions()
    {
        return $this->hasMany('App\WorkflowDetailsAction', 'instances_id', 'id');
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function users_references()
    {
        return $this->hasMany('App\UsersReference', 'instances_id', 'id');
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function users_groups()
    {
        return $this->hasMany('App\UsersGroup', 'instances_id', 'id');
    }

    public function interests()
    {
        return $this->hasMany('App\Interest', 'instances_id', 'id');
    }

    public function projects()
    {
        return $this->hasMany('App\Project', 'instances_id', 'id')->orderBy('title');
    }

    public function communities()
    {
        return $this->hasMany('App\Community', 'instances_id', 'id')->orderBy('name');
    }

    public function houses()
    {
        return $this->hasMany('App\House', 'instances_id', 'id')->orderBy('name');
    }

    public function channels()
    {
        return $this->hasMany('App\Channel', 'instances_id', 'id');
    }

    public function news()
    {
        return $this->hasMany('App\News', 'instances_id', 'id');
    }

    public function events()
    {
        return $this->hasMany('App\TEvent', 'instances_id', 'id');
    }

    public function offers()
    {
        return $this->hasMany('App\Offer', 'instances_id', 'id');
    }

    public function medias()
    {
        return $this->hasMany('App\Media', 'instances_id', 'id');
    }

    public function newsfeeds()
    {
        return $this->hasMany('App\NewsFeed', 'instances_id', 'id');
    }

    public function friends()
    {
        return $this->hasMany('App\Friend', 'instances_id', 'id');
    }

    public function likes()
    {
        return $this->hasMany('App\Like', 'instances_id', 'id');
    }

    public function shares()
    {
        return $this->hasMany('App\Share', 'instances_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment', 'instances_id', 'id');
    }

    public function notifications()
    {
        return $this->hasMany('App\Notif', 'instances_id', 'id');
    }

    public function playlists()
    {
        return $this->hasMany('App\Playlist', 'instances_id', 'id');
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function playlists_items()
    {
        return $this->hasMany('App\PlaylistItem', 'instances_id', 'id');
    }

    public function subscriptions()
    {
        return $this->hasMany('App\Subscription', 'instances_id', 'id');
    }

    public function trackings()
    {
        return $this->hasMany('App\Tracking', 'instances_id', 'id');
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function tracking_reports()
    {
        return $this->hasMany('App\TrackingReport', 'instances_id', 'id');
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function chat_settings()
    {
        return $this->hasMany('App\ChatSettings', 'instances_id', 'id');
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function messages_mail()
    {
        return $this->hasMany('App\MessageMail', 'instances_id', 'id');
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function messages_mail_groups()
    {
        return $this->hasMany('App\MessageGroup', 'instances_id', 'id');
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function netframe_actions()
    {
        return $this->hasMany('App\NetframeAction', 'instances_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany('App\Tag', 'instances_id', 'id');
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function report_abuses()
    {
        return $this->hasMany('App\ReportAbuse', 'instances_id', 'id');
    }

    public function bookmarks()
    {
        return $this->hasMany('App\Bookmark', 'instances_id', 'id');
    }

    public function buzz()
    {
        return $this->hasMany('App\Buzz', 'instances_id', 'id');
    }

    public function parameters()
    {
        return $this->hasMany('App\InstanceParameter', 'instances_id', 'id');
    }

    public function metrics()
    {
        return $this->hasMany('App\InstanceMetric', 'instances_id', 'id');
    }

    public function accountents()
    {
        return $this->belongsToMany('App\Accountent', 'accountents_has_instances', 'instances_id', 'accountents_id');
    }

    public function createNew($instanceName)
    {
        $instanceSlug = str_slug($instanceName);

        // test if instance subdomain is not reserved
        $reservedSubDomains = config('instances.reservedSubDomains');
        if (!empty(env('DEFAULT_SUBDOMAIN'))) {
            $reservedSubDomains[] = env('DEFAULT_SUBDOMAIN');
        }
        if (in_array($instanceSlug, $reservedSubDomains)) {
            $instanceSlug .= '1';
        }

        //check slug unicity
        $slugCount = count(Instance::whereRaw("slug REGEXP '^{$instanceSlug}([0-9]*)?$'")->get());
        $instanceSlug = ($slugCount > 0) ? $instanceSlug.$slugCount : $instanceSlug;

        $this->name = $instanceName;
        $this->slug = $instanceSlug;
        $this->begin_date = Carbon::now()->addDays(90)->format('Y-m-d H:i:s');
        $this->save();

        $this->makeDefaultTaskTemplate();
        $this->makeInstanceParameters();
        $this->makeAppPortal();

        return $this;
    }

    public function getUrl()
    {
        return env('APP_BASE_PROTOCOL') . '://' . $this->slug . '.' . env('APP_BASE_DOMAIN');
    }

    public function getBoardingUrl()
    {
        return env('APP_BASE_PROTOCOL') . '://' . $this->slug . '.' . env('APP_BASE_DOMAIN')
            . '/boarding/key/' . $this->getParameter('boarding_invite_key');
    }

    public function getParameter($parameterName, $returnObj = null)
    {
        $parameter = $this->parameters->where('parameter_name', '=', $parameterName)->first();
        if ($parameter != null) {
            if ($returnObj != null) {
                return $parameter;
            } else {
                return $parameter->parameter_value;
            }
        } else {
            return null;
        }
    }

    public function setParameter($parameterName, $value)
    {
        $existsParameter = $this->getParameter($parameterName, true);
        if ($existsParameter != null) {
            $existsParameter->parameter_value = $value;
            $existsParameter->save();
        } else {
            $parameter = new InstanceParameter();
            $parameter->instances_id = $this->id;
            $parameter->parameter_name = $parameterName;
            $parameter->parameter_value = $value;
            $parameter->save();
        }
    }

    public function deleteParameter($parameterName)
    {
        $existsParameter = $this->getParameter($parameterName, true);
        if ($existsParameter != null) {
            $existsParameter->delete();
        }
    }

    public function billings()
    {
        return $this->hasMany('App\Billing', 'instances_id', 'id');
    }

    public function getMetricValue($metricName, $metricDate)
    {
        $metric = $this->metrics
            ->where('metric_name', '=', $metricName)
            ->where('metric_date', '=', $metricDate)
            ->first();
        if ($metric != null) {
            return $metric->metric_value;
        } else {
            return null;
        }
    }

    public function getMetric($metricName, $metricDate)
    {
        $metric = $this->metrics
        ->where('metric_name', '=', $metricName)
        ->where('metric_date', '=', $metricDate)
        ->first();
        if ($metric != null) {
            return $metric;
        } else {
            return null;
        }
    }

    private function makeInstanceParameters()
    {
        $defaultParameters = config('instances.default_config');
        $defaultParameters['boarding_invite_key'] = str_random(50);

        foreach ($defaultParameters as $parameter => $value) {
            $param = new InstanceParameter();
            $param->instances_id = $this->id;
            $param->parameter_name = $parameter;
            $param->parameter_value = $value;
            $param->save();
        }
    }

    private function makeAppPortal()
    {
        $apps = Application::where('default_active', '=', 1)->get();
        foreach ($apps as $application) {
            $this->apps()->attach($application->id);
        }
    }

    /*
     * create default task template for this instance
     */
    private function makeDefaultTaskTemplate()
    {
        $defaultTemplateName = trans('task.sample.title');

        $fields = [
            Str::slug(trans('task.sample.colTxt'), "-") => [
                'name' => trans('task.sample.colTxt'),
                'type' => 'text',
                'required' => true,
            ],
            Str::slug(trans('task.sample.colEmail'), "-") => [
                'name' => trans('task.sample.colEmail'),
                'type' => 'email',
                'required' => true,
            ],
            Str::slug(trans('task.sample.colDate'), "-") => [
                'name' => trans('task.sample.colDate'),
                'type' => 'date',
                'required' => true,
            ],
            Str::slug(trans('task.sample.colFile'), "-") => [
                'name' => trans('task.sample.colFile'),
                'type' => 'file',
                'required' => false,
            ],
        ];

        $user = auth()->guard('web')->user();
        $template = new Template();
        $template->linked = true;
        $template->name = $defaultTemplateName;
        $template->instances_id = $this->id;
        $template->cols = json_encode($fields);
        $template->language = Lang::locale();
        $template->has_medias = 1;
        $template->save();
    }

    public function newUser()
    {
        $usersMetric = $this->getMetric('total_users', date('Y-m-d'));
        if ($usersMetric == null) {
            $usersMetric = new InstanceMetric();
            $usersMetric->instances_id = $this->id;
            $usersMetric->metric_name = 'total_users';
            $usersMetric->metric_value = $this->users()->count();
            $usersMetric->metric_date = date('Y-m-d');
            $usersMetric->save();
        } else {
            $usersMetric->metric_value = $this->users()->count();
            $usersMetric->save();
        }
    }

    public function getMediaSize()
    {
        $mediaSize = round($this->getParameter('medias_size') / 1024 / 1024 / 1024, 0);

        return $mediaSize;
    }

    /**
        Methode gestion des erreurs de paiement
        0 => Période d'essai et sans mode de paiement
        1 => Ok
        2 => Désabonnement
        3 => Erreur de paiement < 15j
        4 => Erreur de paiement > 15j

    */
    public function subscribeValid()
    {
        // exception for older instances
        if ($this->active == 0) {
            return 2;
        }

        if ($this->created_at < config('billing.oldInstancesDate')) {
            return 1;
        }

        // free instance
        if ($this->getParameter('billing_offer') == 'free' && strtotime($this->begin_date) > strtotime(now())) {
            return 1;
        } elseif (in_array($this->getParameter('billing_offer'), ['unsubscribe', 'free'])) {
            return 2;
        }

        if ($this->getParameter('billing_offer') == 'forever') {
            return 1;
        } else {
            \Log::error('testBill');
            $bill = \DB::table('billings')
                ->where('instances_id', $this->id)
                ->where('paid', '0')
                ->orderBy('created_at', 'asc')
                ->first();
            if ($bill != null) {
                if (date_diff(new \DateTime($bill->created_at), new \DateTime())->format("%R%a") <= 15) {
                    return 3;
                } else {
                    return 4;
                }
            } else {
                return 4;
            }
        }

        return 1;
    }

    // before end of trial period
    public function remainingDays()
    {
        $sv = $this->subscribeValid();
        if ($sv == 0) {
            return date_diff(new \DateTime(), new \DateTime($this->begin_date))->format("%R%a");
        } elseif ($sv==3) {
            $bill = \App\Billing::where('instances_id', '=', $this->id)->orderBy('created_at', 'desc')->first();
            return 15 + date_diff(new \DateTime(), new \DateTime($bill->created_at))->format("%R%a");
        }
        return -1;
    }

    public function administrators()
    {
        return $this->users()->where('roles_id', '<', '3');
    }


    public function getLikeEmojis()
    {
        $ids = json_decode($this->getParameter('like_buttons'), true);
        if ($ids==null) {
            $ids = config('instances.defaultEmojis');//\App\Emoji::limit(5)->get();
        }

        $keepOrderIds = implode(',', $ids);
        return \App\Emoji::whereIn('id', $ids)
            ->orderByRaw(\DB::raw("FIELD(id, $keepOrderIds)"))
            ->get();
    }

    //public function hasApplication(string $slug): boolean
    public function hasApplication(string $slug)
    {
        /*
        return array_reduce(
            $this->apps,
            function(boolean $hasApp, Application $application) use ($slug) {
                return $hasApp || $slug === $application->slug;
            },
            false
        );
        */

        try {
            $query = Application::where('slug', '=', $slug);
            $application = $query->first();
            return $this->apps->contains($application->id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function tasks()
    {
        return $this->hasMany('App\TaskTable', 'instances_id', 'id');
    }

    public function taskTemplates()
    {
        return $this->hasMany('App\Template', 'instances_id', 'id');
    }

    public function allStats()
    {
        return $this->hasMany('App\Stat', 'instances_id', 'id');
    }

    public function stats()
    {
        return $this->morphMany('App\Stat', 'entity');
    }

    public function listRoles($roleKey = null)
    {
        if ($roleKey == null) {
            return config('rights.instance');
        } else {
            return config('rights.instance.' . $roleKey);
        }
    }

    public function compileCustomCss()
    {
        $theme = $this->getParameter('css_theme');

        if (!empty($theme)) {
            $defaultCss = config('themes.themes.' . $theme);
            $paramCss = json_decode($this->getParameter('css_colors_2018'), true);

            if (isset($paramCss[$theme])) {
                // add missing colors
                foreach ($defaultCss['baseColors'] as $keyMode => $themeDefaults) {
                    foreach ($themeDefaults as $cssClass => $defaultValue) {
                        $cssClass = ($keyMode == 'dark') ? $cssClass . 'Dark' : $cssClass;
                        $paramCss[$theme][$cssClass] = ColorsHelper::convertRgbToHex($defaultValue);
                        if (!isset($paramCss[$theme][$cssClass])) {
                            $paramCss[$theme][$cssClass] = ColorsHelper::convertRgbToHex($defaultValue);
                        }
                    }
                }

                // force colors for compilation if disable mode or theme not switchable
                if (!empty($paramCss[$theme]['disableMode']) || !$defaultCss['switchable']) {
                    if ($paramCss[$theme]['disableMode'] == 'dark' || !$defaultCss['switchable']) {
                        $paramCss[$theme]['light']['baseColor'] = $defaultCss['baseColors']['light']['baseColor'];
                        $paramCss[$theme]['dark']['primaryColor'] = $paramCss[$theme]['light']['primaryColor'];
                        $paramCss[$theme]['dark']['accentColor'] = $paramCss[$theme]['light']['accentColor'];
                        $paramCss[$theme]['dark']['bgColor'] = $paramCss[$theme]['light']['bgColor'];
                        $paramCss[$theme]['dark']['baseColor'] = $defaultCss['baseColors']['light']['baseColor'];
                    } elseif ($paramCss[$theme]['disableMode'] == 'light') {
                        $paramCss[$theme]['light']['primaryColor'] = $paramCss[$theme]['dark']['primaryColor'];
                        $paramCss[$theme]['light']['accentColor'] = $paramCss[$theme]['dark']['accentColor'];
                        $paramCss[$theme]['light']['bgColor'] = $paramCss[$theme]['dark']['bgColor'];
                        $paramCss[$theme]['light']['baseColor'] = $defaultCss['baseColors']['dark']['baseColor'];
                        $paramCss[$theme]['dark']['baseColor'] = $defaultCss['baseColors']['dark']['baseColor'];
                    }
                } else {
                    $paramCss[$theme]['light']['baseColor'] = $defaultCss['baseColors']['light']['baseColor'];
                    $paramCss[$theme]['dark']['baseColor'] = $defaultCss['baseColors']['dark']['baseColor'];
                }

                try {
                    // COLOR GENERATION

                    $cssContent = '';
                    if (isset($paramCss[$theme]['light'])) {
                        $cssContent .= '
                            @media (prefers-color-scheme: light) {
                                :root {
                                    --nf-primaryColor: ' . $paramCss[$theme]['light']['primaryColor'] . ' !important;
                                    --nf-bgColor: ' . $paramCss[$theme]['light']['bgColor'] . ' !important;
                                    --nf-accentColor: ' . $paramCss[$theme]['light']['accentColor'] . ' !important;
                                    --nf-baseColor: ' . $paramCss[$theme]['light']['baseColor'] . ' !important;
                                    --nf-baseColor-dark: ' . $paramCss[$theme]['dark']['baseColor'] . ' !important;
                                    --nf-baseColor-light: ' . $paramCss[$theme]['light']['baseColor'] . ' !important;
                                }
                            }';
                    }
                    if (isset($paramCss[$theme]['dark'])) {
                        $cssContent .= '
                            @media (prefers-color-scheme: dark) {
                                :root {
                                    --nf-primaryColor: ' . $paramCss[$theme]['dark']['primaryColor'] . ' !important;
                                    --nf-bgColor: ' . $paramCss[$theme]['dark']['bgColor'] . ' !important;
                                    --nf-accentColor: ' . $paramCss[$theme]['dark']['accentColor'] . ' !important;
                                    --nf-baseColor: ' . $paramCss[$theme]['dark']['baseColor'] . ' !important;
                                    --nf-baseColor-dark: ' . $paramCss[$theme]['dark']['baseColor'] . ' !important;
                                    --nf-baseColor-light: ' . $paramCss[$theme]['light']['baseColor'] . ' !important;
                                }
                            }
                        ';
                    }

                    $storage_dir = env('NETFRAME_DATA_PATH', base_path()).'/storage/uploads/instances-css';
                    if (!file_exists($storage_dir)) {
                        $result = \File::makeDirectory($storage_dir, 0775, true);
                    }

                    \File::put(
                        $storage_dir . '/' . $this->id . '-' . $this->slug . '.css',
                        $cssContent,
                        0775
                    );
                    /*
                    chmod(
                        $storage_dir . '/' . $this->id . '-' . $this->slug . '.css',
                        0775
                    );
                    */
                } catch (Exception $e) {
                    $error_message = $e->getMessage();
                }
            } else {
                // if no custom set it in db
                $this->setParameter('custom_css_2018', 0);
            }
        } else {
            // if no custom set it in db
            $this->setParameter('custom_css_2018', 0);
        }
    }
}
