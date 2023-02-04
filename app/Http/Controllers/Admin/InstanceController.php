<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Instance;
use App\User;
use Illuminate\Support\Facades\Input;
use App\Application;

class InstanceController extends Controller
{

    /**
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        $data = [];

        $data["instances"] = Instance::orderBy('created_at', 'DESC')->paginate(20);
        $data["hide_pagin"] = 0;

        return view('admin.instance.home', $data);
    }

    public function updateApps()
    {
        $instance_id = request()->get('instance_id');
        $instance = Instance::find($instance_id);


        $apps = Application::get();
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
        return redirect()->route('admin.instances.details', ['id' => $instance->id]);
    }

    public function searchInstance()
    {
        $data = [];
        if (request()->isMethod('POST')) {
            $data["instances"] = Instance::where('name', 'like', '%'.request()->get('search').'%')->get();
            $data["hide_pagin"] = 1;

            return view('admin.instance.home', $data);
        }
    }

    public function detailInstance($id = null, $type = null)
    {
        $data = [];
        if (request()->isMethod('POST')) {
            if (Input::get('usersList') || Input::get('invitationSent')) {
                $id = Input::get('idInstance');
            }
        }

        $instance = Instance::find($id);
        $data["instance"] = $instance;
        $data['apps'] = Application::get();

        if ($type == 'usersList') {
            $data["users"] = $instance->users()->orderBy('last_connexion', 'DESC')->get();
        } elseif ($type == 'invitationSent') {
            $data["boardings"] = $instance->boardings;
        }

        return view('admin.instance.details', $data);
    }

    public function userPassword($id)
    {
        $user = User::find($id);

        $result = false;

        if ($user != null) {
            if (request()->isMethod('POST')) {
                $user->password = bcrypt(request()->get('password'));
                $user->save();
                $result = 'passUpdated';
            }
        }

        $data = [
            'user' => $user,
            'result' => $result,
        ];

        return view('admin.instance.update-user-pass', $data);
    }

    public function logAs($id)
    {
        $user = User::find($id);
        if ($user != null) {
            auth('web')->login($user);
            return redirect()->route('netframe.workspace.home');
        }
    }
}
