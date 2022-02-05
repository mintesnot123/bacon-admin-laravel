<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

class Scheduling extends Model
{
    protected $table = "schedulings";

    protected $fillable = [

       'user_id','day_id','slot_name','slot_duration','start_time','end_time','posted_by','status','created_at', 'updated_at'

    ];


     /**
     * Get the AppointmentCharge that owns the doctor.
     */
    public function doctor()
    {
        return $this->belongsTo('App\User','user_id');
    }


     /**
     * Get the AppointmentCharge that owns the doctor.
     */
    public function postedby()
    {
        return $this->belongsTo('App\User','posted_by');
    }



   


    /**
     * Get the AppointmentCharge that owns the doctor.
     */
    public static function dayofweek($id)
    {
        return DB::table('days')->where('id',$id)->first()->day;
    }




}


