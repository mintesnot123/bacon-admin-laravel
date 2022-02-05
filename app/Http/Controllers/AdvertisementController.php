<?php

namespace App\Http\Controllers;

use App\Advertisement;

use Illuminate\Http\Request;

use Session;

    

class AdvertisementController extends Controller

{ 

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

         $this->middleware('permission:advertisement-list|advertisement-create|advertisement-edit|advertisement-delete', ['only' => ['index','show']]);

         $this->middleware('permission:advertisement-create', ['only' => ['create','store']]);

         $this->middleware('permission:advertisement-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:advertisement-delete', ['only' => ['destroy']]);

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



        $advertisements = new Advertisement;

        if(isset($search->keyword)){

           $search = $search->keyword;
           $advertisements = $advertisements->where(function($q) use ($search){
                  $q->orWhere('advertisements.name', 'like', '%'.$search.'%')
                 // ->orWhere('advertisements.payments', 'like', '%'.$search.'%') 
                ->orWhere('advertisements.status', 'like', '%'.$search.'%'); 
            });

        }

        $advertisements = $advertisements->latest()->paginate(20);


        return view('admin.advertisements.index',compact('advertisements'))

            ->with('i', (request()->input('page', 1) - 1) * 10);

    }

    

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        return view('admin.advertisements.create');

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


        $image = '';      
        if($request->hasFile('image'))
        {
            if($request->file('image')->isValid())
            {
                
                $image  = date('Ymdhis').'.'.$request->file('image')->getClientOriginalExtension();

                $public_url = public_path();

                // return response()->json([ 'data' =>$photo, 'success' => 1], 200);

                if($request->file('image')->move($public_url.'/img/advertisement/', $image))
                {      
                    $data['image'] = '/img/advertisement/'.$image;   
                   
                }  
            }
                        
        } 

        $data['posted_by']=$request->user()->id;
        $data['status']=1;
    

        Advertisement::create($data);

    
        return redirect()->route('advertisements.index')

             ->with('success','Advertisement created successfully.');

    }

    

    /**

     * Display the specified resource.

     *

     * @param  \App\Advertisement  $advertisements

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        $advertisements = Advertisement::find($id);

        return view('admin.advertisements.show',compact('advertisements'));

    }

    

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Advertisement  $advertisements

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {
        $advertisements = Advertisement::find($id);
        return view('admin.advertisements.edit',compact('advertisements'));

    }

    

    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\Advertisement  $advertisements

     * @return \Illuminate\Http\Response

     */

    public function update($id, Request $request)

    {

          request()->validate([

            'name' => 'required',

           

        ]);

        $advertisements = Advertisement::find($id);

        $data = $request->all();

        $image = '';      
        if($request->hasFile('image'))
        {
            if($request->file('image')->isValid())
            { 
                $image  = date('Ymdhis').'.'.$request->file('image')->getClientOriginalExtension();
                $public_url = public_path();
                // return response()->json([ 'data' =>$image, 'success' => 1], 200);
                if($request->file('image')->move($public_url.'/img/advertisement/', $image))
                {      
                    $data['image'] = '/img/advertisement/'.$image;   
                   
                }  
            }
                        
        } 

        $data['posted_by'] = $request->user()->id;

        $advertisements->update($data);

        return redirect()->route('advertisements.index')

        ->with('success','Advertisement updated successfully');
    }

    

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Advertisement  $advertisements

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {
        
        $advertisements = Advertisement::find($id);

        if($advertisements):
            $advertisements->delete();
            return redirect()->route('advertisements.index')
            ->with('success','Advertisement deleted successfully');
        else:

            return redirect()->route('advertisements.index')
            ->with('error','Advertisement delete failed');
        endif;  
    }

}


