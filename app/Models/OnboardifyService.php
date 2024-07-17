<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardifyService extends Model
{
    use HasFactory;

    protected $table = 'onboardify_service';
    
    protected $fillable = ['title', 'description', 'image', 'file_location',  'form_embed_code', 'board_id', 'created_at', 'updated_at', 'deleted_at'];
}