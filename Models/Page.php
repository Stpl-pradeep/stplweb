<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Page extends Model
{
    use HasFactory;
    use Sluggable;

        public function bankname()
        {
            return $this->belongsTo('App\Models\Partner','bank_name');
        }

        public function sluggable():array {
        return [
            'slug' => ['source' => 'title']
        ];
    }

      protected $fillable = [
        'title','bank_name','slug', 'image', 'img_title', 'img_alt', 'short_description', 'description', 'status','del','created_at','updated_at'
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    // protected $table = 'axis_applications';
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