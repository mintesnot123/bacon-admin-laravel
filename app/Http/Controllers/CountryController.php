<?php

namespace App\Http\Controllers;

use App\Country;


use Illuminate\Http\Request;

    

class CountryController extends Controller

{ 

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

         $this->middleware('permission:country-list|country-create|country-edit|country-delete', ['only' => ['index','show']]);

         $this->middleware('permission:country-create', ['only' => ['create','store']]);

         $this->middleware('permission:country-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:country-delete', ['only' => ['destroy']]);

    }

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {

        $countries = Country::latest()->paginate(10);



        return view('admin.countries.index',compact('countries'))

            ->with('i', (request()->input('page', 1) - 1) * 10);

    }

    

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        return view('admin.countries.create');

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

            'code' => 'required',

        ]);

        $data = $request->all();

        $data['posted_by']=$request->user()->id;

        $data['status']=1;



    

        Country::create($data);

    
        return redirect()->route('countries.index')

                        ->with('success','Country created successfully.');

    }

    

    /**

     * Display the specified resource.

     *

     * @param  \App\Country  $countries

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        $countries = Country::find($id);

        return view('admin.countries.show',compact('countries'));

    }

    

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Country  $countries

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {
        $countries = Country::find($id);
        return view('admin.countries.edit',compact('countries'));

    }

    

    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\Country  $countries

     * @return \Illuminate\Http\Response

     */

    public function update($id, Request $request)

    {

          request()->validate([

            'name' => 'required',

            'code' => 'required',

        ]);

        $countries = Country::find($id);


        $data = $request->all();

        $data['posted_by'] = $request->user()->id;

        $countries->update($data);

        return redirect()->route('countries.index')

        ->with('success','Country updated successfully');

    }

    

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Country  $countries

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {
        
        $countries = Country::find($id);

        if($countries):
            $countries->delete();
            return redirect()->route('countries.index')
            ->with('success','Country deleted successfully');
        else:

            return redirect()->route('countries.index')
            ->with('error','Country delete failed');
        endif;  
    }

}
