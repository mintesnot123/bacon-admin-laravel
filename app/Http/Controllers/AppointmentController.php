<?php

namespace App\Http\Controllers;
  

use App\Appointment;

use Illuminate\Http\Request;

use Session;

    

class AppointmentController extends Controller

{ 

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

         $this->middleware('permission:appointment-list|appointment-create|appointment-edit|appointment-delete', ['only' => ['index','show']]);

         $this->middleware('permission:appointment-create', ['only' => ['create','store']]);

         $this->middleware('permission:appointment-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:appointment-delete', ['only' => ['destroy']]);

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



        $appointments = new Appointment;

        if(isset($search->keyword)){

           $search = $search->keyword;
           $appointments = $appointments->where(function($q) use ($search){
                  $q->orWhere('patient_appointments.name', 'like', '%'.$search.'%')
                 // ->orWhere('users.email', 'like', '%'.$search.'%') 
                ->orWhere('patient_appointments.appoint_no', 'like', '%'.$search.'%'); 
            });

        }

        $appointments = $appointments->latest()->paginate(20);



        return view('admin.appointments.index',compact('appointments'))

            ->with('i', (request()->input('page', 1) - 1) * 20);

    }

    

    
    public function show($id)

    {

       // $appointment = Appointment::find($id);

        $appointment = Appointment:: leftJoin('user_doctors', 'user_doctors.user_id', '=', 'patient_appointments.doctor_user_id')
        
        ->leftJoin('user_patients', 'user_patients.user_id', '=', 'patient_appointments.patient_user_id')
        ->leftJoin('specialities', 'specialities.id', '=', 'user_doctors.speciality_id')
        ->leftJoin('schedulings', 'schedulings.id', '=', 'patient_appointments.scheduling_id')

        ->leftJoin('countries', 'countries.id', '=', 'user_patients.country_id')
        ->leftJoin('divisions', 'divisions.id', '=', 'user_patients.division_id')
        ->leftJoin('cities', 'cities.id', '=', 'user_patients.city_id')
        ->leftJoin('zones', 'zones.id', '=', 'user_patients.zone_id')

        ->select(
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
        )

        ->where('patient_appointments.id',$id)->first();

         $appointment->id =$id;

          // echo'<pre>';
          // print_r($appointment);
          // echo'</pre>';

         // die();

        return view('admin.appointments.show',compact('appointment'));

    }

    

    

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Appointment  $appointment

     * @return \Illuminate\Http\Response

     */

    public function destroy(Appointment $appointment)

    {
        $appointment->delete();
        return redirect()->route('appointments.index')
        ->with('success','Appointment deleted successfully');

    }

}