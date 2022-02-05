<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

 Route::get('/', function () {
    return view('auth.login');
});


 Route::get('/privacy', function () {
    return view('auth.privacy');
});



Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');


Route::get('schedule_run', function () {
     Artisan::call("schedule:run");
    dd("Schedule Run for Firebase Notification");
});


Route::get('clear_cache', function () {
    \Artisan::call('cache:clear');
    dd("Cache is cleared");
});

   
Route::group(['middleware' => ['auth']], function() {


 //    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
 //    Route::post('login', 'Auth\LoginController@login');
 //    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
 //    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
 //    Route::post('register', 'Auth\RegisterController@register');
 //    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
 //    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
 //    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
 //    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

	Route::get('/profile', 'ProfileController@index')->name('profile');
    Route::post('/profile/update', 'ProfileController@updateProfile')->name('profile.update');



    Route::resource('roles','RoleController');

    Route::resource('users','UserController');

    Route::resource('products','ProductController');

    Route::resource('role-permissions','RolePermissionController');

    Route::resource('countries','CountryController');

    Route::resource('divisions','DivisionController');

    Route::resource('cities','CityController');

    Route::resource('zones','ZoneController');


   
    Route::post('ajax-division', 'CityController@getDivision')->name('ajaxDivisionRequest.post');
    Route::get('ajax-division', 'CityController@getDivision')->name('ajaxDivisionRequest.get');
    Route::post('ajax-city', 'CityController@getCity')->name('ajaxCityRequest.post');
    Route::get('ajax-city', 'CityController@getCity')->name('ajaxCityRequest.get');
    Route::post('ajax-zone', 'CityController@getZone')->name('ajaxZoneRequest.post');
    Route::get('ajax-zone', 'CityController@getZone')->name('ajaxZoneRequest.get');


    Route::resource('specialities','SpecialityController');

    Route::resource('advertisements','AdvertisementController');

    Route::resource('appointment_charges','AppointmentChargeController');

    Route::resource('schedulings','SchedulingController');

    Route::resource('settings','SiteSettingController');

    Route::resource('patients','PatientController');

    Route::resource('doctors','DoctorController');

    Route::resource('payments','PaymentController');

    Route::resource('appointments','AppointmentController');

    Route::resource('medicines','MedicineController');

    Route::post('medicines_upload', 'MedicineController@uploadMedicine')->name('uploadMedicine.post');
   // Route::get('medicines_upload', 'MedicineController@uploadMedicine')->name('uploadMedicine.get');
    
});


