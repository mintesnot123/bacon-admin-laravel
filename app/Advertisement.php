<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
  protected $table = "advertisements";

    protected $fillable = [

        'name','image','posted_by','status','created_at', 'updated_at'

    ];
}