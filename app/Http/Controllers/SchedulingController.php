<?php

namespace App\Http\Controllers;

use App\Scheduling;


use Illuminate\Http\Request;

use Carbon;

use DB;

use Session;

    

class SchedulingController extends Controller

{ 

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

         $this->middleware('permission:scheduling-list|scheduling-create|scheduling-edit|scheduling-delete', ['only' => ['index','show']]);

         $this->middleware('permission:scheduling-create', ['only' => ['create','store']]);

         $this->middleware('permission:scheduling-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:scheduling-delete', ['only' => ['destroy']]);

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



        $schedulings = new Scheduling;

        $schedulings = $schedulings->leftJoin('users', 'users.id', '=', 'schedulings.user_id');

        $schedulings = $schedulings->select('schedulings.*');


        if(isset($search->keyword)){

           $search = $search->keyword;
           $schedulings = $schedulings->where(function($q) use ($search){
                  $q->orWhere('users.name', 'like', '%'.$search.'%')
                 // ->orWhere('schedulings.payments', 'like', '%'.$search.'%') 
                ->orWhere('schedulings.slot_name', 'like', '%'.$search.'%'); 
            });

        }

        $schedulings = $schedulings->orderBy('schedulings.user_id', 'ASC')->orderBy('schedulings.day_id', 'ASC')->orderBy('schedulings.start_time', 'ASC')->paginate(10);




       // $schedulings = Scheduling::orderBy('user_id', 'ASC')->orderBy('day_id', 'ASC')->orderBy('start_time', 'ASC')->paginate(10);



        return view('admin.schedulings.index',compact('schedulings'))

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

        $days =  array('' => 'Select Day') + DB::table('days')->where('status',1)->pluck('day','id')->toArray();

        return view('admin.schedulings.create',compact('doctors','days'));

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

            'day_id' => 'required',

            'slot_name' => 'required',

            'user_id' => 'required',

            'start_time' => 'required',

            'slot_duration' => 'required',

        ],[

            'day_id.required' => 'The day field is required',

            'user_id.required' => 'the doctor field is required'

        ]);

        $data = $request->all();

        $data['posted_by']=$request->user()->id;

        $data['status']=1;


        Scheduling::create($data);

    
        return redirect()->route('schedulings.index')

            ->with('success','Scheduling created successfully.');

    }

    

    /**

     * Display the specified resource.

     *

     * @param  \App\Scheduling  $schedulings

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {
        $schedulings = Scheduling::find($id);

        return view('admin.schedulings.show',compact('schedulings'));

    }

    

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Scheduling  $schedulings

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {
        $schedulings = Scheduling::find($id);

        $doctors = array('' => 'Select Doctor') + DB::table('users')
        ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
        ->where('model_has_roles.role_id',3)
        ->where('users.status',1)->pluck('users.name','users.id')->toArray();

        $days =  array('' => 'Select Day') + DB::table('days')->where('status',1)->pluck('day','id')->toArray();


        return view('admin.schedulings.edit',compact('schedulings','doctors','days'));

    }

    

    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\Scheduling  $schedulings

     * @return \Illuminate\Http\Response

     */

    public function update($id, Request $request)

    {

        request()->validate([

            'day_id' => 'required',

            'slot_name' => 'required',

            'user_id' => 'required',

            'start_time' => 'required',

            'slot_duration' => 'required',

        ],[

            'day_id.required' => 'The day field is required',

            'user_id.required' => 'the doctor field is required'

        ]);

        $schedulings = Scheduling::find($id);


        $data = $request->all();

        $data['posted_by'] = $request->user()->id;

        $schedulings->update($data);

        return redirect()->route('schedulings.index')

        ->with('success','Scheduling updated successfully');

    }

    

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Scheduling  $schedulings

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {
        
        $schedulings = Scheduling::find($id);

        if($schedulings):
            $schedulings->delete();
            return redirect()->route('schedulings.index')
            ->with('success','Scheduling deleted successfully');
        else:

            return redirect()->route('schedulings.index')
            ->with('error','Scheduling delete failed');
        endif;  
    }

}

