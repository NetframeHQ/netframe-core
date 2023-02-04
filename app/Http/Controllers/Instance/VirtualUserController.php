<?php
namespace App\Http\Controllers\Instance;

use App\Http\Controllers\BaseController;
use App\Instance;

class VirtualUserController extends BaseController
{
    public function __construct()
    {
        $this->middleware('checkAppActive:virtualUsers');
    }

    public function virtualUsers($userId)
    {
        $instance = Instance::find(session('instanceId'));
        $user = $instance->users()->where('id', '=', $userId)->first();
        $virtualUsers = $user->virtualUsers;

        $data = [
            'user' => $user,
            'virtualUsers' => $virtualUsers,
        ];

        return view('instances.virtual-users.list', $data);
    }

    public function editVirtualUser($userId, $virtualUserId = null)
    {
        $instance = Instance::find(session('instanceId'));
        $user = $instance->users()->where('id', '=', $userId)->first();
        if ($user != null) {
            //$virtualUser = $user->virtualUsers()->where('id', '=', $virtualUserId)->first();
            $virtualUser = $user->virtualUsers()->findOrNew($virtualUserId);

            if (request()->isMethod('POST')) {
                $rules = config('validation.virtualuser/edit');
                // check email change
                if ($virtualUser != null && $virtualUser->email != request()->get('email')) {
                    $rules = array_merge($rules, config('validation.virtualuser/email'));
                }

                $virtualUser->instances_id = session('instanceId');
                $virtualUser->firstname = request()->get('firstname');
                $virtualUser->lastname = request()->get('lastname');
                $virtualUser->email = request()->get('email');
                $virtualUser->active = (request()->has('active') && request()->get('active') == 1) ? 1 : 0;

                // verify email unicity from users and virtual users
                if ($virtualUserId == null) {
                    $rules['password']  = config('validation.virtualuser/create.password');
                    $rules = array_merge($rules, config('validation.virtualuser/email'));
                }
                $validate = validator(request()->all(), $rules)->validate();

                if (!empty(request()->get('password'))) {
                    $virtualUser->password = bcrypt(request()->get('password'));
                }

                $virtualUser->save();

                return redirect()->route('instance.virtualuser.list', ['usersId' => $user->id]);
            }

            return view('instances.virtual-users.edit', [
                'user' => $user,
                'virtualUser' => $virtualUser,
            ]);
        }
    }

    public function deleteVirtualUser($virtualUserId)
    {
        $deleteResult = false;

        $instance = Instance::find(session('instanceId'));
        $virtualUser = $instance->virtualUsers()->where('id', '=', $virtualUserId)->first();
        if ($virtualUser != null) {
            $virtualUser->delete();
            $deleteResult = true;
        }

        return response()->json([
            'delete' => $deleteResult,
            'targetId' => 'virtualUser-' . $virtualUserId
        ]);
    }

    public function disableVirtualUser()
    {
        $virtualUserId = request()->get('profileId');
        $instance = Instance::find(session('instanceId'));
        $virtualUser = $instance->virtualUsers()->where('id', '=', $virtualUserId)->first();
        if ($virtualUser != null) {
            $virtualUser->active = request()->get('stateTo');
            $virtualUser->save();

            return response()->json([
                'active' => $virtualUser->active
            ]);
        }
    }
}
