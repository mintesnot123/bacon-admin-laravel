<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
  protected $table = "medicines";

    protected $fillable = [

        'name','icon','detail','posted_by','status','created_at', 'updated_at'

    ];
}
