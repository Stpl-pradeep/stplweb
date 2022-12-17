<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Blog extends Model
{
    use HasFactory;
    use Sluggable;

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'cat_id');
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin', 'user_id');
    }

    public function sluggable(): array
    {
        return [
            'slug' => ['source' => 'title']
        ];
    }

    protected $fillable = [
        'title', 'slug', 'cat_id', 'user_id', 'image', 'img_title', 'img_alt', 'short_description', 'description', 'status', 'del', 'created_at', 'updated_at'
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
        'email_verified_at' => 'datetime'
    ];
}
