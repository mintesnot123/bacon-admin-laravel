<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
  protected $table = "specialities";

    protected $fillable = [

        'name','icon','posted_by','status','created_at', 'updated_at'

    ];
}