<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

use Illuminate\Support\Facades\Validator;
use Log; 
use DB;
use STDclass;
use Session;
use Storage;
use Mail;
use PDF;

class AuthDoctorController extends Controller
{


    function getLogs($message, $type=7){

        switch ($type) {
            case 1:
               Log::error($message);
            break;
            case 2:
               Log::emergency($message);
            break;
            case 3:
               Log::critical($message);
            break;
            case 4:
               Log::warning($message);
            break;
            case 5:
               Log::notice($message);
            break;
            case 6:
               Log::info($message);
            break; 
            case 7:
               Log::debug($message);
            break; 

            default:
                Log::debug($message);
                break;
        }
    }


     public function getPhotoUrl($url)
    {
       $site_url = getenv('APP_URL');
       $is_url = stripos( $url, '://');

        if($url && !$is_url):
            $url =  str_replace('\\', '/', $url);
            //$url =  str_replace('public/', '', $url);
           return $site_url.trim($url); 
        endif;
        return $url; 
    }



    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6'
        ]);       

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);        

        $user->save();        

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }
  
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);        

        $credentials = request(['email', 'password']);    


        if(!Auth::attempt($credentials))
        return response()->json([
            'message' => 'Unauthorized'
        ], 401);       

        $user = $request->user();        

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;        

        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);   


        $token->save();        

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
    	]);
    }
  
    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();        

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
  
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }


    /**
     * Create user
     *
     * @param  [string] mobile_no
    
     * @return [string] message
     */
    public function signupDoctor(Request $request)
    {

        $mobile_no = trim(str_replace('+88', '', $request->mobile_no));
        
        $count = DB::table('users')->where('mobile_no','=',$mobile_no)->where('status',1)->count();

        // return response()->json(['error' =>$count, 'success' => 0], 401);   

        if($count && $mobile_no != NULL ){
             try {

                $data = DB::table('users')->where('mobile_no',$mobile_no)->where('status',1)->first();

                if( $data):
                    if (auth()->loginUsingId($data->id)) {
                        $token = auth()->user()->createToken('TrutsForWeb')->accessToken;
                        $this->getLogs($token);
                        return response()->json([ 'token_type' => 'Bearer','token' => $token, 'success' => 1], 200);
                    } else {
                        $this->getLogs('Unauthorized');
                        return response()->json(['error' => 'Unauthorized', 'success' => 0], 401);
                    }
                else:
                    $this->getLogs('Unauthorized');
                   return response()->json(['data' => 'Unauthorized', 'success' => 0], 401);   
                endif; 

            } catch (Exception $e) {
                $this->getLogs('Unauthorized');
                return response()->json(['error' =>'Unauthorized', 'success' => 0], 401);    
            } 

        }else{


            $request->email = substr($request->mobile_no.uniqid(), 4) .'@beaconpatientcare.com';

            $validators = array(  
                
                'mobile_no' => 'required|string|min:10|max:15|unique:users',    
            );

            if($request->has('email')){
                $push = array(            
                    'email' => 'required|string|email|unique:users',          
                );
                $validators = $validators+$push; 
            }

            
            $setAttributeNames = array(     
                'email' => 'E-mail Name',
                'mobile_no' => 'Contact Phone Number',      
            );

            $validator = Validator::make($request->all(), $validators);
            $validator->setAttributeNames($setAttributeNames); 

            if ($validator->fails())
            {
                return response(['message'=>$validator->errors()->all(),'success' => 0], 422);
            }

            $user = new User([
                'name' => $request->mobile_no,
                'email' => $request->email,
                'mobile_no' => $mobile_no,
                'password' => bcrypt($mobile_no),
                'status' => 1
            ]);        

            $user->save();    

            $user->assignRole(3);  //Doctor Role setup   

            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;        
            $token->save();  

            if($user){
                DB::table('user_doctors')->insertGetId([
                    'user_id' => $user->id,
                    'posted_by' => $user->id,
                    'device_id' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }      

            return response()->json([
                'token_type' => 'Bearer',
                'token' => $tokenResult->accessToken,
                // 'expires_at' => Carbon::parse(
                //     $tokenResult->token->expires_at
                // )->toDateTimeString(),
                'success' => 1
            ]);

            // return response()->json([
            //     'message' => 'Successfully created user!'
            // ], 201);

        }
        
    }



     /**
     * Handles Login Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginDoctor(Request $request)
    {

        try {

            $mobile_no = trim(str_replace('+88', '', $request->mobile_no));

            $data = DB::table('users')->where('mobile_no',$mobile_no)->where('status',1)->first();

            if( $data && $mobile_no != NULL ):
                if (auth()->loginUsingId($data->id)) {
                    $token = auth()->user()->createToken('TrutsForWeb')->accessToken;
                    $this->getLogs($token);
                    return response()->json([ 'token_type' => 'Bearer','token' => $token, 'success' => 1], 200);
                } else {
                    $this->getLogs('Unauthorized');
                    return response()->json(['error' => 'Unauthorized', 'success' => 0], 401);
                }
            else:
                $this->getLogs('Unauthorized');
               return response()->json(['data' => 'Unauthorized', 'success' => 0], 401);   
            endif; 

        } catch (Exception $e) {
            $this->getLogs('Unauthorized');
            return response()->json(['error' =>'Unauthorized', 'success' => 0], 401);    
        }         
    }


      /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setDoctorProfile(Request $request)
    {

       // return response()->json([ 'data' =>auth()->user()->id, 'success' => 1], 200);
        
        if(auth()->user()->id):

            $validators = array(); 
            $inputs = array();
            $user_data = array();

            if($request->get('name') !== null ){
                $push = array(            
                    'name' => 'required',             
                );
                $validators = $validators+$push; 
                $user_data['name'] = $request->get('name');
            }


            if($request->get('email') !== null ){
                $push = array(            
                    'email' => 'required|email|unique:users,email,'.auth()->user()->id,            
                );
                $validators = $validators+$push; 

                 $user_data['email'] = $request->get('email');
            }


            if($request->get('mobile_no') !== null ){
                $push = array(            
                    'mobile_no' => 'required|unique:users,mobile_no,'.auth()->user()->id,           
                );
                $validators = $validators+$push; 
                $user_data['mobile_no'] = $request->get('mobile_no');
            }

            if($request->get('age ') !== null ){
                $push = array(            
                    'age ' => 'required',             
                );
                $validators = $validators+$push; 

                 $inputs['age'] = $request->get('age');
            }

            if($request->get('sex') !== null ){
                $push = array(            
                    'sex' => 'required',             
                );
                $validators = $validators+$push; 
                $inputs['sex'] = $request->get('sex');
            }


            if($request->get('country_id') !== null ){
                $push = array(            
                    'country_id' => 'required',
                );
                $validators = $validators+$push;  

                 $inputs['country_id'] = $request->get('country_id');
            }

            if($request->get('division_id') !== null ){
                $push = array(            
                    'division_id' => 'required',
                   
                );
                $validators = $validators+$push; 

                $inputs['division_id'] = $request->get('division_id');
            }


           if($request->get('city_id') !== null ){
                $push = array(            
                    'city_id' => 'required',
                   
                );
                $validators = $validators+$push; 

                $inputs['city_id'] = $request->get('city_id');
            }


            if($request->get('zone_id') !== null ){
                $push = array(            
                    'zone_id' => 'required',
                   
                );
                $validators = $validators+$push; 

                $inputs['zone_id'] = $request->get('zone_id');
            }

           
            if($request->file('doctor_photo') !== null ){
                $push = array(            
                    'doctor_photo' => 'required',
                   
                );
                $validators = $validators+$push; 
            }



            if($request->get('note') !== null ){
                $push = array(            
                    'note' => 'required',
                   
                );
                $validators = $validators+$push; 

                $inputs['note'] = $request->get('note');
            }

            if($request->get('degree') !== null ){
                $push = array(            
                    'degree' => 'required',
                   
                );
                $validators = $validators+$push; 
            }

            if($request->get('bmdc_regi_no') !== null ){
                $push = array(            
                    'bmdc_regi_no' => 'required',
                   
                );
                $validators = $validators+$push; 

                 $inputs['bmdc_regi_no'] = $request->get('bmdc_regi_no');
            }

            if($request->get('speciality_id') !== null ){
                $push = array(            
                    'speciality_id' => 'required',
                   
                );
                $validators = $validators+$push; 

                $inputs['speciality_id'] = $request->get('speciality_id');
            }

            if($request->get('relevant_degree') !== null ){
                $push = array(            
                    'relevant_degree' => 'required',
                   
                );
                $validators = $validators+$push; 

                  $inputs['relevant_degree'] = $request->get('relevant_degree');
            }


            if($request->get('institute_name') !== null ){
                $push = array(            
                    'institute_name' => 'required',
                   
                );
                $validators = $validators+$push; 

                 $inputs['institute_name'] = $request->get('institute_name');
            }


            if($request->get('chamber_address') !== null ){
                $push = array(            
                    'chamber_address' => 'required',
                   
                );
                $validators = $validators+$push; 

                 $inputs['chamber_address'] = $request->get('chamber_address');
            }


            if($request->get('reference') !== null ){
                $push = array(            
                    'reference' => 'required',
                   
                );
                $validators = $validators+$push; 

                 $inputs['reference'] = $request->get('reference');
            }


            if($request->get('is_mobile_bank') !== null && $request->get('is_mobile_bank')!=0){
                $push = array(            
                    'is_mobile_bank' => 'required',
                    'mobile_bank_name' => 'required',
                    'mobile_bank_no' => 'required',
                   
                );
                $validators = $validators+$push; 

                 $inputs['is_mobile_bank'] = $request->get('is_mobile_bank');
                 $inputs['mobile_bank_name'] = $request->get('mobile_bank_name');
                 $inputs['mobile_bank_no'] = $request->get('mobile_bank_no');
            }


            if($request->get('is_bank_account') !== null && $request->get('is_bank_account')!=0){
                $push = array(            
                    'is_bank_account' => 'required',
                    'bank_name' => 'required',
                    'branch_name' => 'required',
                    'account_name' => 'required',
                    'account_no' => 'required',
                   
                );
                $validators = $validators+$push; 

                $inputs['is_bank_account'] = $request->get('is_bank_account');
                $inputs['bank_name'] = $request->get('bank_name');
                $inputs['branch_name'] = $request->get('branch_name');
                $inputs['account_name'] = $request->get('account_name');
                $inputs['account_no'] = $request->get('account_no');

            }


           
            $setAttributeNames = array(
                'name' => 'Doctor Name',
                'age' => 'Doctor Age(Yrs)',
                'sex' => 'Doctor Gender',
                'country_id' => 'Doctor Country Name',
                'division_id' => 'Doctor Division Name',   
                'city_id' => 'Doctor City Name',
                'zone_id' => 'Doctor Thana Name',
                'doctor_photo' => 'Doctor Valid Photo',
                'note' => 'Doctor Note'
                
            );

            $validator = Validator::make($request->all(), $validators);
            $validator->setAttributeNames($setAttributeNames); 

            if ($validator->fails())
            {
                return response(['errors'=>$validator->errors()->all()], 422);
            }

            $is_photo = 0;
            $doctor_photo = '';  
            $id = 0;
            $data = $user = '';


          
            if($request->hasFile('doctor_photo'))
            {
                if($request->file('doctor_photo')->isValid())
                {

                    if(Storage::has(User::find(auth()->user()->id)->photo))
                    {
                        
                        Storage::delete(User::find(auth()->user()->id)->photo);                
                    }
                    
                    $photo  = date('Ymdhis').'.'.$request->file('doctor_photo')->getClientOriginalExtension();

                    $public_url = str_replace('/api/', '/', public_path());

                    // return response()->json([ 'data' =>$photo, 'success' => 1], 200);

                    if($request->file('doctor_photo')->move($public_url.'/img/users/', $photo))
                    {      
                        $user_data['photo'] = '/img/users/'.$photo;   
                        $photo = '/img/users/'.$photo;     
                        $is_photo = 1;  
                    }  
                }
                            
            } 

            //$request['name'] = $request->input('firstname')." ".$request->input('lastname');
            //return response()->json([ 'data' =>$is_photo, 'success' => 1], 200);

            if($request->all()){
               $user =  DB::table('users')->where('id',auth()->user()->id)->update($user_data); 

               $data =  DB::table('user_doctors')->where('user_id',auth()->user()->id)->update($inputs);
           
               /* $data =  DB::table('user_doctors')->where('user_id',auth()->user()->id)->update([

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

                    'country_id' => $request->get('country_id')?$request->get('country_id'):1,
                    'division_id' => $request->get('division_id'),
                    'city_id' => $request->get('city_id'),
                    'zone_id' => $request->get('zone_id'),
                    

                    'is_mobile_bank' => $request->get('is_mobile_bank'),
                    'mobile_bank_name' => $request->get('mobile_bank_name'),
                    'mobile_bank_no' => $request->get('mobile_bank_no'),
                    'is_bank_account' => $request->get('is_bank_account'),
                    'bank_name' => $request->get('bank_name'),
                    'branch_name' => $request->get('branch_name'),
                    'account_name' => $request->get('account_name'),
                    'account_no' => $request->get('account_no')
                    
                ]); */

                


            }else {

                $this->getLogs((array)$request->all());
                return response()->json(['error' => $request->all(), 'success' => 0], 200);
            }

            // if($is_photo){   
            //    $data =  DB::table('users')->where('id',auth()->user()->id)->update([ 'photo' =>$photo]); 
            // }  

            if($data || $user){        

                $this->getLogs((array)$request->all());

                return response()->json([ 'data' =>'Updated successfully', 'success' => 1], 200);
            } else {

                $this->getLogs((array)$request->all());
                return response()->json(['error' => $request->all(), 'success' => 0], 200);
            }
        else:
            $this->getLogs('Unauthorized');
            return response()->json(['error' => 'Unauthorized', 'success' => 0], 401);
        endif;     
    }



     /**
     * Get the getPatientProfile per Page
     *
     * @return [json] User Completed Order object
     */

    public function getDoctorProfile(Request $request)
    {

        $doctor = User::find(auth()->user()->id);

        $doctor = User:: leftJoin('user_doctors', 'user_doctors.user_id', '=', 'users.id')

        ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
        ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->leftJoin('specialities', 'specialities.id', '=', 'user_doctors.speciality_id');

        $doctor = $doctor->leftJoin('countries', 'countries.id', '=', 'user_doctors.country_id');
        $doctor = $doctor->leftJoin('divisions', 'divisions.id', '=', 'user_doctors.division_id');
        $doctor = $doctor->leftJoin('cities', 'cities.id', '=', 'user_doctors.city_id');
        $doctor = $doctor->leftJoin('zones', 'zones.id', '=', 'user_doctors.zone_id');

        $doctor = $doctor->select(

        	'users.id as doctor_id',
        	'users.name as doctor_name',
            'users.email as doctor_email',
            'users.mobile_no as doctor_mobile_no',
            'users.photo as doctor_photo',
            'user_doctors.age as doctor_age',
            'user_doctors.sex as doctor_gender',

            'specialities.name as specialty_name',
        	'roles.name as role_name',
           
            'user_doctors.note as doctor_note',
            'countries.name as country_name',
            'divisions.name as division_name',
            'cities.name as city_name',
            'zones.name as thana_name',

            'user_doctors.degree as doctor_degree',
            'user_doctors.bmdc_regi_no as doctor_bmdc_regi_no',
            'user_doctors.relevant_degree as doctor_relevant_degree',
            'user_doctors.institute_name as doctor_institute_name',
            'user_doctors.chamber_address as doctor_chamber_address',
            'user_doctors.is_mobile_bank as doctor_is_mobile_bank',
            'user_doctors.mobile_bank_name as doctor_mobile_bank_name',
            'user_doctors.mobile_bank_no as doctor_mobile_bank_no',
            'user_doctors.is_bank_account as doctor_is_bank_account',
            'user_doctors.bank_name as doctor_bank_name',
            'user_doctors.branch_name as doctor_branch_name',
            'user_doctors.account_name as doctor_account_name',
            'user_doctors.account_no as doctor_account_no',
            'user_doctors.reference as doctor_reference',

            'users.status as account_status',
            'users.created_at  as account_created_date',
            'users.updated_at  as account_last_updated_date'
            //'user_doctors.*'  

        );


        $doctor = $doctor->where('users.id',auth()->user()->id)->whereIn('model_has_roles.role_id',[3])->first();

        //$files = DB::table('user_files')->where('user_id',auth()->user()->id)->where('type_id',1)->where('status',1)->orderBy('id', 'ASC')->get();  

        if($doctor->doctor_photo)
        $doctor->doctor_photo = $this->getPhotoUrl($doctor->doctor_photo);    

        if( $doctor):
           $this->getLogs( (array)$doctor);
           return response()->json(['data' =>  $doctor, 'success' => 1], 200);
        else:
            $this->getLogs('empty');
           return response()->json(['data' => 'empty', 'success' => 0], 200);   
        endif;

    }  


    






      /**
     * Get the getUserPaymentOrder per Page
     *
     * @return [json] User Specialty  object
     */
    public function getDoctorList(Request $request)
    {
        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}
        if($request->has('keyword')){ $keyword = $request->get('keyword');}else{ $keyword = '';}

        $start = 0;
        if($page)
        $start = $per_page*$page;
        

        $data = DB::table('users');
        $data = $data->leftJoin('user_doctors', 'user_doctors.user_id', '=', 'users.id');
        $data = $data->leftJoin('specialities', 'specialities.id', '=', 'user_doctors.speciality_id');
        $data = $data->leftJoin('appointment_charges', 'appointment_charges.user_id', '=', 'users.id');
        $data = $data->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id');
        

         $data = $data->select(
            'users.id as doctor_id',
            'users.name as doctor_name',   
            'users.photo as doctor_photo', 
            'user_doctors.*',
            'specialities.name as specialty_name', 
            'appointment_charges.amount as doctor_fees'
        );

        $data = $data->where('users.status',1);
        $data = $data->whereIn('model_has_roles.role_id',[3]);

       // $result = $result->orWhere( $field, 'like', '%' . $keyword . '%');


        if($keyword && $keyword!='fees asc' && $keyword!='fees desc'):
            $data = $data->where('users.name','like', '%' . $keyword . '%');
            $data = $data->orWhere('specialities.name','like', '%' . $keyword . '%');
            $data = $data->orWhere('user_doctors.degree','like', '%' . $keyword . '%');
            $data = $data->orWhere('user_doctors.relevant_degree','like', '%' . $keyword . '%');
            $data = $data->orWhere('user_doctors.sex','like', $keyword . '%');
            $data = $data->orWhere('countries.name','like', '%' . $keyword . '%');
            $data = $data->orWhere('divisions.name','like', '%' . $keyword . '%');
            $data = $data->orWhere('cities.name','like', '%' . $keyword . '%');
            $data = $data->orWhere('zones.name','like', '%' . $keyword . '%');
        endif;



        if($keyword=='fees asc')
             $data = $data->orderBy('doctor_fees', 'ASC'); 
        if($keyword=='fees desc')
             $data = $data->orderBy('doctor_fees', 'DESC');  

        $data = $data->orderBy('users.name', 'ASC'); 


        $count = $data->get()->count();  
        $data = $data->offset($start)->limit($per_page)->get();




        if( $data):
            foreach ($data as $key => $doctor) {

             $p_today_app = DB::table('patient_appointments')->where('doctor_user_id',$doctor->doctor_id)->where('appointment_date','=',date('Y-m-d'))->count();
             $doctor->availability=0;
             $d_app_limit = DB::table('schedulings')->where('user_id',$doctor->doctor_id)->count();
             if(($d_app_limit-$p_today_app)>0)
                $doctor->availability=1;  


             $doctor->doctor_photo = $this->getPhotoUrl($doctor->doctor_photo);    

             unset($doctor->created_at);unset($doctor->updated_at);unset($doctor->posted_by); unset($doctor->status);   
             unset($doctor->device_id);unset($doctor->reference);unset($doctor->user_id); 
             unset($doctor->id);unset($doctor->reference);unset($doctor->user_id); 
             
            }
            
           $this->getLogs( (array)$data);
           return response()->json(['data' =>  $data,'items' => $count,'current_page' =>  $page+1,'limit' => $per_page, 'offset' => $start, 'success' => 1], 200);
        else:
            $this->getLogs('empty');
           return response()->json(['data' => 'empty', 'success' => 0], 200);   
        endif;

    } 

   



      /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setPatientPrescriptions(Request $request)
    {

       // return response()->json([ 'data' =>auth()->user()->id, 'success' => 1], 200);
        
        if(auth()->user()->id):

           $validators = array(); 
           
            $push = array(            
                'appointment_id' => 'required',    
            );

            $validators = $validators+$push; 

            $push = array(            
                'patient_prescription_title' => 'required',
               
            );

            $validators = $validators+$push; 
     
            $push = array(            
                'patient_prescription_detail' => 'required',
               
            );

            $validators = $validators+$push; 


            if($request->get('patient_prescription') !== null ){
                $push = array(            
                    'patient_prescription' => 'required',
                   
                );
                
                $validators = $validators+$push; 
            }
            
            $setAttributeNames = array(
                'patient_prescription_detail' => 'Doctor Prescription Detail',
                'appointment_id' => 'Doctor Appointment Id'
                        
            );

            $validator = Validator::make($request->all(), $validators);
            $validator->setAttributeNames($setAttributeNames); 

            if ($validator->fails())
            {
                return response(['errors'=>$validator->errors()->all()], 422);
            }


            $appointment_count = DB::table('patient_appointments')->where('id',$request->get('appointment_id'))->where('doctor_user_id',Auth::user()->id)->count();

            $is_photo = 0;
            $patient_prescription = '';  
            $id = 0;
            $success = 0;
            $path = '';
          
            if($request->hasFile('patient_prescription'))
            {
                if($request->file('patient_prescription')->isValid())
                {
                    

                    $name = str_ireplace('.'.$request->file('patient_prescription')->getClientOriginalExtension(), '', $request->file('patient_prescription')->getClientOriginalName());

                    $patient_prescription  = $name .'_'.date('Ymdhis').'.'.$request->file('patient_prescription')->getClientOriginalExtension();

                    $public_url = str_replace('/api/', '/', public_path());


                    if($request->file('patient_prescription')->move($public_url.'/uploads/images/doctors/', $patient_prescription))
                    {      
                      
                        $path = '/uploads/images/doctors/'.$patient_prescription;     
                        $is_photo = 1;  
                    }  

                  
                }
                            
            } 


            if($appointment_count){        
                 //Set user profile image path in database to path
               $success = DB::table('user_files')->insertGetId([
                    'name' => $request->get('patient_prescription_title'),
                    'detail' => $request->get('patient_prescription_detail'),
                    'appointment_id' => $request->get('appointment_id'),
                    'user_id' => auth()->user()->id,
                    'path' => $path,
                    'posted_by' => Auth::user()->id,
                    'type_id' => 2,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

               if($success){        
                    $this->getLogs((array)$request->all());
                    return response()->json([ 'data' =>'Updated successfully', 'success' => 1], 200);
                } else {

                    $this->getLogs((array)$request->all());
                    return response()->json(['error' => $request->all(), 'success' => 0], 200);
                }
            } else {

                $this->getLogs((array)$request->all());
                return response()->json(['error' => $request->all(), 'success' => 0], 200);
            }
            
            
        else:
            $this->getLogs('Unauthorized');
            return response()->json(['error' => 'Unauthorized', 'success' => 0], 401);
        endif;     
    }


      /**
     * Get the getUserPaymentOrder per Page
     *
     * @return [json] User Specialty  object
     */
    public function getPaymentList(Request $request)
    {
        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}
        

        $start = 0;
        if($page)
        $start = $per_page*$page;
        

        $data = DB::table('payments');
        $data = $data->leftJoin('patient_appointments', 'patient_appointments.id', '=', 'payments.patient_appointment_id');
        //$data = $data->leftJoin('appointment_charges', 'appointment_charges.user_id', '=', 'patient_appointments.doctor_user_id');

        // $data = $data->select(
        //     'specialities.*'  
        // );

        $data = $data->select(
            'payments.*',
            'patient_appointments.name as appoint_patient_name'
           // 'appointment_charges.amount as specialty_name'   
        );

        $data = $data->where('patient_appointments.doctor_user_id',Auth::user()->id);
        $data = $data->where('payments.status',1);
     
        $data = $data->orderBy('payments.created_at', 'DESC'); 
        $count = $data->get()->count();  
        $data = $data->offset($start)->limit($per_page)->get();


        if( $data):
            foreach ($data as $key => $orders) {

            if($orders->payment_type==1){
                $orders->payment_type='Pending';
            }elseif($orders->payment_type==2){
                 $orders->payment_type='Complete';
            }if($orders->payment_type==1){
                $orders->payment_type='Cancelled';
            }   

            $orders->payment_date= $today = date("F j, Y", strtotime($orders->created_at)); 

            unset($orders->status); unset($orders->created_at); unset($orders->updated_at);unset($orders->posted_by); unset($orders->logs);  unset($orders->payeer_id);  
             
             }
            
           $this->getLogs( (array)$data);
           return response()->json(['data' =>  $data,'items' => $count,'current_page' =>  $page+1,'limit' => $per_page, 'offset' => $start, 'success' => 1], 200);
        else:
            $this->getLogs('empty');
           return response()->json(['data' => 'empty', 'success' => 0], 200);   
        endif;

    }  


      /**
     * Get the getUserPaymentOrder per Page
     *
     * @return [json] User Specialty  object
     */
    public function getAppointmentList(Request $request)
    {
        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}
        

        $start = 0;
        if($page)
        $start = $per_page*$page;

        //return response()->json(['data' => Auth::user()->id, 'success' => 0], 200);   


        $data = DB::table('patient_appointments');

        $data = $data->leftJoin('user_doctors', 'user_doctors.user_id', '=', 'patient_appointments.doctor_user_id')
        ->leftJoin('user_patients', 'user_patients.user_id', '=', 'patient_appointments.patient_user_id')
        ->leftJoin('specialities', 'specialities.id', '=', 'user_doctors.speciality_id')
        ->leftJoin('schedulings', 'schedulings.id', '=', 'patient_appointments.scheduling_id')
        ->leftJoin('countries', 'countries.id', '=', 'user_patients.country_id')
        ->leftJoin('divisions', 'divisions.id', '=', 'user_patients.division_id')
        ->leftJoin('cities', 'cities.id', '=', 'user_patients.city_id')
        ->leftJoin('zones', 'zones.id', '=', 'user_patients.zone_id');

        $data = $data->select(
            'patient_appointments.id as patient_appointment_id',
            'patient_appointments.*',
            'user_doctors.*',
            'user_doctors.sex as doctor_gender',
            'user_doctors.age as doctor_age',
            'user_doctors.note as doctor_note',
            'specialities.name as doctor_specialty',
            'user_patients.*',
            'user_patients.sex as patient_gender',
            'user_patients.age as patient_age',
            'user_patients.note as patient_note',
            'schedulings.*',
            'countries.name as patient_country_name',
            'divisions.name as patient_division_name',
            'cities.name as patient_city_name',
            'zones.name as patient_zone_name'    
        );

        $data = $data->where('patient_appointments.doctor_user_id',Auth::user()->id);

        $data = $data->where('patient_appointments.status',1);
     
        $data = $data->orderBy('patient_appointments.created_at', 'DESC'); 
        $count = $data->get()->count();  
        $data = $data->offset($start)->limit($per_page)->get();


        if( $data):
            foreach ($data as $key => $orders) {

           //$orders->payment_date= $today = date("F j, Y", strtotime($orders->created_at)); 

            unset($orders->id); 
             }
            
           $this->getLogs( (array)$data);
           return response()->json(['data' =>  $data,'items' => $count,'current_page' =>  $page+1,'limit' => $per_page, 'offset' => $start, 'success' => 1], 200);
        else:
            $this->getLogs('empty');
           return response()->json(['data' => 'empty', 'success' => 0], 200);   
        endif;

    }  


     /**
     * Get the getPatientProfile per Page
     *
     * @return [json] User Completed Order object
     */

    public function getPatientList(Request $request)
    {


        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}
        if($request->has('keyword')){ $keyword = $request->get('keyword');}else{ $keyword = '';}

        $start = 0;
        if($page)
        $start = $per_page*$page;
        

        $data = DB::table('users');

        $data = $data->leftJoin('patient_appointments', 'patient_appointments.patient_user_id', '=', 'users.id');

        $data = $data->leftJoin('payments', 'payments.patient_appointment_id', '=', 'patient_appointments.id');

        $data = $data->leftJoin('user_patients', 'user_patients.user_id', '=', 'users.id');
        $data = $data->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id');
        $data = $data->leftJoin('countries', 'countries.id', '=', 'user_patients.country_id');
        $data = $data->leftJoin('divisions', 'divisions.id', '=', 'user_patients.division_id');
        $data = $data->leftJoin('cities', 'cities.id', '=', 'user_patients.city_id');
        $data = $data->leftJoin('zones', 'zones.id', '=', 'user_patients.zone_id');

         $data = $data->select(     
            'users.id as patient_id',
            'users.name as patient_name',
            'users.email as patient_email',
            'users.mobile_no as patient_mobile_no',
            'users.photo as patient_photo',
            'user_patients.age as account_age',
            'user_patients.sex as account_gender',
            'user_patients.address as account_address',
            'user_patients.note as account_note',
            'countries.name as country_name',
            'divisions.name as division_name',
            'cities.name as city_name',
            'zones.name as thana_name',
            'users.status as account_status',
            'users.created_at as account_created_date',
            'users.updated_at as account_last_updated_date'     
        );

        $data = $data->where('patient_appointments.doctor_user_id', auth()->user()->id);

        $data = $data->where('payments.payment_type', 2);

        $data = $data->where('users.status', 1);

        $data = $data->whereIn('model_has_roles.role_id',[4]);


       // $result = $result->orWhere( $field, 'like', '%' . $keyword . '%');

         if($keyword):
           $data = $data->where(function($q) use ($keyword){

                $q->orWhere('users.name','=',$keyword)
                ->orWhere('user_patients.address','=',$keyword)
                ->orWhere('user_patients.age','=',$keyword)
                ->orWhere('user_patients.sex','=',$keyword)
                ->orWhere('countries.name','=',$keyword)
                ->orWhere('divisions.name','=',$keyword)
                ->orWhere('cities.name','=',$keyword)
                ->orWhere('zones.name','=',$keyword);
            });

        endif;

        $data = $data->orderBy('users.id', 'DESC'); 


        $count = $data->distinct()->get()->count();  
        $data = $data->offset($start)->limit($per_page)->distinct()->get();


        if( $data):
            foreach ($data as $key => $patient) {

             $patient->patient_photo = $this->getPhotoUrl($patient->patient_photo);    

             unset($patient->created_at);unset($patient->updated_at);unset($patient->posted_by); unset($patient->status);   
             unset($patient->device_id);unset($patient->reference);unset($patient->user_id); 
             unset($patient->id);unset($patient->user_id); 
             
            }
            
           $this->getLogs( (array)$data);
           return response()->json(['data' =>  $data,'items' => $count,'current_page' =>  $page+1,'limit' => $per_page, 'offset' => $start, 'success' => 1], 200);
        else:
            $this->getLogs('empty');
           return response()->json(['data' => 'empty', 'success' => 0], 200);   
        endif;

    } 


    /**
     * Get the getPatientProfile per Page
     *
     * @return [json] User Completed Order object
     */

    public function getMedicineList(Request $request)
    {

        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}
        if($request->has('keyword')){ $keyword = $request->get('keyword');}else{ $keyword = '';}

        $start = 0;
        if($page)
        $start = $per_page*$page;

        $data = DB::table('medicines');
        $data = $data->where('medicines.status', 1);

        if($keyword):
           $data = $data->where(function($q) use ($keyword){
                $q->orWhere('medicines.name','=',$keyword)
                ->orWhere('medicines.detail','=',$keyword);    
            });
        endif;

        $data = $data->orderBy('medicines.id', 'DESC'); 

        $count = $data->get()->count();  
        $data = $data->offset($start)->limit($per_page)->get();


        if( $data):
            foreach ($data as $key => $medicine) {

             unset($medicine->icon);   
             unset($medicine->created_at);
             unset($medicine->updated_at);
             unset($medicine->posted_by); 
             unset($medicine->status);  
             
            }
            
           $this->getLogs( (array)$data);
           return response()->json(['data' =>  $data,'items' => $count,'current_page' =>  $page+1,'limit' => $per_page, 'offset' => $start, 'success' => 1], 200);
        else:
            $this->getLogs('empty');
           return response()->json(['data' => 'empty', 'success' => 0], 200);   
        endif;

    } 



     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setPatientPrescription(Request $request)
    {

       // return response()->json([ 'data' =>auth()->user()->id, 'success' => 1], 200);
        
        if(auth()->user()->id):

           $validators = array(); 
           
            $push = array(            
                'patient_appointment_id' => 'required',
               
            );
            $validators = $validators+$push; 
     
            $push = array(            
                'detail' => 'required',
               
            );
            $validators = $validators+$push; 

            $setAttributeNames = array(
                'patient_appointment_id' => 'Patient Appointment Id',
                'detail' => 'Patient Prescription Detail'
            );

            $validator = Validator::make($request->all(), $validators);
            $validator->setAttributeNames($setAttributeNames); 

            if ($validator->fails())
            {
                return response(['errors'=>$validator->errors()->all()], 422);
            }

             $count = DB::table('patient_appointments')->where('id',$request->get('patient_appointment_id'))->where('doctor_user_id',Auth::user()->id)->count();

            if($count){
                $success = DB::table('prescriptions')->insertGetId([
                    'appointment_id' => $request->get('patient_appointment_id'),
                    'detail' => $request->get('detail'),
                    'posted_by' => Auth::user()->id,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }else{
                $success = 0;
                $this->getLogs((array)$request->all());
                return response()->json(['error' => $request->all(), 'success' => 0], 200);
            }

            if($success){        
                $this->getLogs((array)$request->all());
                return response()->json([ 'data' =>'Updated successfully', 'success' => 1], 200);
            } else {

                $this->getLogs((array)$request->all());
                return response()->json(['error' => $request->all(), 'success' => 0], 200);
            }
        else:
            $this->getLogs('Unauthorized');
            return response()->json(['error' => 'Unauthorized', 'success' => 0], 401);
        endif;     
    }


     /**
     * Get the getPatientProfile per Page
     *
     * @return [json] User Completed Order object
     */

    public function getPatientPrescriptionList(Request $request)
    {


        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}
        if($request->has('keyword')){ $keyword = $request->get('keyword');}else{ $keyword = '';}

        $start = 0;
        if($page)
        $start = $per_page*$page;
        

        $data = DB::table('prescriptions');
        $data = $data->leftJoin('patient_appointments', 'patient_appointments.id', '=', 'prescriptions.appointment_id');
        $data = $data->leftJoin('users as patient', 'patient.id', '=', 'patient_appointments.patient_user_id');
        $data = $data->leftJoin('users as doctor', 'doctor.id', '=', 'patient_appointments.doctor_user_id');
        $data = $data->leftJoin('user_doctors', 'user_doctors.user_id', '=', 'patient_appointments.doctor_user_id');
   

         $data = $data->select(     
            'prescriptions.id as prescription_id',
            'prescriptions.*',
            'patient_appointments.*',
            //'patient.name as patient_name',
            //'patient.email as patient_email',
            //'patient.mobile_no as patient_mobile_no',
            'doctor.photo as doctor_photo',
            'doctor.name as doctor_name',
            'doctor.email as doctor_email',
            'doctor.mobile_no as doctor_mobile_no',  
            'user_doctors.signature as doctor_signature'
        );

        $data = $data->where('patient_appointments.doctor_user_id', auth()->user()->id); 

         if($keyword):
           $data = $data->where(function($q) use ($keyword){

                $q->orWhere('prescriptions.appointment_id','=',$keyword)
                ->orWhere('prescriptions.detail','=',$keyword)
                ->orWhere('patient_appointments.name','=',$keyword)
                 ->orWhere('patient_appointments.sex','=',$keyword)
                ->orWhere('patient_appointments.age','=',$keyword);      
            });

        endif;

        $data = $data->orderBy('prescriptions.id', 'DESC'); 

        $count = $data->distinct()->get()->count();  
        $data = $data->offset($start)->limit($per_page)->distinct()->get();


        if( $data):
            foreach ($data as $key => $patient) {

              $patient->doctor_signature = $this->getPhotoUrl($patient->doctor_signature);    
              $patient->doctor_photo = $this->getPhotoUrl($patient->doctor_photo);    
              $patient->doctor_pdf_prescription = $this->getPhotoUrl('/api/auth/get_pdf_prescription_list?appointment_id='.$patient->appointment_id);  

              //unset($patient->created_at);unset($patient->updated_at);
              unset($patient->posted_by); unset($patient->status);   
              unset($patient->id);unset($patient->doctor_user_id);
              unset($patient->patient_user_id); unset($patient->scheduling_id); 
            }
            
           $this->getLogs( (array)$data);
           return response()->json(['data' =>  $data,'items' => $count,'current_page' =>  $page+1,'limit' => $per_page, 'offset' => $start, 'success' => 1], 200);
        else:
            $this->getLogs('empty');
           return response()->json(['data' => 'empty', 'success' => 0], 200);   
        endif;

    } 



     /**
     * Get the getPatientProfile per Page
     *
     * @return [json] User Completed Order object
     */

    public function getPDFPatientPrescriptionList(Request $request)
    {


        $validators = array(); 
           
            $push = array(            
                'appointment_id' => 'required',
               
            );
            $validators = $validators+$push; 
     

            $setAttributeNames = array(
                'appointment_id' => 'Patient Appointment Id'
            );

            $validator = Validator::make($request->all(), $validators);
            $validator->setAttributeNames($setAttributeNames); 

            if ($validator->fails())
            {
                return response(['errors'=>$validator->errors()->all()], 422);
            }



        if($request->has('appointment_id')){ $keyword = $request->get('appointment_id');}else{ $keyword = '';}

  

        $data = DB::table('prescriptions');
        $data = $data->leftJoin('patient_appointments', 'patient_appointments.id', '=', 'prescriptions.appointment_id');
        $data = $data->leftJoin('users as patient', 'patient.id', '=', 'patient_appointments.patient_user_id');
        $data = $data->leftJoin('users as doctor', 'doctor.id', '=', 'patient_appointments.doctor_user_id');
        $data = $data->leftJoin('user_doctors', 'user_doctors.user_id', '=', 'patient_appointments.doctor_user_id');
   

         $data = $data->select(     
            'prescriptions.id as prescription_id',
            'prescriptions.*',
            'patient_appointments.*',
            //'patient.name as patient_name',
            //'patient.email as patient_email',
            //'patient.mobile_no as patient_mobile_no',
            'doctor.name as doctor_name',
            'doctor.email as doctor_email',
            'doctor.mobile_no as doctor_mobile_no',  
            'user_doctors.signature as doctor_signature'
        );

       // $data = $data->where('patient_appointments.doctor_user_id', auth()->user()->id); 

         if($keyword):
           $data = $data->where(function($q) use ($keyword){

                $q->orWhere('prescriptions.appointment_id','=',$keyword)
                ->orWhere('prescriptions.detail','=',$keyword)
                ->orWhere('patient_appointments.name','=',$keyword)
                 ->orWhere('patient_appointments.sex','=',$keyword)
                ->orWhere('patient_appointments.age','=',$keyword);      
            });

        endif;

        $data = $data->orderBy('prescriptions.id', 'DESC'); 

      
        $data = $data->distinct()->get();


        if( $data):
            foreach ($data as $key => $patient) {

             $patient->doctor_signature = $this->getPhotoUrl($patient->doctor_signature);    

              unset($patient->created_at);unset($patient->updated_at);unset($patient->posted_by); unset($patient->status);   

              unset($patient->id);unset($patient->doctor_user_id);unset($patient->patient_user_id); unset($patient->scheduling_id); 
            
             
            }
            
           $this->getLogs( (array)$data);
           //return response()->json(['data' =>  $data, 'success' => 1], 200);

            //return view('admin.appointments.index',compact('appointments'))

  
            $pdf = PDF::loadView('admin/appointments/show_pdf',compact('data'));

            // download PDF file with download method
            return $pdf->download('pdf_file.pdf');


        else:
            $this->getLogs('empty');
           return response()->json(['data' => 'empty', 'success' => 0], 200);   
        endif;

    } 





 
    


    
  
}