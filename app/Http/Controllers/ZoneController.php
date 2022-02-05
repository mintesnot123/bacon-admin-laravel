<?php

namespace App\Http\Controllers;

use App\Zone;
use Illuminate\Http\Request;
use DB;

use Session;
    

class ZoneController extends Controller

{ 

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

         $this->middleware('permission:zone-list|zone-create|zone-edit|zone-delete', ['only' => ['index','show']]);

         $this->middleware('permission:zone-create', ['only' => ['create','store']]);

         $this->middleware('permission:zone-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:zone-delete', ['only' => ['destroy']]);

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

        $zones = new Zone;

       // $zones = $zones->leftJoin('users', 'users.id', '=', 'appointment_charges.user_id');

        if(isset($search->keyword)){

           $search = $search->keyword;
           $zones = $zones->where(function($q) use ($search){
                  $q->orWhere('zones.name', 'like', '%'.$search.'%')
                 // ->orWhere('zones.amount', 'like', '%'.$search.'%') 
                ->orWhere('zones.status', 'like', '%'.$search.'%'); 
            });

        }

        $zones = $zones->latest('zones.created_at')->paginate(10);

        //$zones = Zone::latest()->paginate(10);

        return view('admin.zones.index',compact('zones'))

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
        $cities = array('' => 'Select City') + DB::table('cities')->where('status',1)->pluck('name','id')->toArray();
        return view('admin.zones.create',compact('countries','divisions','cities'));

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

            'city_id' => 'required',

        ]);



        $data = $request->all();

        $data['posted_by'] = $request->user()->id;

        $data['status']=1;

        Zone::create($data);


        return redirect()->route('zones.index')

        ->with('success','Zone created successfully.');

    }

    

    /**

     * Display the specified resource.

     *

     * @param  \App\Zone  $zones

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        $zones = Zone::find($id);

        return view('admin.zones.show',compact('zones'));

    }

    

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Zone  $zones

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {
        $zones = Zone::find($id);

        $countries = array('' => 'Select Country') + DB::table('countries')->where('status',1)->pluck('name','id')->toArray();
        $divisions = array('' => 'Select Division') + DB::table('divisions')->where('status',1)->pluck('name','id')->toArray();
        $cities = array('' => 'Select City') + DB::table('cities')->where('status',1)->pluck('name','id')->toArray();
        return view('admin.zones.edit',compact('zones','countries','divisions','cities'));
    }

    

    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\Zone  $zones

     * @return \Illuminate\Http\Response

     */

     public function update($id, Request $request)

    {

         request()->validate([

            'name' => 'required',

            'country_id' => 'required',

            'division_id' => 'required',

            'city_id' => 'required',

        ]);

        $zones = Zone::find($id); 

        $data = $request->all();

        $data['posted_by'] = $request->user()->id;

        $zones->update($data);
    
        return redirect()->route('zones.index')

         ->with('success','Zone updated successfully');

    }

    
    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Zone  $zones

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)
    {
        
        $zones = Zone::find($id);

        if($zones):
            $zones->delete();
            return redirect()->route('zones.index')
            ->with('success','Zone deleted successfully');
        else:
            return redirect()->route('zones.index')
            ->with('error','Zone delete failed');
        endif;  
    }

}
