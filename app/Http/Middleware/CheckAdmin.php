<?php

namespace App\Http\Middleware;

use Closure;
use App\Admin;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $role = null)
    {
        if (!config('admin.freeAccess')) {
            if (auth()->guard('admin')->guest()) {
                return redirect()->guest('management/login');
            } else {
                if (auth()->guard('admin')->check()) {
                    if (auth()->guard('admin')->viaRemember()) {
                    }
                }
            }
        } elseif (config('app.env') == 'local') {
            // test if admin exists
            $admin = Admin::find(1);
            if ($admin == null) {
                \DB::table('admins')->insert([
                    [
                        'id' => 1,
                        'username' => 'Admin Netframe',
                        'email' => 'admin@netframe.co',
                        'password' => bcrypt('Iam@dmin'),
                        'created_at' => Date('Y-m-d H:i:s'),
                        'updated_at' => Date('Y-m-d H:i:s'),
                    ],
                ]);
                $admin = Admin::find(1);
            }
            auth('admin')->login($admin);
        }
        return $next($request);
    }
}
