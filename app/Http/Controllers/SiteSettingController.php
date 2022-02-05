<?php

namespace App\Http\Controllers;

use App\SiteSetting;


use Illuminate\Http\Request;

    

class SiteSettingController extends Controller

{ 

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

         $this->middleware('permission:site-setting-list|site-setting-create|site-setting-edit|site-setting-delete', ['only' => ['index','show']]);

         $this->middleware('permission:site-setting-create', ['only' => ['create','store']]);

         $this->middleware('permission:site-setting-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:site-setting-delete', ['only' => ['destroy']]);

    }

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {

        $settings = SiteSetting::latest()->paginate(10);



        return view('admin.settings.index',compact('settings'))

            ->with('i', (request()->input('page', 1) - 1) * 10);

    }

    

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        return view('admin.settings.create');

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

            'label_name' => 'required',

            'field_name' => 'required',

            'field_value' => 'required',

        ]);

        $data = $request->all();

        $data['posted_by']=$request->user()->id;

        $data['status']=1;

        SiteSetting::create($data);

    
        return redirect()->route('settings.index')

                        ->with('success','SiteSetting created successfully.');

    }

    

    /**

     * Display the specified resource.

     *

     * @param  \App\SiteSetting  $settings

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        $settings = SiteSetting::find($id);

        return view('admin.settings.show',compact('settings'));

    }

    

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\SiteSetting  $settings

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {
        $settings = SiteSetting::find($id);
        return view('admin.settings.edit',compact('settings'));

    }

    

    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\SiteSetting  $settings

     * @return \Illuminate\Http\Response

     */

    public function update($id, Request $request)

    {

         request()->validate([

            'label_name' => 'required',

           // 'field_name' => 'required',

            'field_value' => 'required',

        ]);

        $settings = SiteSetting::find($id);


        $data = $request->all();

        $data['posted_by'] = $request->user()->id;

        $settings->update($data);

        return redirect()->route('settings.index')

        ->with('success','SiteSetting updated successfully');

    }

    

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\SiteSetting  $settings

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {
        
        $settings = SiteSetting::find($id);

        if($settings):
            $settings->delete();
            return redirect()->route('settings.index')
            ->with('success','SiteSetting deleted successfully');
        else:

            return redirect()->route('settings.index')
            ->with('error','SiteSetting delete failed');
        endif;  
    }

}
