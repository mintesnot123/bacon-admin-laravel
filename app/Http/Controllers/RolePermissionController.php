<?php

namespace App\Http\Controllers;

use App\RolePermission;


use Illuminate\Http\Request;

    

class RolePermissionController extends Controller

{ 

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

         $this->middleware('permission:role-permission-list|role-permission-create|role-permission-edit|role-permission-delete', ['only' => ['index','show']]);

         $this->middleware('permission:role-permission-create', ['only' => ['create','store']]);

         $this->middleware('permission:role-permission-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:role-permission-delete', ['only' => ['destroy']]);

    }

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {

        $permissions = RolePermission::latest()->paginate(10);



        return view('admin.role_permissions.index',compact('permissions'))

            ->with('i', (request()->input('page', 1) - 1) * 10);

    }

    

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        return view('admin.role_permissions.create');

    }

    

    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        request()->validate([

            'name' => 'required',

            'guard_name' => 'required',

        ]);

    

        RolePermission::create($request->all());

    

        return redirect()->route('role-permissions.index')

                        ->with('success','Permission created successfully.');

    }

    

    /**

     * Display the specified resource.

     *

     * @param  \App\RolePermission  $Permission

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

    	$permission = RolePermission::find($id);

        return view('admin.role_permissions.show',compact('permission'));

    }

    

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\RolePermission  $permission

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {
    	$permission = RolePermission::find($id);
        return view('admin.role_permissions.edit',compact('permission'));

    }

    

    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\RolePermission  $permission

     * @return \Illuminate\Http\Response

     */

    public function update($id, Request $request)

    {

         request()->validate([

            'name' => 'required',

            'guard_name' => 'required',

        ]);

        $permission = RolePermission::find($id);

        $permission->update($request->all());

        return redirect()->route('role-permissions.index')

                        ->with('success','Permission updated successfully');

    }

    

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\RolePermission  $permission

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {
    	
    	$permission = RolePermission::find($id);

    	if($permission):
    		$permission->delete();
    		return redirect()->route('role-permissions.index')
        	->with('success','Permission deleted successfully');
    	else:

    		return redirect()->route('role-permissions.index')
        	->with('error','Permission delete failed');
    	endif;	
    }

}
