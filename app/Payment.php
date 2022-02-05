<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = "payments";

    protected $fillable = [

        'invoice_no','patient_appointment_id','payment_type','payeer_id','logs','amount','posted_by','status','created_at', 'updated_at'

    ];


    /**
     * Get the user that owns the country.
     */

    public function appointment()
    {
        return $this->belongsTo('App\Appointment','patient_appointment_id');
    }

}
