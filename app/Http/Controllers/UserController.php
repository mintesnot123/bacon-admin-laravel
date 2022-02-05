<?php

namespace App\Http\Controllers;
    

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\User;

use Spatie\Permission\Models\Role;

use DB;

use Hash;

use Auth;

    

class UserController extends Controller

{

      /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

         $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);

         $this->middleware('permission:user-create', ['only' => ['create','store']]);

         $this->middleware('permission:user-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:user-delete', ['only' => ['destroy']]);

    }

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {

        $data = User:: leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
        ->whereIn('model_has_roles.role_id',[1,2])->orderBy('id','DESC')->paginate(20);

        return view('admin.users.index',compact('data'))

            ->with('i', ($request->input('page', 1) - 1) * 20);

    }

    

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        $user = User::find(Auth::user()->id);
        $userRole = $user->roles->pluck('id','id')->toArray();

        // print_r($userRole);
        foreach ($userRole as $key => $value) {
            if($key==1):
                $roles = Role::whereIn('id',[1,2])->pluck('name','name');
            elseif($key==2):
                $roles = Role::whereIn('id',[2])->pluck('name','name');
            elseif($key==3):
                $roles = Role::whereIn('id',[3])->pluck('name','name');
             else:
                $roles = Role::whereIn('id',[4])->pluck('name','name');
            endif;
        }

        //$roles = Role::pluck('name','name')->all();

        return view('admin.users.create',compact('roles'));
    }

    

    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        $this->validate($request, [

            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'mobile_no' => 'required|unique:users,mobile_no',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);

    
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['status']=1;

        $user = User::create($input);

        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')

            ->with('success','User created successfully');
    }

    

    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        $user = User::find($id);

        return view('admin.users.show',compact('user'));
    }

    

    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        $user = User::find($id);

        $userRole = $user->roles->pluck('name','name')->all();

        $role_user = User::find(Auth::user()->id);
        $userRole = $role_user->roles->pluck('id','id')->toArray();

        // print_r($userRole);
        foreach ($userRole as $key => $value) {
            if($key==1):
                $roles = Role::whereIn('id',[1,2])->pluck('name','name');
            elseif($key==2):
                $roles = Role::whereIn('id',[2])->pluck('name','name');
            elseif($key==3):
                $roles = Role::whereIn('id',[3])->pluck('name','name');
             else:
                $roles = Role::whereIn('id',[4])->pluck('name','name');
            endif;
        }

        //$roles = Role::pluck('name','name')->all();

        return view('admin.users.edit',compact('user','roles','userRole'));
    }

    

    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, $id)

    {

        $this->validate($request, [

            'name' => 'required',

            'email' => 'required|email|unique:users,email,'.$id,

            'mobile_no' => 'required|unique:users,mobile_no,'.$id,

            'password' => 'same:confirm-password',

            'roles' => 'required'

        ]);

    

        $input = $request->all();

       

        if(!empty($input['password'])){ 

            $input['password'] = Hash::make($input['password']);

        }else{

            unset($input['password']);
            unset($input['confirm-password']);
            //$input = array_except($input,array('password'));    
        }

        $input['status']=1;

        $user = User::find($id);

        $user->update($input);

        DB::table('model_has_roles')->where('model_id',$id)->delete();

    

        $user->assignRole($request->input('roles'));

    

        return redirect()->route('users.index')

            ->with('success','User updated successfully');

    }

    

    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        User::find($id)->delete();

        return redirect()->route('users.index')

            ->with('success','User deleted successfully');

    }

}