<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $table = "site_settings";

    protected $fillable = [

        'label_name','field_name','field_value','posted_by','status','created_at', 'updated_at'

    ];


   /**
     * Get the AppointmentCharge that owns the doctor.
     */
    public function postedby()
    {
        return $this->belongsTo('App\User','posted_by');
    }

}