<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $table = "divisions";

    protected $fillable = [

        'name','country_id','posted_by','status','created_at', 'updated_at'

    ];

    /**
     * Get the division that owns the country.
     */
    public function country()
    {
        return $this->belongsTo('App\Country','country_id');
    }
}
