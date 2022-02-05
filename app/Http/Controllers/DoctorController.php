<?php

namespace App\Http\Controllers;
    

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Traits\UploadTrait;

use Illuminate\Support\Str;

use App\User;

use Spatie\Permission\Models\Role;
use DB;
use Auth;
use Hash;
use Log; 
use STDclass;
use Session;
use Storage;

    

class DoctorController extends Controller

{

    use UploadTrait;
      /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

         $this->middleware('permission:doctor-list|doctor-create|doctor-edit|doctor-delete', ['only' => ['index','store']]);

         $this->middleware('permission:doctor-create', ['only' => ['create','store']]);

         $this->middleware('permission:doctor-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:doctor-delete', ['only' => ['destroy']]);

    }

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {


        $search = array_filter($request->all());
        unset($search['_token']);

        if($request->has('keyword')){
           Session::put("search", $search);
           $search = (object) Session::get('search'); 
        }else{
          $search = (object) Session::get('search'); 
        }


        $data = User:: leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id');

        if(isset($search->keyword)){

           $search = $search->keyword;
           $data = $data->where(function($q) use ($search){
                  $q->orWhere('users.name', 'like', '%'.$search.'%')
                 // ->orWhere('users.email', 'like', '%'.$search.'%') 
                ->orWhere('users.mobile_no', 'like', '%'.$search.'%'); 
            });

        }


        $data = $data->whereIn('model_has_roles.role_id',[3])->orderBy('id','DESC')->paginate(20);

        return view('admin.doctors.index',compact('data'))

            ->with('i', ($request->input('page', 1) - 1) * 20);

    }

    

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        $doctor = User::find(Auth::user()->id);
        $userRole = $doctor->roles->pluck('id','id')->toArray();

        $roles = Role::whereIn('id',[3])->pluck('name','name');
        //$roles = Role::pluck('name','name')->all();

        return view('admin.doctors.create',compact('roles'));
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
        $doctor = User::create($input);

        //print_r($doctor);

        if($doctor){
        	DB::table('user_doctors')->insertGetId([
                'user_id' => $doctor->id,
                'posted_by' => Auth::user()->id,
                'device_id' => 2,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        $doctor->assignRole($request->input('roles'));

        //die();

        return redirect()->route('doctors.index')

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

        $doctor = User::find($id);

        $doctor = User:: leftJoin('user_doctors', 'user_doctors.user_id', '=', 'users.id')
        ->select('users.*','user_doctors.*','model_has_roles.*','specialities.name as specialty_name','roles.name as role_name')
        ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
        ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->leftJoin('specialities', 'specialities.id', '=', 'user_doctors.speciality_id')
        ->where('users.id',$id)->whereIn('model_has_roles.role_id',[3])->first();

        $files = DB::table('user_files')->where('user_id',$id)->where('type_id',1)->where('status',1)->orderBy('id', 'ASC')->get();   

        // echo'<pre>';
        // print_r($doctor);
        // echo'</pre>';





        return view('admin.doctors.show',compact('doctor','files'));
    }

    

    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        //$doctor = User::find($id);


        $doctor = User:: leftJoin('user_doctors', 'user_doctors.user_id', '=', 'users.id')
        ->select('users.*','user_doctors.*','model_has_roles.*','specialities.name as specialty_name','roles.name as role_name')
        ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
        ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->leftJoin('specialities', 'specialities.id', '=', 'user_doctors.speciality_id')
        ->where('users.id',$id)->whereIn('model_has_roles.role_id',[3])->first();

        $userRole = $doctor->roles->pluck('name','name')->all();

        //$roles = Role::pluck('name','name')->all();
        $doctor->id=$id;
        // echo "<pre>";
        // printf($doctor);
        // echo "</pre>";

        $roles = Role::whereIn('id',[3])->pluck('name','name');

        $specialities =  array('' => 'Select Specialty') + DB::table('specialities')->where('status',1)->pluck('name','id')->toArray();

        return view('admin.doctors.edit',compact('doctor','roles','userRole','specialities'));
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

            'roles' => 'required',

            //'photo1'     =>  'mimes:jpeg,png,jpg,gif,pdf|max:20048',

           // 'photo2'     =>  'mimes:jpeg,png,jpg,gif,pdf|max:20048',

            //'photo3'     =>  'mimes:jpeg,png,jpg,gif,pdf|max:20048',

            //'photo4'     =>  'mimes:jpeg,png,jpg,gif,pdf|max:20048'

        ]);

    
        $input = $request->all(); 

        if(!empty($input['password'])){ 

            $input['password'] = Hash::make($input['password']);

        }else{

            unset($input['password']);
            unset($input['confirm-password']);
            //$input = array_except($input,array('password'));    
        }
      
        $doctor = User::find($id);

        $photo = '';      
        if($request->hasFile('photo'))
        {
            if($request->file('photo')->isValid())
            { 
                $photo  = date('Ymdhis').'.'.$request->file('photo')->getClientOriginalExtension();
                $public_url = public_path();
                // return response()->json([ 'data' =>$photo, 'success' => 1], 200);
                if($request->file('photo')->move($public_url.'/img/users/', $photo))
                {      
                    $input['photo'] = '/img/users/'.$photo;         
                }
            }
                        
        } 

        $signature = '';      
        if($request->hasFile('signature'))
        {
            if($request->file('signature')->isValid())
            { 
                $signature  = date('Ymdhis').'.'.$request->file('signature')->getClientOriginalExtension();
                $public_url = public_path();
                // return response()->json([ 'data' =>$photo, 'success' => 1], 200);
                if($request->file('signature')->move($public_url.'/img/signature/', $signature))
                {      
                    $signature = '/img/signature/'.$signature;         
                }
            }
                        
        } 

        $doctor->update($input);


        $count = DB::table('user_doctors')->where('user_id',$doctor->id)->count();

        if($count){
            DB::table('user_doctors')
            ->where('user_id',$doctor->id)
            ->update([
                'degree' => $request->input('degree'),
                'bmdc_regi_no' => $request->input('bmdc_regi_no'),
                'speciality_id' => $request->input('speciality_id'),
                'relevant_degree' => $request->input('relevant_degree'),
                'sex' => $request->input('sex'),
                'age' => $request->input('age'),
                'institute_name' => $request->input('institute_name'),
                'chamber_address' => $request->input('chamber_address'),
                'reference' => $request->input('reference'),
                'note' => $request->input('note'),
                'signature' => $signature,
                'posted_by' => Auth::user()->id
            ]);

        }else{

             DB::table('user_doctors')->insertGetId([
                'degree' => $request->input('degree'),
                'bmdc_regi_no' => $request->input('bmdc_regi_no'),
                'speciality_id' => $request->input('speciality_id'),
                'relevant_degree' => $request->input('relevant_degree'),
                'sex' => $request->input('sex'),
                'age' => $request->input('age'),
                'institute_name' => $request->input('institute_name'),
                'chamber_address' => $request->input('chamber_address'),
                'reference' => $request->input('reference'),
                'note' => $request->input('note'),
                'signature' => $signature,
                'user_id' => $doctor->id,
                'posted_by' => Auth::user()->id,
                'device_id' => 2,
                'created_at' => date('Y-m-d H:i:s')
            ]);


        }



        //print_r($doctor);

        if($doctor){

        	DB::table('user_doctors')
        	->where('user_id',$doctor->id)
        	->update([
                'degree' => $request->input('degree'),
                'bmdc_regi_no' => $request->input('bmdc_regi_no'),
                'speciality_id' => $request->input('speciality_id'),
                'relevant_degree' => $request->input('relevant_degree'),
                'sex' => $request->input('sex'),
                'age' => $request->input('age'),
                'institute_name' => $request->input('institute_name'),
                'chamber_address' => $request->input('chamber_address'),
                'reference' => $request->input('reference'),
                'note' => $request->input('note'),
                'posted_by' => Auth::user()->id
            ]);
        }
        $doctor->assignRole($request->input('roles'));
        
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        $doctor->assignRole($request->input('roles'));


        try {

           for ($i=1; $i <=4 ; $i++) { 

                $photo = $name = $public_url = '';     
                if($request->hasFile('photo'.$i))
                {
                    if($request->file('photo'.$i)->isValid())
                    {
                        
                        $name = str_ireplace('.'.$request->file('photo'.$i)->getClientOriginalExtension(), '', $request->file('photo'.$i)->getClientOriginalName());

                        $photo  = $name .'_'.date('Ymdhis').'.'.$request->file('photo'.$i)->getClientOriginalExtension();

                        $public_url = public_path();

                        // return response()->json([ 'data' =>$photo, 'success' => 1], 200);

                        if($request->file('photo'.$i)->move($public_url.'/uploads/images/doctors/', $photo))
                        {      
                            //$request['photo'] = '/uploads/images/doctors/'.$photo;   
                            $photo = '/uploads/images/doctors/'.$photo;     
                             // Set user profile image path in database to filePath
                            DB::table('user_files')->insertGetId([
                                'name' => $name,
                                'user_id' => $doctor->id,
                                'path' => $photo,
                                'posted_by' => Auth::user()->id,
                                'type_id' => 1,
                                'status' => 1,
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                        }  
                    }
                                
                }
           }
            
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => 'Image updated Failed.']); 
        }


        

        return redirect()->route('doctors.index')
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

        DB::table('user_doctors')->where('user_id','=',$id)->delete();
        DB::table('appointment_charges')->where('user_id','=',$id)->delete();
        DB::table('schedulings')->where('user_id','=',$id)->delete();
        DB::table('user_files')->where('user_id','=',$id)->delete();
 
        return redirect()->route('doctors.index')
            ->with('success','User deleted successfully');
    }

}