<?php

  

namespace App;

  

use Illuminate\Notifications\Notifiable;

use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Spatie\Permission\Traits\HasRoles;

use Laravel\Passport\HasApiTokens;

  

class User extends Authenticatable

{

    use Notifiable;

    use HasRoles;

    use HasApiTokens;

  

    /**

     * The attributes that are mass assignable.

     *

     * @var array

     */

    protected $fillable = [

        'name', 'email', 'password','photo','mobile_no','mobile_alt_no','status','type_id'

    ];

  

    /**

     * The attributes that should be hidden for arrays.

     *

     * @var array

     */

    protected $hidden = [

        'password', 'remember_token',

    ];

  

    /**

     * The attributes that should be cast to native types.

     *

     * @var array

     */

    protected $casts = [

        'email_verified_at' => 'datetime',

    ];


    public function getImageAttribute()
    {
       return $this->photo;
    }


    /**
     * Get the user that owns the country.
     */

    public function postedby()
    {
        return $this->belongsTo('App\User','posted_by');
    }


    /**
     * Get the user that owns the country.
     */

    public function country()
    {
        return $this->belongsTo('App\Country','country_id');
    }

    /**
     * Get the user that owns the division.
     */
    
    public function division()
    {
        return $this->belongsTo('App\Division','division_id');
    }

    /**
     * Get the user that owns the city.
     */
    
    public function city()
    {
        return $this->belongsTo('App\City','city_id');
    }

    /**
     * Get the user that owns the Thana.
     */
    
    public function zone()
    {
        return $this->belongsTo('App\Zone','zone_id');
    }




}