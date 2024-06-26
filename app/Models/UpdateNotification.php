<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpdateNotification extends Model
{
    use HasFactory;

    protected $table = 'update_notification';

    protected $fillable = [
        'email', 
        'item_data'
    ];
}
