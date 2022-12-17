<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Geolocation extends Model
{
       use HasFactory;

     protected $fillable = [
        'id','name','pin','parent_id','area_id'
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
   // protected $fillable = [];

    protected $table = 'geolocations';
    public $timestamps = false;
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
}
