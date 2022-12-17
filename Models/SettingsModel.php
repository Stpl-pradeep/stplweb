<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Cviebrock\EloquentSluggable\Sluggable;

class SettingsModel extends Model
{
    use HasFactory;
    // use Sluggable;

    //     public function sluggable():array {
    //     return [
    //         'slug' => ['source' => 'name']
    //     ];
    // }

      protected $fillable = [
        'title','keywords','description','logo', 'top_email','top_address','favicon','status', 'del','created_at','updated_at'
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'site_settings';
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
        'email_verified_at' => 'datetime'];
}

