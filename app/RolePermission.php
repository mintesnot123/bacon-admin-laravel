<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = "permissions";

    protected $fillable = [

        'name', 'guard_name','created_at', 'updated_at'

    ];
}
