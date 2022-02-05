<?php

namespace App\Http\Controllers;

use App\AppointmentCharge;

use Illuminate\Http\Request;

use DB;

use Session;

    

class AppointmentChargeController extends Controller

{ 

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

         $this->middleware('permission:Appointment_charge-list|Appointment_charge-create|Appointment_charge-edit|Appointment_charge-delete', ['only' => ['index','show']]);

         $this->middleware('permission:Appointment_charge-create', ['only' => ['create','store']]);

         $this->middleware('permission:Appointment_charge-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:Appointment_charge-delete', ['only' => ['destroy']]);

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


        $charges = new AppointmentCharge;

        $charges = $charges->leftJoin('users', 'users.id', '=', 'appointment_charges.user_id');

        $charges = $charges->select('appointment_charges.*');

        if(isset($search->keyword)){

           $search = $search->keyword;
           $charges = $charges->where(function($q) use ($search){
                  $q->orWhere('users.name', 'like', '%'.$search.'%')
                 // ->orWhere('users.amount', 'like', '%'.$search.'%') 
                ->orWhere('appointment_charges.amount', 'like', '%'.$search.'%'); 
            });

        }

        $charges = $charges->latest('appointment_charges.created_at')->paginate(10);
        

        return view('admin.charges.index',compact('charges'))

        ->with('i', (request()->input('page', 1) - 1) * 10);

    }

    

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        $doctors = array('' => 'Select Doctor') + DB::table('users')
        ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
        ->where('model_has_roles.role_id',3)
        ->where('users.status',1)->pluck('users.name','users.id')->toArray();

        return view('admin.charges.create',compact('doctors'));

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

            'amount' => 'required',

            'user_id' => 'required',

        ]);


        $data = $request->all();

        $data['posted_by'] = $request->user()->id;

        $data['status']=1;

        AppointmentCharge::create($data);

        return redirect()->route('appointment_charges.index')

        ->with('success','Appointment Charge created successfully.');

    }

    

    /**

     * Display the specified resource.

     *

     * @param  \App\AppointmentCharge  $charges

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        $charges = AppointmentCharge::find($id);

        return view('admin.charges.show',compact('charges'));

    }

    

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\AppointmentCharge  $charges

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {
        $charges = AppointmentCharge::find($id);
        $doctors =  array('' => 'Select Doctor') + DB::table('users')
        ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
        ->where('model_has_roles.role_id',3)
        ->where('users.status',1)->pluck('users.name','users.id')->toArray();

        return view('admin.charges.edit',compact('charges','doctors'));

    }

    

    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\AppointmentCharge  $charges

     * @return \Illuminate\Http\Response

     */

     public function update($id, Request $request)

    {

         request()->validate([

            'amount' => 'required',

            'user_id' => 'required',

        ]);

        $charges = AppointmentCharge::find($id);

        $data = $request->all();

        $data['posted_by'] = $request->user()->id;

        $charges->update($data);

        return redirect()->route('appointment_charges.index')
        ->with('success','Appointment Charge updated successfully');

    }

    

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\AppointmentCharge  $charges

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {
        
        $charges = AppointmentCharge::find($id);

        if($charges):
            $charges->delete();
            return redirect()->route('appointment_charges.index')
            ->with('success','Appointment Charge deleted successfully');
        else:

            return redirect()->route('appointment_charges.index')
            ->with('error','Appointment Charge delete failed');
        endif;  
    }

}
