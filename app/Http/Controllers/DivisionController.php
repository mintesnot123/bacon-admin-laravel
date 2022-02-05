<?php

namespace App\Http\Controllers;

use App\Division;


use Illuminate\Http\Request;

use DB;

    

class DivisionController extends Controller

{ 

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

         $this->middleware('permission:division-list|division-create|division-edit|division-delete', ['only' => ['index','show']]);

         $this->middleware('permission:division-create', ['only' => ['create','store']]);

         $this->middleware('permission:division-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:division-delete', ['only' => ['destroy']]);

    }

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {

        $divisions = Division::latest()->paginate(10);
        

        return view('admin.divisions.index',compact('divisions'))

        ->with('i', (request()->input('page', 1) - 1) * 10);

    }

    

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        $countries =  array('' => 'Select Country') + DB::table('countries')->where('status',1)->pluck('name','id')->toArray();

        return view('admin.divisions.create',compact('countries'));

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

        ]);


        $data = $request->all();

        $data['posted_by'] = $request->user()->id;

        $data['status']=1;

        Division::create($data);

        return redirect()->route('divisions.index')

        ->with('success','division created successfully.');

    }

    

    /**

     * Display the specified resource.

     *

     * @param  \App\Division  $divisions

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        $divisions = Division::find($id);

        return view('admin.divisions.show',compact('divisions'));

    }

    

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Division  $divisions

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {
        $divisions = Division::find($id);
        $countries =  array('' => 'Select Country') + DB::table('countries')->where('status',1)->pluck('name','id')->toArray();

        return view('admin.divisions.edit',compact('divisions','countries'));

    }

    

    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\Division  $divisions

     * @return \Illuminate\Http\Response

     */

     public function update($id, Request $request)

    {

         request()->validate([

            'name' => 'required',

            'country_id' => 'required',

        ]);

        $divisions = Division::find($id);

        $data = $request->all();

        $data['posted_by'] = $request->user()->id;

        $divisions->update($data);

        return redirect()->route('divisions.index')
        ->with('success','Division updated successfully');

    }

    

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Division  $divisions

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {
        
        $divisions = Division::find($id);

        if($divisions):
            $divisions->delete();
            return redirect()->route('divisions.index')
            ->with('success','Division deleted successfully');
        else:

            return redirect()->route('divisions.index')
            ->with('error','Division delete failed');
        endif;  
    }

}