<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Admin;

class AdminsController extends Controller
{

    public function listing()
    {
        $data = [];

        $admins = Admin::select()
            ->orderBy('username')
            ->get();

        $data['admins'] = $admins;

        return view('admin.admins.list', $data);
    }

    public function edit($id = null)
    {
        $data = [];

        if ($id !== null) {
            $admin = Admin::findOrFail($id);
        } else {
            $admin = new Admin();
        }

        if (request()->isMethod('POST')) {
            $validationRules = config('validation.admin/admin/add');

            if ($id != null) {
                $validationRules = config('validation.admin/admin/update');
            }

            if ($id != null && request()->get('email') != $admin->email) {
                $validationRules = array_merge(config('validation.admin/admin/update/email'), $validationRules);
            }
            if ($id != null && request()->get('password') != '') {
                $validationRules = array_merge(config('validation.admin/admin/update/password'), $validationRules);
            }

            $validator = validator(request()->all(), $validationRules);

            if ($validator->fails()) {
                $admin->username = request()->get('username');
                $admin->email = request()->get('email');

                $data['admin'] = $admin;

                return view('admin.admins.form', $data)->withErrors($validator);
            } else {
                $admin->username = request()->get('username');
                $admin->email = request()->get('email');
                if (($id != null && request()->get('password') != null) || request()->get('password') != null) {
                    $admin->password = bcrypt(request()->get(trim('password')));
                    ;
                }
                $admin->save();

                return $this->listing();
            }
        }

        $data['admin'] = $admin;
        return view('admin.admins.form', $data);
    }

    public function delete($id)
    {
        if ($id != 1) {
            $admin = Admin::findOrFail($id);
            $admin->delete();

            return response()->json(array(
                        'delete' => true,
                        'targetId' => "#admin-".$id
            ));
        }
    }
}
