<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Cviebrock\EloquentSluggable\Sluggable;

class Menu extends Model
{
    use HasFactory;
    // use Sluggable;


    public function pagename()
    {
        return $this->belongsTo('App\Models\Page', 'page_id');
    }


    //     public function sluggable():array {
    //     return [
    //         'slug' => ['source' => 'name']
    //     ];
    // }

    protected $fillable = [
        'name', 'menu_type', 'layout_type', 'order_by', 'parent_id', 'url', 'type', 'page_id', 'bank_id', 'created_at', 'updated_at'
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
