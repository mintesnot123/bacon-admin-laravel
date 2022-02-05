<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\RtcTokenBuilder;
use App\RtmTokenBuilder;
use Illuminate\Support\Facades\Validator;
use Log; 
use DB;
use STDclass;
use Session;
use Storage;
use Mail;

class AuthController extends Controller
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
    public function getChannelToken(Request $request)
    {


        if( $request->channel_name != NULL ):

        $appID = getenv('AGORA_APP_ID'); //"6affa947cb204c5ea365f9bff3609121";
        $appCertificate = getenv('AGORA_APP_CERTIFICATE'); //"8a225e9cec2e4194ac08e30d635db1f3";
        $channelName = $request->get('channel_name'); //"7d72365e6573453485397e3e3f9d460bdda";
        $uid = 0;//2882341273;
        $uidStr = "2882341273";
        $role = RtcTokenBuilder::RoleAttendee;
        $expireTimeInSeconds = 3600;

        $currentTimestamp = Carbon::now()->timestamp;

        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        $token = RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);
       // echo 'Token with int uid: ' . $token . PHP_EOL;

        if($token){
           return response()->json([ 'token' => $token, 'success' => 1], 200); 
       }else{
            return response()->json(['error' => 'Unauthorized', 'success' => 0], 401);

       }

       else:
            return response()->json(['error' => 'Unauthorized', 'success' => 0], 401);

       endif;
        
        //return response()->json($token);
    }



    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function getMessageToken(Request $request)
    {

        // return response()->json([ 'token' => $request->user(), 'success' => 1], 200); 

        $appID = getenv('AGORA_APP_ID'); 

        $appCertificate = getenv('AGORA_APP_CERTIFICATE'); 

        $role = RtmTokenBuilder::RoleRtmUser;

        $expireTimeInSeconds = 3600;

        $user = $request->user()->mobile_no?$request->user()->mobile_no:$request->user()->id;

        $currentTimestamp = Carbon::now()->timestamp;

        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        $token = RtmTokenBuilder::buildToken($appID, $appCertificate, $user, $role, $privilegeExpiredTs);

        if($token){
           return response()->json([ 'token' => $token, 'success' => 1], 200); 
       }else{
            return response()->json(['error' => 'Unauthorized', 'success' => 0], 401);

       }

     
        
        //return response()->json($token);
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
    public function signupPatient(Request $request)
    {
         $mobile_no = trim(str_replace('+88', '', $request->mobile_no));
        $count = DB::table('users')->where('mobile_no',$mobile_no)->where('status',1)->count();

        if($count && $request->mobile_no != NULL ){
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


            $request->email = substr($mobile_no.uniqid(), 4) .'@beaconpatientcare.com';

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
                'name' => $mobile_no,
                'email' => $request->email,
                'mobile_no' => $mobile_no,
                'password' => bcrypt($mobile_no),
                'status' => 1
            ]);        

            $user->save();    

            $user->assignRole(4);    

            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;        
            $token->save();  

            if($user){
                DB::table('user_patients')->insertGetId([
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
    public function loginPatient(Request $request)
    {

        try {
            $mobile_no = trim(str_replace('+88', '', $request->mobile_no));
            $data = DB::table('users')->where('mobile_no',$request->mobile_no)->where('status',1)->first();

            if( $data && $request->mobile_no != NULL ):
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
    public function setPatientProfile(Request $request)
    {

       // return response()->json([ 'data' =>auth()->user()->id, 'success' => 1], 200);
        
        if(auth()->user()->id):

           $validators = array(); 

            if($request->get('name') !== null ){
                $push = array(            
                    'name' => 'required',             
                );
                $validators = $validators+$push; 
            }

            if($request->get('age ') !== null ){
                $push = array(            
                    'age ' => 'required',             
                );
                $validators = $validators+$push; 
            }

            if($request->get('sex') !== null ){
                $push = array(            
                    'sex' => 'required',             
                );
                $validators = $validators+$push; 
            }


            if($request->get('country_id') !== null ){
                $push = array(            
                    'country_id' => 'required',
                );
                $validators = $validators+$push;  
            }

            if($request->get('division_id') !== null ){
                $push = array(            
                    'division_id' => 'required',
                   
                );
                $validators = $validators+$push; 
            }


           if($request->get('city_id') !== null ){
                $push = array(            
                    'city_id' => 'required',
                   
                );
                $validators = $validators+$push; 
            }


            if($request->get('zone_id') !== null ){
                $push = array(            
                    'zone_id' => 'required',
                   
                );
                $validators = $validators+$push; 
            }

           
            if($request->file('patient_photo') !== null ){
                $push = array(            
                    'patient_photo' => 'required',
                   
                );
                $validators = $validators+$push; 
            }

            
            if($request->get('address') !== null ){
                $push = array(            
                    'address' => 'required',
                   
                );
                $validators = $validators+$push; 
            }


            if($request->get('note') !== null ){
                $push = array(            
                    'note' => 'required',
                   
                );
                $validators = $validators+$push; 
            }
           
            $setAttributeNames = array(
                'name' => 'Patient Name',
                'age' => 'Patient Age(Yrs)',
                'sex' => 'Patient Gender',
                'country_id' => 'Patient Country Name',
                'division_id' => 'Patient Division Name',   
                'city_id' => 'Patient City Name',
                'zone_id' => 'Patient Thana Name',
                'patient_photo' => 'Patient Valid Photo',
                'address' => 'Patient Present Address',
                'note' => 'Patient Note'
                
            );

            $validator = Validator::make($request->all(), $validators);
            $validator->setAttributeNames($setAttributeNames); 

            if ($validator->fails())
            {
                return response(['errors'=>$validator->errors()->all()], 422);
            }

            $is_photo = 0;
            $patient_photo = '';  
            $id = 0;
          
            if($request->hasFile('patient_photo'))
            {
                if($request->file('patient_photo')->isValid())
                {

                    if(Storage::has(User::find(auth()->user()->id)->photo))
                    {
                        
                        Storage::delete(User::find(auth()->user()->id)->photo);                
                    }
                    
                    $photo  = date('Ymdhis').'.'.$request->file('patient_photo')->getClientOriginalExtension();

                    $public_url = str_replace('/api/', '/', public_path());

                    // return response()->json([ 'data' =>$photo, 'success' => 1], 200);

                    if($request->file('patient_photo')->move($public_url.'/img/users/', $photo))
                    {      
                        $request['photo'] = '/img/users/'.$photo;   
                        $photo = '/img/users/'.$photo;     
                        $is_photo = 1;  
                    }  
                }
                            
            } 

            //$request['name'] = $request->input('firstname')." ".$request->input('lastname');
            //return response()->json([ 'data' =>$is_photo, 'success' => 1], 200);

            if($request->all()){
               $user =  DB::table('users')->where('id',auth()->user()->id)->update([ 'name' =>$request->get('name')]); 
            } 


            if($request->all()){
               $data =  DB::table('user_patients')->where('user_id',auth()->user()->id)->update([
                    'age' => $request->get('age'),
                    'sex' => $request->get('sex'),
                    'country_id' => $request->get('country_id')?$request->get('country_id'):1,
                    'division_id' => $request->get('division_id'),
                    'city_id' => $request->get('city_id'),
                    'zone_id' => $request->get('zone_id'),
                    'address' => $request->get('address'),
                    'note' => $request->get('note')
                ]); 
            }

            if($is_photo){   
               $data =  DB::table('users')->where('id',auth()->user()->id)->update([ 'photo' =>$photo]); 
            }  

            

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

    public function getPatientProfile(Request $request)
    {

        $data = DB::table('users');
        $data = $data->leftJoin('user_patients', 'user_patients.user_id', '=', 'users.id');
        $data = $data->leftJoin('countries', 'countries.id', '=', 'user_patients.country_id');
        $data = $data->leftJoin('divisions', 'divisions.id', '=', 'user_patients.division_id');
        $data = $data->leftJoin('cities', 'cities.id', '=', 'user_patients.city_id');
        $data = $data->leftJoin('zones', 'zones.id', '=', 'user_patients.zone_id');

        $data = $data->select(     
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
            'users.created_at  as account_created_date',
            'users.updated_at  as account_last_updated_date'     
        );

        $data = $data->where('users.id', auth()->user()->id);
        $data = $data->where('users.status', 1);
        $data = $data->first();

        $data->patient_photo = $this->getPhotoUrl($data->patient_photo);    

        if( $data):
           $this->getLogs( (array)$data);
           return response()->json(['data' =>  $data, 'success' => 1], 200);
        else:
            $this->getLogs('empty');
           return response()->json(['data' => 'empty', 'success' => 0], 200);   
        endif;

    }  


      /**
     * Get the getUserPaymentOrder per Page
     *
     * @return [json] User Payment Order object
     */
    public function getCountryList(Request $request)
    {
        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}

        $start = 0;
        if($page)
        $start = $per_page*$page;
       

        $data = DB::table('countries');
              
        $data = $data->select(
            'countries.id as country_id',
            'countries.name as country_name',
            'countries.code as country_code'    
        );

        $data = $data->where('status',1);
        $data = $data->orderBy('countries.name', 'ASC'); 
        $count = $data->get()->count();  
        $data = $data->offset($start)->limit($per_page)->get();


        if( $data):
            // foreach ($data as $key => $orders) {
            //     unset($orders->created_at);
            //     unset($orders->updated_at); 
            //     unset($orders->posted_by);
            //     unset($orders->prefix); 
            //     unset($orders->status); 
            // }
            
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
     * @return [json] User Payment Order object
     */
    public function getDivisionList(Request $request)
    {
        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}
        if($request->has('country_id')){ $country_id = $request->get('country_id');}else{ $country_id = 1;}

       
        $start = 0;
        if($page)
        $start = $per_page*$page;
        

        $data = DB::table('divisions');

        // $data = $data->select(
        //     'divisions.*'  
        // );

        $data = $data->select(
            'divisions.id as division_id',
            'divisions.name as division_name'
            //'divisions.code as country_code'    
        );

        $data = $data->where('status',1);
        if($country_id)
        $data = $data->where('country_id', $country_id);

        $data = $data->orderBy('name', 'ASC'); 
        $count = $data->get()->count();  
        $data = $data->offset($start)->limit($per_page)->get();


        if( $data):
            // foreach ($data as $key => $orders) {
            // unset($orders->created_at);unset($orders->updated_at);unset($orders->posted_by); unset($orders->status);     
            // }
            
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
     * @return [json] User Payment Order object
     */
    public function getCityList(Request $request)
    {
        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}
        if($request->has('division_id')){ $division_id = $request->get('division_id');}else{ $division_id = 0;}

       
        $start = 0;
        if($page)
        $start = $per_page*$page;
        

        $data = DB::table('cities');

        // $data = $data->select(
        //     'cities.*'  
        // );

        $data = $data->select(
            'cities.id as city_id',
            'cities.name as city_name'
            //'countries.code as country_code'    
        );

        $data = $data->where('status',1);

        if($division_id)
        $data = $data->where('division_id', $division_id);

        $data = $data->orderBy('name', 'ASC'); 
        $count = $data->get()->count();  
        $data = $data->offset($start)->limit($per_page)->get();


        if( $data):
            // foreach ($data as $key => $orders) {

            // unset($orders->created_at);unset($orders->updated_at);unset($orders->posted_by); unset($orders->status);   
             
            // }
            
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
     * @return [json] User Payment Order object
     */
    public function getZoneList(Request $request)
    {
        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}
        if($request->has('city_id')){ $city_id = $request->get('city_id');}else{ $city_id = 0;}

        $start = 0;
        if($page)
        $start = $per_page*$page;
        

        $data = DB::table('zones');

        // $data = $data->select(
        //     'zones.*'  
        // );

         $data = $data->select(
            'zones.id as zone_id',
            'zones.name as zone_name'
            //'countries.code as country_code'    
        );

        $data = $data->where('status',1);

        if($city_id)
        $data = $data->where('city_id', $city_id);

        $data = $data->orderBy('name', 'ASC'); 
        $count = $data->get()->count();  
        $data = $data->offset($start)->limit($per_page)->get();


        if( $data):
            // foreach ($data as $key => $orders) {

            // unset($orders->created_at);unset($orders->updated_at);unset($orders->posted_by); unset($orders->status);   
             
            // }
            
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
    public function getSpecialtyList(Request $request)
    {
        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}
        if($request->has('specialty_name')){ $specialty_name = $request->get('specialty_name');}else{ $specialty_name = 0;}

        $start = 0;
        if($page)
        $start = $per_page*$page;
        

        $data = DB::table('specialities');

        // $data = $data->select(
        //     'specialities.*'  
        // );

         $data = $data->select(
            'specialities.id as specialty_id',
            'specialities.icon as specialty_icon',
            'specialities.name as specialty_name'   
        );

        $data = $data->where('status',1);

        if($specialty_name)
        $data = $data->where('name','like',$specialty_name);

        $data = $data->orderBy('name', 'ASC'); 
        $count = $data->get()->count();  
        $data = $data->offset($start)->limit($per_page)->get();


        if( $data):
            // foreach ($data as $key => $orders) {

            // unset($orders->created_at);unset($orders->updated_at);unset($orders->posted_by); unset($orders->status);   
             
            // }
            
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
    public function getAdvertisementList(Request $request)
    {
        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}
        if($request->has('advertisement_name')){ $advertisement_name = $request->get('advertisement_name');}else{ $advertisement_name = 0;}

        $start = 0;
        if($page)
        $start = $per_page*$page;
        

        $data = DB::table('advertisements');

        // $data = $data->select(
        //     'advertisements.*'  
        // );

         $data = $data->select(
            'advertisements.id as advertisement_id',
            'advertisements.image as advertisement_image',
            'advertisements.name as advertisement_name'   
        );

        $data = $data->where('status',1);

        if($advertisement_name)
        $data = $data->where('name','like',$advertisement_name);

        $data = $data->orderBy('name', 'ASC'); 
        $count = $data->get()->count();  
        $data = $data->offset($start)->limit($per_page)->get();


        if( $data):
            foreach ($data as $key => $advertisement) {

                 $advertisement->advertisement_image = $this->getPhotoUrl($advertisement->advertisement_image);    

            // unset($orders->created_at);unset($orders->updated_at);unset($orders->posted_by); unset($orders->status);   
             
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

        $data = $data->leftJoin('countries', 'countries.id', '=', 'user_doctors.country_id');
        $data = $data->leftJoin('divisions', 'divisions.id', '=', 'user_doctors.division_id');
        $data = $data->leftJoin('cities', 'cities.id', '=', 'user_doctors.city_id');
        $data = $data->leftJoin('zones', 'zones.id', '=', 'user_doctors.zone_id');
        

         $data = $data->select(
            'users.id as doctor_id',
            'users.name as doctor_name',   
            'users.photo as doctor_photo', 
            'user_doctors.*',
            'countries.name as country_name',
            'divisions.name as division_name',
            'cities.name as city_name',
            'zones.name as thana_name',
            'specialities.name as specialty_name', 
            'appointment_charges.amount as doctor_fees'
        );

        $data = $data->whereIn('model_has_roles.role_id',[3]);  

        $data = $data->where('users.status',1);
       

       // $result = $result->orWhere( $field, 'like', '%' . $keyword . '%');


        if($keyword && $keyword!='fees asc' && $keyword!='fees desc'):

        
           $data = $data->where(function($q) use ($keyword){

                $q->orWhere('users.name','like', '%' . $keyword . '%')
                ->orWhere('specialities.name','like', '%' . $keyword . '%')
                ->orWhere('user_doctors.degree','like', '%' . $keyword . '%')
                ->orWhere('user_doctors.relevant_degree','like', '%' . $keyword . '%')
                ->orWhere('user_doctors.sex','like', $keyword . '%')
                ->orWhere('countries.name','like', '%' . $keyword . '%')
                ->orWhere('divisions.name','like', '%' . $keyword . '%')
                ->orWhere('cities.name','like', '%' . $keyword . '%')
                ->orWhere('zones.name','like', '%' . $keyword . '%');

            });

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
             unset($doctor->id);unset($doctor->user_id); 
             
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
    public function getSceduleList(Request $request)
    {
        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}
        if($request->has('schedule_date')){ $schedule_date = $request->get('schedule_date');}else{ $schedule_date = date('Y-m-d');}

        if($request->has('doctor_id')){ $doctor_id = $request->get('doctor_id');}else{ $doctor_id = 0;}

        $start = 0;
        if($page)
        $start = $per_page*$page;

        $c = new Carbon($schedule_date);
        //echo $c->dayOfWeek;

        $dayofweek = (($c->dayOfWeek+2)%7)?(($c->dayOfWeek+2)%7):7;

       // return response()->json(['data' => $dayofweek, 'success' => 1], 200);   

        $data = DB::table('schedulings');
        $data = $data->leftJoin('users', 'users.id', '=', 'schedulings.user_id');
        $data = $data->leftJoin('days', 'days.id', '=', 'schedulings.day_id');
       

         $data = $data->select(
            'schedulings.id as schedule_id',
            'users.id as doctor_id',   
            'users.name as doctor_name',   
            'days.day as schedule_day',
            'schedulings.slot_name as schedule_slot_name',
            'schedulings.slot_duration as schedule_slot_duration',
            'schedulings.start_time as schedule_start_time'
        );

        if($doctor_id)
        $data = $data->where('schedulings.user_id',$doctor_id);
        $data = $data->whereIn('schedulings.day_id',[$dayofweek]);
        $data = $data->where('users.status',1);
        $data = $data->where('schedulings.status',1);

        $data = $data->orderBy('schedule_start_time', 'ASC'); 

        $count = $data->get()->count();  
        $data = $data->offset($start)->limit($per_page)->get();

        if( $data):
            foreach ($data as $key => $doctor) {

            $doctor->schedule_date = $schedule_date;

            $doctor->availability=1; 
             $p_today_app = DB::table('patient_appointments')->where('doctor_user_id',$doctor->doctor_id)->where('scheduling_id',$doctor->schedule_id)->where('appointment_date','=',$schedule_date)->where('status',1)->count();
             if($p_today_app)
             $doctor->availability=0;
          
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
    public function setPatientAppointment(Request $request)
    {

       // return response()->json([ 'data' =>auth()->user()->id, 'success' => 1], 200);
        
        if(auth()->user()->id):

           $validators = array(); 
          
            $push = array(            
                'name' => 'required',             
            );
            $validators = $validators+$push; 
      

        
            $push = array(            
                'age' => 'required',             
            );
            $validators = $validators+$push; 
      

            $push = array(            
                'sex' => 'required',             
            );
            $validators = $validators+$push; 
     

       
            $push = array(            
                'doctor_id' => 'required',
            );
            $validators = $validators+$push;  
      

    
            $push = array(            
                'scheduling_id' => 'required',
            );
            $validators = $validators+$push;  
   

            $push = array(            
                'appointment_date' => 'required',
            );
            $validators = $validators+$push;  
            

           
            $setAttributeNames = array(
                'name' => 'Patient Name',
                'age' => 'Patient Age(Yrs)',
                'sex' => 'Patient Gender',
                'doctor_id' => 'Doctor Name',
                'scheduling_id' => 'Patient Schedule Slot',   
                'appointment_date' => 'Patient Appointment Date'        
            );

            $validator = Validator::make($request->all(), $validators);
            $validator->setAttributeNames($setAttributeNames); 

            if ($validator->fails())
            {
               // return response(['errors'=>$validator->errors()->all()], 422);

                return response()->json(['error' => $validator->errors()->all(), 'success' => 0], 422);
            }

            $success=0;

            //$appointments = DB::table('patient_appointments')->orderBy('id', 'DESC')->first();

            if($request->all()){
               $Id =  DB::table('patient_appointments')->insertGetId([
                    'name' => $request->get('name'),
                    'age' => $request->get('age'),
                    'sex' => $request->get('sex'),
                    'doctor_user_id' => $request->get('doctor_id'),
                    'scheduling_id' => $request->get('scheduling_id'),
                    'appointment_date' => $request->get('appointment_date'),
                    'patient_user_id' => auth()->user()->id, 
                    'posted_by' => auth()->user()->id,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]); 
            }

            if($Id){

                DB::table('patient_appointments')->where('id',$Id)->where('patient_user_id',auth()->user()->id)->update([ 'appoint_no' =>'BPC-'.$Id]); 

                  
                $schedulings = DB::table('schedulings')->where('id',$request->get('scheduling_id'))->orderBy('id', 'DESC')->first();

                if($schedulings){

                    $site_settings = DB::table('site_settings')->where('field_name','notification_sending_time')->where('status',1)->first();

                    if($site_settings){ 

                        $appoint_time = $request->get('appointment_date').' '.$schedulings->start_time;

                        $dt = Carbon::create($appoint_time);

                        $dt->toDateTimeString(); 

                        DB::table('user_notifications')->insertGetId([
                            'user_id' => auth()->user()->id, 
                            'mobile_no' => auth()->user()->mobile_no, 
                            'appointment_id' => $Id,
                            'sending_date' => $request->get('appointment_date'),
                            'sending_time' => $dt->subHour($site_settings->field_value),
                            'sending_status' => 0,
                            'message' => 'Your Doctor Appointment No. '.'BPC-'.$Id.' and appointment time is '.date("F j, Y, g:i a", strtotime($appoint_time)),
                            'created_at' => date('Y-m-d H:i:s')
                        ]); 

                    }

                }
            } 

            if($Id){        
                $this->getLogs((array)$request->all());
                return response()->json([ 'data' =>$Id, 'success' => 1], 200);
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
                'patient_prescription' => 'required',
               
            );
            $validators = $validators+$push; 
            
            $setAttributeNames = array(
                'patient_prescription' => 'Patient Prescription',
                'appointment_id' => 'Patient Appointment Id'
                        
            );

            $validator = Validator::make($request->all(), $validators);
            $validator->setAttributeNames($setAttributeNames); 

            if ($validator->fails())
            {
                return response(['errors'=>$validator->errors()->all()], 422);
            }

            $is_photo = 0;
            $patient_prescription = '';  
            $id = 0;
            $success = 0;
          
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

                        // Set user profile image path in database to path
                       $success = DB::table('user_files')->insertGetId([
                            'name' => $name,
                            'appointment_id' => $request->get('appointment_id'),
                            'user_id' => auth()->user()->id,
                            'path' => $path,
                            'posted_by' => Auth::user()->id,
                            'type_id' => 2,
                            'status' => 1,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }  
                }
                            
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setPatientPayment(Request $request)
    {

       // return response()->json([ 'data' =>auth()->user()->id, 'success' => 1], 200);
        
        if(auth()->user()->id):

           $validators = array(); 
           
            $push = array(            
                'patient_appointment_id' => 'required',
               
            );
            $validators = $validators+$push; 
     
            $push = array(            
                'payment_type' => 'required',
               
            );
            $validators = $validators+$push; 

            $push = array(            
                'payeer_id' => 'required|string|unique:payments',   
               
            );
            $validators = $validators+$push; 

            $push = array(            
                'amount' => 'required',
               
            );
            $validators = $validators+$push; 



            $setAttributeNames = array(
                'patient_appointment_id' => 'Patient Appointment Id',
                'payment_type' => 'Patient payment type (such as: 1=pending, 2=complete, 3=cancelled)',
                'payeer_id' => 'Payment Payeer Id',
                'amount' => 'Patient Doctor Fees'
            );

            $validator = Validator::make($request->all(), $validators);
            $validator->setAttributeNames($setAttributeNames); 

            if ($validator->fails())
            {
                return response(['errors'=>$validator->errors()->all()], 422);
            }

             $count = DB::table('patient_appointments')->where('id',$request->get('patient_appointment_id'))->where('patient_user_id',Auth::user()->id)->count();

              $pay_count = DB::table('payments')->where('invoice_no',$request->get('payeer_id'))->where('posted_by',Auth::user()->id)->count();

            if($count && !$pay_count){

                $success = DB::table('payments')->insertGetId([
                    'invoice_no' => $request->get('payeer_id'),
                    'patient_appointment_id' => $request->get('patient_appointment_id'),
                    'amount' => $request->get('amount'),
                    'payment_type' => $request->get('payment_type'),
                    'payeer_id' => $request->get('payeer_id'),
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setFirebaseToken(Request $request)
    {

       // return response()->json([ 'data' => auth()->user()->id, 'success' => 1], 200);
        
        if(auth()->user()->id):

           $validators = array(); 
           
            $push = array(            
                'token' => 'required',
               
            );
            $validators = $validators+$push; 
     

            $setAttributeNames = array(
                'token' => 'Firebase Token'
            );

            $validator = Validator::make($request->all(), $validators);
            $validator->setAttributeNames($setAttributeNames); 

            if ($validator->fails())
            {
                return response(['errors'=>$validator->errors()->all()], 422);
            }

             $data =  DB::table('users')->where('id',auth()->user()->id)->update([ 'token' => $request->get('token')]); 


            if($data){      

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
     * Get the getUserPaymentOrder per Page
     *
     * @return [json] User Specialty  object
     */
    public function getDoctorWishList(Request $request)
    {
        
        $doctor_id_list = array();
        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}
        if($request->has('doctor_id_list')){ $doctor_id_list = explode(',', $request->get('doctor_id_list'));}else{ $doctor_id_list = 0;}

        $start = 0;
        if($page)
        $start = $per_page*$page;
        

        $data = DB::table('users');
        $data = $data->leftJoin('user_doctors', 'user_doctors.user_id', '=', 'users.id');
        $data = $data->leftJoin('specialities', 'specialities.id', '=', 'user_doctors.speciality_id');
        $data = $data->leftJoin('appointment_charges', 'appointment_charges.user_id', '=', 'users.id');
        $data = $data->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id');

        $data = $data->leftJoin('countries', 'countries.id', '=', 'user_doctors.country_id');
        $data = $data->leftJoin('divisions', 'divisions.id', '=', 'user_doctors.division_id');
        $data = $data->leftJoin('cities', 'cities.id', '=', 'user_doctors.city_id');
        $data = $data->leftJoin('zones', 'zones.id', '=', 'user_doctors.zone_id');
        

         $data = $data->select(
            'users.id as doctor_id',
            'users.name as doctor_name',   
            'users.photo as doctor_photo', 
            'user_doctors.*',
            'countries.name as country_name',
            'divisions.name as division_name',
            'cities.name as city_name',
            'zones.name as thana_name',
            'specialities.name as specialty_name', 
            'appointment_charges.amount as doctor_fees'
        );

        $data = $data->where('users.status',1);
        $data = $data->whereIn('users.id',$doctor_id_list);

        $data = $data->orderBy('users.created_at', 'DESC'); 

        $count = $data->get()->count();  
        $data = $data->offset($start)->limit($per_page)->get();

       //return response()->json(['data' => $doctor_id_list, 'success' => 1], 200);   


        if( $data):
            foreach ($data as $key => $doctor) {

             $p_today_app = DB::table('patient_appointments')->where('doctor_user_id',$doctor->doctor_id)->where('appointment_date','=',date('Y-m-d'))->count();
             $doctor->availability=0;
             $d_app_limit = DB::table('schedulings')->where('user_id',$doctor->doctor_id)->count();
             if(($d_app_limit-$p_today_app)>0)
                $doctor->availability=1;  

             $doctor->doctor_photo = $this->getPhotoUrl($doctor->doctor_photo);    

             unset($doctor->created_at);unset($doctor->updated_at);unset($doctor->posted_by); unset($doctor->status);   
             unset($doctor->device_id);unset($doctor->reference);unset($doctor->user_id); unset($doctor->id); 
             
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
    public function getPaymentList(Request $request)
    {
        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}
        

        $start = 0;
        if($page)
        $start = $per_page*$page;
        

        $data = DB::table('payments');
        $data = $data->leftJoin('patient_appointments', 'patient_appointments.id', '=', 'payments.patient_appointment_id');
        $data = $data->leftJoin('users', 'users.id', '=', 'patient_appointments.doctor_user_id');


        //$data = $data->leftJoin('appointment_charges', 'appointment_charges.user_id', '=', 'patient_appointments.doctor_user_id');

        // $data = $data->select(
        //     'specialities.*'  
        // );

        $data = $data->select(
            'payments.*',
            'users.photo as doctor_photo', 
            'patient_appointments.name as appoint_patient_name'
           // 'appointment_charges.amount as specialty_name'   
        );

        $data = $data->where('patient_appointments.patient_user_id',Auth::user()->id);
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

            $orders->doctor_photo = $this->getPhotoUrl($orders->doctor_photo);  

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

        $data = $data->leftJoin('user_doctors', 'user_doctors.user_id', '=', 'patient_appointments.doctor_user_id');

        $data = $data->leftJoin('users', 'users.id', '=', 'patient_appointments.doctor_user_id');

        //$data = $data->leftJoin('payments', 'payments.patient_appointment_id', '=', 'patient_appointments.id');

        $data = $data->leftJoin('schedulings', 'schedulings.id', '=', 'patient_appointments.scheduling_id');

        $data = $data->leftJoin('user_patients', 'user_patients.user_id', '=', 'patient_appointments.patient_user_id');


        $data = $data->leftJoin('specialities', 'specialities.id', '=', 'user_doctors.speciality_id')
        ->leftJoin('countries', 'countries.id', '=', 'user_patients.country_id')
        ->leftJoin('divisions', 'divisions.id', '=', 'user_patients.division_id')
        ->leftJoin('cities', 'cities.id', '=', 'user_patients.city_id')
        ->leftJoin('zones', 'zones.id', '=', 'user_patients.zone_id');

        $data = $data->select(
            'patient_appointments.id as patient_appointment_id',
            'patient_appointments.*',
            'user_doctors.*',
            'users.name as doctor_name',
            'users.mobile_no as doctor_mobile_no',
            'users.photo as doctor_photo',
            'user_doctors.sex as doctor_gender',
            'user_doctors.age as doctor_age',
            'user_doctors.note as doctor_note',
            'user_doctors.degree as doctor_degree',
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
           // 'payments.*'    
        );



        $data = $data->where('patient_appointments.patient_user_id',Auth::user()->id);

        $data = $data->where('patient_appointments.status',1);
     
        $data = $data->orderBy('patient_appointments.created_at', 'DESC'); 
        $count = $data->get()->count();  
        $data = $data->offset($start)->limit($per_page)->get();


        if( $data):
            foreach ($data as $key => $orders) {

            $orders->payments = DB::table('payments')->where('payments.patient_appointment_id',$orders->patient_appointment_id)->get();

            foreach ($orders->payments as $key => $payment) {
               if($payment->payment_type==1){
                $payment->payment_type='Pending';
                }elseif($payment->payment_type==2){
                     $payment->payment_type='Complete';
                }if($payment->payment_type==1){
                    $payment->payment_type='Cancelled';
                }   
            }
            

            $orders->doctor_photo = $this->getPhotoUrl($orders->doctor_photo);  

            $orders->payment_date = date("F j, Y", strtotime($orders->created_at)); 

           //$orders->payment_date= $today = date("F j, Y", strtotime($orders->created_at)); 

            unset($orders->status); unset($orders->created_at); unset($orders->updated_at);unset($orders->posted_by); unset($orders->logs);  
            //unset($orders->payeer_id);  

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
     * Get the getUserPaymentOrder per Page
     *
     * @return [json] User Specialty  object
     */
    public function getMyPrescriptionList(Request $request)
    {
        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}

        if($request->has('appointment_id')){ $appointment_id = $request->get('appointment_id');}else{ $appointment_id = 0;}
        

        $start = 0;
        if($page)
        $start = $per_page*$page;

        //return response()->json(['data' => Auth::user()->id, 'success' => 0], 200);   


        $data = DB::table('user_files');

        $data = $data->leftJoin('patient_appointments', 'patient_appointments.id', '=', 'user_files.appointment_id');
        $data = $data->leftJoin('users', 'users.id', '=', 'patient_appointments.doctor_user_id');
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

            'user_files.name as prescription_title',
            'user_files.detail as prescription_detail',
            'user_files.path as prescription_file',
            'patient_appointments.*',
            //'user_doctors.*',
            'users.name as doctor_name',
            'users.mobile_no as doctor_mobile_no',
            'user_doctors.sex as doctor_gender',
            'user_doctors.age as doctor_age',
            'user_doctors.note as doctor_note',
            'user_doctors.degree as doctor_degree',
            'specialities.name as doctor_specialty',
            //'user_patients.*',
            'user_patients.sex as patient_gender',
            'user_patients.age as patient_age',
            'user_patients.note as patient_note',
            'schedulings.*',
            'countries.name as patient_country_name',
            'divisions.name as patient_division_name',
            'cities.name as patient_city_name',
            'zones.name as patient_zone_name'    
        );

        if($appointment_id)
        $data = $data->where('patient_appointments.appointment_id',$appointment_id);

        $data = $data->where('patient_appointments.patient_user_id',Auth::user()->id);

        $data = $data->where('patient_appointments.status',1);
     
        $data = $data->orderBy('patient_appointments.created_at', 'DESC'); 
        $count = $data->get()->count();  
        $data = $data->offset($start)->limit($per_page)->get();


        if( $data):
            foreach ($data as $key => $orders) {

                $orders->prescription_file = $this->getPhotoUrl($orders->prescription_file);    

                unset($orders->status); unset($orders->created_at); unset($orders->updated_at);unset($orders->posted_by); 

                unset($orders->id); unset($orders->user_id); 
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
    public function getMyNotificationList(Request $request)
    {
        if($request->has('page')){ $page = $request->get('page')-1;}else{ $page = 0;}
        if($request->has('per_page')){ $per_page = $request->get('per_page');}else{ $per_page = 12;}

        $start = 0;
        if($page)
        $start = $per_page*$page;

        //return response()->json(['data' => Auth::user()->id, 'success' => 0], 200);   

        $data = DB::table('user_notifications');
        $data = $data->leftJoin('patient_appointments', 'patient_appointments.id', '=', 'user_notifications.appointment_id');
        $data = $data->leftJoin('users', 'users.id', '=', 'user_notifications.user_id');

        $data = $data->select(

            //'user_notifications.id as notification_id',
            'user_notifications.*'
            //'patient_appointments.*',
           // 'users.*',
        );


        $data = $data->where('user_notifications.user_id',Auth::user()->id);
        $data = $data->where('user_notifications.sending_status',1);
        $data = $data->orderBy('user_notifications.created_at', 'DESC'); 
        $count = $data->get()->count();  
        $data = $data->offset($start)->limit($per_page)->get();


        if( $data):
            foreach ($data as $key => $orders) {

                unset($orders->mobile_no); unset($orders->sending_date); 
             }
            
           $this->getLogs( (array)$data);
           return response()->json(['data' =>  $data,'items' => $count,'current_page' =>  $page+1,'limit' => $per_page, 'offset' => $start, 'success' => 1], 200);
        else:
            $this->getLogs('empty');
           return response()->json(['data' => 'empty', 'success' => 0], 200);   
        endif;

    }  



 
    


    
  
}