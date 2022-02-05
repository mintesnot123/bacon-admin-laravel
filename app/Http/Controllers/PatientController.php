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

use Session;

    

class PatientController extends Controller

{


    use UploadTrait;
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

         $this->middleware('permission:patient-list|patient-create|patient-edit|patient-delete', ['only' => ['index','store']]);

         $this->middleware('permission:patient-create', ['only' => ['create','store']]);

         $this->middleware('permission:patient-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:patient-delete', ['only' => ['destroy']]);

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

        $data = $data->whereIn('model_has_roles.role_id',[4])->orderBy('id','DESC')->paginate(20);

        return view('admin.patients.index',compact('data'))

            ->with('i', ($request->input('page', 1) - 1) * 20);

    }

    

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        $patient = User::find(Auth::user()->id);
        $userRole = $patient->roles->pluck('id','id')->toArray();

        $roles = Role::whereIn('id',[4])->pluck('name','name');
        //$roles = Role::pluck('name','name')->all();

        return view('admin.patients.create',compact('roles'));
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
        $patient = User::create($input);

        //print_r($patient);

        if($patient){
            DB::table('user_patients')->insertGetId([
                'user_id' => $patient->id,
                'posted_by' => Auth::user()->id,
                'device_id' => 2,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        $patient->assignRole($request->input('roles'));

        return redirect()->route('patients.index')

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

        $patient = User::find($id);

        $patient = User:: leftJoin('user_patients', 'user_patients.user_id', '=', 'users.id')
        ->select('users.*','user_patients.*','model_has_roles.*','roles.name as role_name')
        ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
        ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->where('users.id',$id)->whereIn('model_has_roles.role_id',[4])->first();

        $patient->id= $id;

        $files = DB::table('user_files')->where('user_id',$id)->where('type_id',2)->where('status',1)->orderBy('id', 'ASC')->get(); 

        // echo'<pre>';
        // print_r($patient);
        // echo'</pre>';

        return view('admin.patients.show',compact('patient','files'));
    }

    

    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        //$patient = User::find($id);

        $patient = User:: leftJoin('user_patients', 'user_patients.user_id', '=', 'users.id')
        ->select('users.*','user_patients.*','model_has_roles.*','roles.name as role_name')
        ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
        ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->where('users.id',$id)->whereIn('model_has_roles.role_id',[4])->first();

        $userRole = $patient->roles->pluck('name','name')->all();

        //$roles = Role::pluck('name','name')->all();
        $patient->id = $id;
        // echo "<pre>";
        // printf($patient);
        // echo "</pre>";

        $roles = Role::whereIn('id',[4])->pluck('name','name');

        $specialities =  array('' => 'Select Specialty') + DB::table('specialities')->where('status',1)->pluck('name','id')->toArray();

        $countries = array('' => 'Select Country') + DB::table('countries')->where('status',1)->pluck('name','id')->toArray();
        $divisions = array('' => 'Select Division') + DB::table('divisions')->where('status',1)->pluck('name','id')->toArray();
        $cities = array('' => 'Select City') + DB::table('cities')->where('status',1)->pluck('name','id')->toArray();
        $zones = array('' => 'Select Thana') + DB::table('zones')->where('status',1)->pluck('name','id')->toArray();

        return view('admin.patients.edit',compact('countries','divisions','cities','zones','patient','roles','userRole','specialities'));
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

            //'photo'     =>  'mimes:jpeg,png,jpg,gif,pdf|max:20048',

           // 'photo1'     =>  'mimes:jpeg,png,jpg,gif,pdf|max:20048',

            //'photo2'     =>  'mimes:jpeg,png,jpg,gif,pdf|max:20048',

            //'photo3'     =>  'mimes:jpeg,png,jpg,gif,pdf|max:20048'

        ]);

    
        $input = $request->all(); 

        if(!empty($input['password'])){ 

            $input['password'] = Hash::make($input['password']);

        }else{

            unset($input['password']);
            unset($input['confirm-password']);
            //$input = array_except($input,array('password'));    
        }
      
        $patient = User::find($id);

        $patient->update($input);

        //print_r($patient);

       $count = DB::table('user_patients')->where('user_id',$patient->id)->count();

        if($count){
            DB::table('user_patients')
            ->where('user_id',$patient->id)
            ->update([
                'country_id' => $request->input('country_id'),
                'division_id' => $request->input('division_id'),
                'city_id' => $request->input('city_id'),
                'zone_id' => $request->input('zone_id'),
                'sex' => $request->input('sex'),
                'age' => $request->input('age'),
                'address' => $request->input('address'),
                'note' => $request->input('note'),
                'posted_by' => Auth::user()->id
            ]);
        }else{

             DB::table('user_patients')->insertGetId([

                'country_id' => $request->input('country_id'),
                'division_id' => $request->input('division_id'),
                'city_id' => $request->input('city_id'),
                'zone_id' => $request->input('zone_id'),
                'sex' => $request->input('sex'),
                'age' => $request->input('age'),
                'address' => $request->input('address'),
                'note' => $request->input('note'),
                'user_id' => $patient->id,
                'posted_by' => Auth::user()->id,
                'device_id' => 2,
                'created_at' => date('Y-m-d H:i:s')
            ]);


        }
        $patient->assignRole($request->input('roles'));
        
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        $patient->assignRole($request->input('roles'));



        try {


            // Check if a profile image has been uploaded
            if ($request->has('photo')) {
                // Get image file
                $image = $request->file('photo');
                // Make a image name based on user name and current timestamp
                $name = Str::slug($request->input('name')).'_'.time();
                // Define folder path
                $folder = '/uploads/images/doctors/';
                // Make a file path where image will be stored [ folder path + file name + file extension]
                $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
                // Upload image
                if($this->uploadOne($image, $folder, 'public', $name)){

                    // Set user profile image path in database to filePath
                    DB::table('user_files')->insertGetId([
                        'name' => $name,
                        'user_id' => $patient->id,
                        'path' => $filePath,
                        'posted_by' => Auth::user()->id,
                        'type_id' => 2,
                        'status' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                }
            }


            // Check if a profile image has been uploaded
            if ($request->has('photo1')) {
                // Get image file
                $image = $request->file('photo1');
                // Make a image name based on user name and current timestamp
                $name = Str::slug($request->input('name')).'_'.time();
                // Define folder path
                $folder = '/uploads/images/doctors/';
                // Make a file path where image will be stored [ folder path + file name + file extension]
                $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
                // Upload image
                if($this->uploadTwo($image, $folder, 'public', $name)){

                    // Set user profile image path in database to filePath
                    DB::table('user_files')->insertGetId([
                        'name' => $name,
                        'user_id' => $patient->id,
                        'path' => $filePath,
                        'posted_by' => Auth::user()->id,
                        'type_id' => 2,
                        'status' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                }
                
            }


            // Check if a profile image has been uploaded
            if ($request->has('photo2')) {
                // Get image file
                $image = $request->file('photo2');
                // Make a image name based on user name and current timestamp
                $name = Str::slug($request->input('name')).'_'.time();
                // Define folder path
                $folder = '/uploads/images/doctors/';
                // Make a file path where image will be stored [ folder path + file name + file extension]
                $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
                // Upload image
                if($this->uploadThree($image, $folder, 'public', $name)){

                    // Set user profile image path in database to filePath
                    DB::table('user_files')->insertGetId([
                        'name' => $name,
                        'user_id' => $patient->id,
                        'path' => $filePath,
                        'posted_by' => Auth::user()->id,
                        'type_id' => 2,
                        'status' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                }
            }


            // Check if a profile image has been uploaded
            if ($request->has('photo3')) {
                // Get image file
                $image = $request->file('photo3');
                // Make a image name based on user name and current timestamp
                $name = Str::slug($request->input('name')).'_'.time();
                // Define folder path
                $folder = '/uploads/images/doctors/';
                // Make a file path where image will be stored [ folder path + file name + file extension]
                $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
                // Upload image
                if($this->uploadFour($image, $folder, 'public', $name)){

                    // Set user profile image path in database to filePath
                    DB::table('user_files')->insertGetId([
                        'name' => $name,
                        'user_id' => $patient->id,
                        'path' => $filePath,
                        'posted_by' => Auth::user()->id,
                        'type_id' => 2,
                        'status' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                }
            }
            
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => 'Image updated Failed.']); 
        }

        return redirect()->route('patients.index')

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

        DB::table('user_patients')->where('user_id','=',$id)->delete();
        DB::table('user_files')->where('user_id','=',$id)->delete();
 
        return redirect()->route('patients.index')
            ->with('success','User deleted successfully');
    }

}