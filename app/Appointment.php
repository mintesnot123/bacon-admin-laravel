<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DateTime;

class Appointment extends Model
{
    protected $table = "patient_appointments";

    protected $fillable = [

        'appoint_no','doctor_user_id','patient_user_id','scheduling_id','appointment_date','posted_by','status','created_at', 'updated_at'

    ];


    /**
     * Get the user that owns the country.
     */

    public function postedby()
    {
        return $this->belongsTo('App\User','posted_by');
    }



	/**
     * Get the user that owns the country.
     */

    public function doctor()
    {
        return $this->belongsTo('App\User','doctor_user_id');
    }


    /**
     * Get the user that owns the country.
     */

    public function patient()
    {
        return $this->belongsTo('App\User','patient_user_id');
    }


    /**
     * Get the user that owns the country.
     */

    public function schedule()
    {
        return $this->belongsTo('App\Scheduling','scheduling_id');
    }


    public static function schedule_date($date, $time)
    {
    	$date = new DateTime($date.' '.$time);

		return $date->format('D jS \of F Y h:i A');

    }


}
