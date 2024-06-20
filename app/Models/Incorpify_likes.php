<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incorpify_likes extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_type_id',
        'item_type',
        'liked',
    ];
}
