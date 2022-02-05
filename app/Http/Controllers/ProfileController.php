<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Traits\UploadTrait;
use Illuminate\Support\Str;

use Log; 
use DB;
use STDclass;
use Session;
use Storage;

class ProfileController extends Controller
{
    use UploadTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('auth.profile');
    }

    public function updateProfile(Request $request)
    {
        // Form validation
        $request->validate([
            'name'      =>  'required',
            //'photo'     =>  'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Get current user
        $user = User::findOrFail(auth()->user()->id);
        // Set user name
        $user->name = $request->input('name');
  
        $photo = '';      
        if($request->hasFile('photo'))
        {
            if($request->file('photo')->isValid())
            {

                if(Storage::has(User::find(auth()->user()->id)->photo))
                {
                    
                    Storage::delete(User::find(auth()->user()->id)->photo);                
                }
                
                $photo  = date('Ymdhis').'.'.$request->file('photo')->getClientOriginalExtension();

                $public_url = public_path();

                // return response()->json([ 'data' =>$photo, 'success' => 1], 200);

                if($request->file('photo')->move($public_url.'/img/users/', $photo))
                {      
                    $request['photo'] = '/img/users/'.$photo;   
                    $photo = '/img/users/'.$photo;     
                    $user->photo = $photo;
                }  
            }
                        
        } 




        // Check if a profile image has been uploaded
        // if ($request->has('photo')) {
        //     // Get image file
        //     $image = $request->file('photo');
        //     // Make a image name based on user name and current timestamp
        //     $name = Str::slug($request->input('name')).'_'.time();
        //     // Define folder path
        //     $folder = '/uploads/images/';
        //     // Make a file path where image will be stored [ folder path + file name + file extension]
        //     $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
        //     // Upload image
        //     $this->uploadOne($image, $folder, 'public', $name);
        //     // Set user profile image path in database to filePath
        //     $user->photo = $filePath;
        // }
        // Persist user record to database
        $user->save();

        // Return user back and show a flash message
        return redirect()->back()->with(['status' => 'Profile updated successfully.']);
    }
}