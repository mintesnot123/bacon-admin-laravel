<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([
    'prefix' => 'auth'
    ], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');

    Route::post('signup_patient', 'AuthController@signupPatient');
    Route::post('login_patient', 'AuthController@loginPatient');


    Route::post('signup_doctor', 'AuthDoctorController@signupDoctor');
    Route::post('login_doctor', 'AuthDoctorController@loginDoctor');


    Route::get('channel_token', 'AuthController@getChannelToken');

    //Route::get('message_token', 'AuthController@getMessageToken');

     Route::get('get_pdf_prescription_list', 'AuthDoctorController@getPDFPatientPrescriptionList');
  


    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');

        Route::get('message_token', 'AuthController@getMessageToken');

        //Patient Detail Update
        Route::post('set_patient_profile', 'AuthController@setPatientProfile');
        //Patient Detail Show
        Route::post('get_patient_profile', 'AuthController@getPatientProfile');
        Route::get('get_patient_profile', 'AuthController@getPatientProfile');
        //get Country List
        Route::post('get_country_list', 'AuthController@getCountryList');
        Route::get('get_country_list', 'AuthController@getCountryList');
        //get Division with country ID List
        Route::post('get_division_list', 'AuthController@getDivisionList');
        Route::get('get_division_list', 'AuthController@getDivisionList');
        //get City with Division ID List
        Route::post('get_city_list', 'AuthController@getCityList');
        Route::get('get_city_list', 'AuthController@getCityList');
        //get City with City ID List
        Route::post('get_zone_list', 'AuthController@getZoneList');
        Route::get('get_zone_list', 'AuthController@getZoneList');
        //get specialty with specialty ID List
        Route::post('get_specialty_list', 'AuthController@getSpecialtyList');
        Route::get('get_specialty_list', 'AuthController@getSpecialtyList');
        //get doctors with keywords List
        Route::post('get_doctor_list', 'AuthController@getDoctorList');
        Route::get('get_doctor_list', 'AuthController@getDoctorList');
        //get doctors with date List
        Route::post('get_schedule_list', 'AuthController@getSceduleList');
        Route::get('get_schedule_list', 'AuthController@getSceduleList');
        //Patient Detail Update
        Route::post('set_patient_appointment', 'AuthController@setPatientAppointment');
        //Patient Detail Update
        Route::post('set_patient_prescription', 'AuthController@setPatientPrescriptions');
         //Patient Detail Update
        Route::post('set_patient_payment', 'AuthController@setPatientPayment');

        //Get Doctor wishlist
        Route::get('get_doctor_wishlist', 'AuthController@getDoctorWishList');
        Route::post('get_doctor_wishlist', 'AuthController@getDoctorWishList');
        //Get Payment List
        Route::post('get_payment_list', 'AuthController@getPaymentList');
        Route::get('get_payment_list', 'AuthController@getPaymentList');
        //Get My Appointment List
        Route::post('get_appointment_list', 'AuthController@getAppointmentList');
        Route::get('get_appointment_list', 'AuthController@getAppointmentList');
        //Get My Prescription List
        Route::post('get_prescription_list', 'AuthController@getMyPrescriptionList');
        Route::get('get_prescription_list', 'AuthController@getMyPrescriptionList');

         //Set firebase Token
        Route::post('set_firebase_token', 'AuthController@setFirebaseToken');

        //Get My Notification List
        Route::post('get_notification_list', 'AuthController@getMyNotificationList');
        Route::get('get_notification_list', 'AuthController@getMyNotificationList');

    
        Route::get('logout_doctor', 'AuthDoctorController@logout');
        Route::get('user_doctor', 'AuthDoctorController@user');
        //Doctor Detail Update
        Route::post('set_doctor_profile', 'AuthDoctorController@setDoctorProfile');
        //Patient Detail Show
        Route::post('get_doctor_profile', 'AuthDoctorController@getDoctorProfile');
        Route::get('get_doctor_profile', 'AuthDoctorController@getDoctorProfile');
        //get doctors with keywords List
        Route::post('get_bdc_doctor_list', 'AuthDoctorController@getDoctorList');
        Route::get('get_bdc_doctor_list', 'AuthDoctorController@getDoctorList');
        //Patient Detail Update
        Route::post('set_bdc_patient_prescription', 'AuthDoctorController@setPatientPrescriptions');
        //Get Payment List
        Route::post('get_bdc_payment_list', 'AuthDoctorController@getPaymentList');
        Route::get('get_bdc_payment_list', 'AuthDoctorController@getPaymentList');
         //Get My Appointment List
        Route::post('get_bdc_appointment_list', 'AuthDoctorController@getAppointmentList');
        Route::get('get_bdc_appointment_list', 'AuthDoctorController@getAppointmentList');
        //Patient Detail Show
        Route::post('get_bdc_patient_list', 'AuthDoctorController@getPatientList');
        Route::get('get_bdc_patient_list', 'AuthDoctorController@getPatientList');
        //Medicine Detail Show
        Route::post('get_medicine_list', 'AuthDoctorController@getMedicineList');
        Route::get('get_medicine_list', 'AuthDoctorController@getMedicineList');
        //Prescription Detail Update
        Route::post('set_patient_prescription', 'AuthDoctorController@setPatientPrescription');
        //Medicine Detail Show
        Route::post('get_patient_prescription_list', 'AuthDoctorController@getPatientPrescriptionList');
        Route::get('get_patient_prescription_list', 'AuthDoctorController@getPatientPrescriptionList');

          //get Advetisements with specialty ID List
        Route::post('get_advetisement_list', 'AuthController@getAdvertisementList');
        Route::get('get_advetisement_list', 'AuthController@getAdvertisementList');


        


    });
});