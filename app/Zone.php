<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $table = "zones";

    protected $fillable = [

        'name','country_id','division_id','city_id','posted_by','status','created_at', 'updated_at','hub_id','zip_code','landmarks'

    ];

    /**
     * Get the zone that owns the country.
     */

    public function country()
    {
        return $this->belongsTo('App\Country','country_id');
    }

    /**
     * Get the zone that owns the division.
     */
    
    public function division()
    {
        return $this->belongsTo('App\Division','division_id');
    }

    /**
     * Get the zone that owns the city.
     */
    
    public function city()
    {
        return $this->belongsTo('App\City','city_id');
    }
}