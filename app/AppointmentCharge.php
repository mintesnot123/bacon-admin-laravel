<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppointmentCharge extends Model
{
    protected $table = "appointment_charges";

    protected $fillable = [

        'name','user_id','amount','posted_by','status','created_at', 'updated_at'

    ];

    /**
     * Get the AppointmentCharge that owns the doctor.
     */
    public function doctor()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
