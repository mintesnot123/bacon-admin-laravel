<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\UsersImport;
//use App\Exports\UsersExport;

use App\Medicine;

use Illuminate\Http\Request;

use Session;




    

class MedicineController extends Controller

{ 

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

         $this->middleware('permission:medicine-list|medicine-create|medicine-edit|medicine-delete', ['only' => ['index','show']]);

         $this->middleware('permission:medicine-create', ['only' => ['create','store']]);

         $this->middleware('permission:medicine-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:medicine-delete', ['only' => ['destroy']]);

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



        $medicines = new Medicine;

        if(isset($search->keyword)){

           $search = $search->keyword;
           $medicines = $medicines->where(function($q) use ($search){
                  $q->orWhere('medicines.name', 'like', '%'.$search.'%')
                 // ->orWhere('medicines.payments', 'like', '%'.$search.'%') 
                ->orWhere('medicines.status', 'like', '%'.$search.'%'); 
            });

        }

        $medicines = $medicines->latest()->paginate(20);


        return view('admin.medicines.index',compact('medicines'))

            ->with('i', (request()->input('page', 1) - 1) * 10);

    }

    

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        return view('admin.medicines.create');

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

                if($request->file('icon')->move($public_url.'/img/medicine/', $icon))
                {      
                    $data['icon'] = '/img/medicine/'.$icon;   
                   
                }  
            }
                        
        } 

        $data['posted_by']=$request->user()->id;
        $data['status']=1;
    

        Medicine::create($data);

    
        return redirect()->route('medicines.index')

             ->with('success','Medicine added successfully.');

    }


     /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function uploadMedicine(Request $request)

    {

        Excel::import(new UsersImport,request()->file('file'));

        return redirect()->route('medicines.index')

             ->with('success','Medicine added successfully.');

    }




    

    /**

     * Display the specified resource.

     *

     * @param  \App\Medicine  $medicines

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        $medicines = Medicine::find($id);

        return view('admin.medicines.show',compact('medicines'));

    }

    

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Medicine  $medicines

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {
        $medicines = Medicine::find($id);
        return view('admin.medicines.edit',compact('medicines'));

    }

    

    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\Medicine  $medicines

     * @return \Illuminate\Http\Response

     */

    public function update($id, Request $request)

    {

          request()->validate([

            'name' => 'required',

           

        ]);

        $medicines = Medicine::find($id);

        $data = $request->all();

        $icon = '';      
        if($request->hasFile('icon'))
        {
            if($request->file('icon')->isValid())
            { 
                $icon  = date('Ymdhis').'.'.$request->file('icon')->getClientOriginalExtension();
                $public_url = public_path();
                // return response()->json([ 'data' =>$icon, 'success' => 1], 200);
                if($request->file('icon')->move($public_url.'/img/medicine/', $icon))
                {      
                    $data['icon'] = '/img/medicine/'.$icon;   
                   
                }  
            }
                        
        } 

        $data['posted_by'] = $request->user()->id;

        $medicines->update($data);

        return redirect()->route('medicines.index')

        ->with('success','Medicine updated successfully');
    }

    

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Medicine  $medicines

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {
        
        $medicines = Medicine::find($id);

        if($medicines):
            $medicines->delete();
            return redirect()->route('medicines.index')
            ->with('success','Medicine deleted successfully');
        else:

            return redirect()->route('medicines.index')
            ->with('error','Medicine delete failed');
        endif;  
    }

}


