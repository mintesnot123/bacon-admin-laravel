<?php

namespace App\Http\Controllers;

use App\Speciality;

use Illuminate\Http\Request;

use Session;

    

class SpecialityController extends Controller

{ 

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

         $this->middleware('permission:speciality-list|speciality-create|speciality-edit|speciality-delete', ['only' => ['index','show']]);

         $this->middleware('permission:speciality-create', ['only' => ['create','store']]);

         $this->middleware('permission:speciality-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:speciality-delete', ['only' => ['destroy']]);

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



        $specialities = new Speciality;

        if(isset($search->keyword)){

           $search = $search->keyword;
           $specialities = $specialities->where(function($q) use ($search){
                  $q->orWhere('specialities.name', 'like', '%'.$search.'%')
                 // ->orWhere('specialities.payments', 'like', '%'.$search.'%') 
                ->orWhere('specialities.status', 'like', '%'.$search.'%'); 
            });

        }

        $specialities = $specialities->latest()->paginate(20);


        return view('admin.specialities.index',compact('specialities'))

            ->with('i', (request()->input('page', 1) - 1) * 10);

    }

    

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        return view('admin.specialities.create');

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


        ]);

        $data = $request->all();


        $icon = '';      
        if($request->hasFile('icon'))
        {
            if($request->file('icon')->isValid())
            {
                
                $icon  = date('Ymdhis').'.'.$request->file('icon')->getClientOriginalExtension();

                $public_url = public_path();

                // return response()->json([ 'data' =>$photo, 'success' => 1], 200);

                if($request->file('icon')->move($public_url.'/img/specialty/', $icon))
                {      
                    $data['icon'] = '/img/specialty/'.$icon;   
                   
                }  
            }
                        
        } 

        $data['posted_by']=$request->user()->id;
        $data['status']=1;
    

        Speciality::create($data);

    
        return redirect()->route('specialities.index')

             ->with('success','Speciality created successfully.');

    }

    

    /**

     * Display the specified resource.

     *

     * @param  \App\Speciality  $specialities

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        $specialities = Speciality::find($id);

        return view('admin.specialities.show',compact('specialities'));

    }

    

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Speciality  $specialities

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {
        $specialities = Speciality::find($id);
        return view('admin.specialities.edit',compact('specialities'));

    }

    

    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\Speciality  $specialities

     * @return \Illuminate\Http\Response

     */

    public function update($id, Request $request)

    {

          request()->validate([

            'name' => 'required',

           

        ]);

        $specialities = Speciality::find($id);

        $data = $request->all();

        $icon = '';      
        if($request->hasFile('icon'))
        {
            if($request->file('icon')->isValid())
            { 
                $icon  = date('Ymdhis').'.'.$request->file('icon')->getClientOriginalExtension();
                $public_url = public_path();
                // return response()->json([ 'data' =>$icon, 'success' => 1], 200);
                if($request->file('icon')->move($public_url.'/img/specialty/', $icon))
                {      
                    $data['icon'] = '/img/specialty/'.$icon;   
                   
                }  
            }
                        
        } 

        $data['posted_by'] = $request->user()->id;

        $specialities->update($data);

        return redirect()->route('specialities.index')

        ->with('success','Speciality updated successfully');
    }

    

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Speciality  $specialities

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {
        
        $specialities = Speciality::find($id);

        if($specialities):
            $specialities->delete();
            return redirect()->route('specialities.index')
            ->with('success','Speciality deleted successfully');
        else:

            return redirect()->route('specialities.index')
            ->with('error','Speciality delete failed');
        endif;  
    }

}

