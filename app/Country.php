<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = "countries";

    protected $fillable = [

        'name','code','prefix','posted_by','status','created_at', 'updated_at'

    ];
}
