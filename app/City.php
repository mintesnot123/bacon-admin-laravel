<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = "cities";

    protected $fillable = [

        'name','country_id','division_id','serial_code','posted_by','status','created_at', 'updated_at'

    ];


    /**
     * Get the city that owns the country.
     */

    public function country()
    {
        return $this->belongsTo('App\Country','country_id');
    }

    /**
     * Get the city that owns the country.
     */
    
    public function division()
    {
        return $this->belongsTo('App\Division','division_id');
    }
}
