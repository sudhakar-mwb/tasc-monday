<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColourMappings extends Model
{
    use HasFactory;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'id', 'colour_name', 'colour_value', 'updated_at', 'created_at'
    ];

    /**
    * The attributes excluded from the model's JSON form.
    *
    * @var array
    */
    // protected $hidden = [
    //     'password',
    // ];
}
