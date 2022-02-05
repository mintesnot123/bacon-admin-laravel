<?php

namespace App\Http\Controllers;

use App\City;


use Illuminate\Http\Request;

use DB;

use Session;

    

class CityController extends Controller

{ 

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

         $this->middleware('permission:city-list|city-create|city-edit|city-delete', ['only' => ['index','show']]);

         $this->middleware('permission:city-create', ['only' => ['create','store']]);

         $this->middleware('permission:city-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:city-delete', ['only' => ['destroy']]);

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


        $cities = new City;

       // $cities = $cities->leftJoin('users', 'users.id', '=', 'appointment_charges.user_id');

        if(isset($search->keyword)){

           $search = $search->keyword;
           $cities = $cities->where(function($q) use ($search){
                  $q->orWhere('cities.name', 'like', '%'.$search.'%')
                 // ->orWhere('cities.amount', 'like', '%'.$search.'%') 
                ->orWhere('cities.status', 'like', '%'.$search.'%'); 
            });

        }

        $cities = $cities->latest('cities.created_at')->paginate(10);

        return view('admin.cities.index',compact('cities'))
        ->with('i', (request()->input('page', 1) - 1) * 10);

    }

    

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {
        $countries = array('' => 'Select Country') + DB::table('countries')->where('status',1)->pluck('name','id')->toArray();
        $divisions = array('' => 'Select Division') + DB::table('divisions')->where('status',1)->pluck('name','id')->toArray();

        return view('admin.cities.create',compact('countries','divisions'));

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

            'country_id' => 'required',

            'division_id' => 'required',

        ]);



        $data = $request->all();

        $data['posted_by'] = $request->user()->id;

        $data['status']=1;

        City::create($data);

    
        return redirect()->route('cities.index')

            ->with('success','City created successfully.');

    }

    

    /**

     * Display the specified resource.

     *

     * @param  \App\City  $cities

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        $cities = City::find($id);

        return view('admin.cities.show',compact('cities'));

    }

    

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\City  $cities

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {
        $cities = City::find($id);
        $countries = array('' => 'Select Country') + DB::table('countries')->where('status',1)->pluck('name','id')->toArray();
        $divisions = array('' => 'Select Division') + DB::table('divisions')->where('status',1)->pluck('name','id')->toArray();
        return view('admin.cities.edit',compact('cities','countries','divisions'));
    }

    

    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\City  $cities

     * @return \Illuminate\Http\Response

     */

   
    public function update($id, Request $request)

    {

         request()->validate([

            'name' => 'required',

            'country_id' => 'required',

            'division_id' => 'required',

        ]);

    
        $cities = City::find($id);

        $data = $request->all();

        $data['posted_by'] = $request->user()->id;

        $cities->update($data);

    
        return redirect()->route('cities.index')

             ->with('success','City updated successfully');

    }

    

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\City  $cities

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {
        
        $cities = City::find($id);

        if($cities):
            $cities->delete();
            return redirect()->route('cities.index')
            ->with('success','City deleted successfully');
        else:
            return redirect()->route('cities.index')
            ->with('error','City delete failed');
        endif;  
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function getDivision(Request $request)
    {
        $selected='';
        if($request->get('division_id'))
            $selected=$request->get('division_id');
        $divisions = DB::table('divisions')->where('country_id',$request->get('country_id'))->where('status',1)->pluck('name','id');
        $html ='<option>--- Select Division ---</option>';
        if($divisions){
            foreach ($divisions as $key => $division) {
               $html .='<option value="'.$key.'"';
               if($selected==$key)$html .=' Selected ';
               $html .='>'. $division.'</option>';
            }
        }
        return response()->json(['options'=>$html]);
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function getCity(Request $request)
    {
        $selected='';
        if($request->get('city_id'))
            $selected=$request->get('city_id');
        $cities = DB::table('cities')->where('division_id',$request->get('division_id'))->where('status',1)->pluck('name','id');
        $html ='<option>--- Select City ---</option>';
        if($cities){
            foreach ($cities as $key => $city) {
               $html .='<option value="'.$key.'"';
               if($selected==$key)$html .=' Selected ';
               $html .='>'. $city.'</option>';
            }
        }
        return response()->json(['options'=>$html]);
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function getZone(Request $request)
    {
        $selected='';
        if($request->get('zone_id'))
            $selected=$request->get('zone_id');
        $zones = DB::table('zones')->where('city_id',$request->get('city_id'))->where('status',1)->pluck('name','id');
        $html ='<option>--- Select Zone/Thana ---</option>';
        if($zones){
            foreach ($zones as $key => $zone) {
               $html .='<option value="'.$key.'"';
               if($selected==$key)$html .=' Selected ';
               $html .='>'. $zone.'</option>';
            }
        }
        return response()->json(['options'=>$html]);
    }

}
