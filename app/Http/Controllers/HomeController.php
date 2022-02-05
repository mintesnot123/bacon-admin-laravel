<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Session;
use App\User;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $user = User::find(Auth::user()->id);
        $roles = Role::pluck('name','id')->all();
        $userRole = $user->roles->pluck('name','id')->all();

        //print_r($userRole);
        // die();

        foreach ($userRole as $key => $value) {
            //Session::put('UserRole', $key);
            switch ($key) {
                case '1':
                    return view('admin.super_admin');
                    break;
                case '2':
                    return view('admin.admin');
                    break;
                case '3':
                    return view('admin.user');
                    break;
                default:
                    return view('admin.user');
                    break;
            }
        }

    }
}
