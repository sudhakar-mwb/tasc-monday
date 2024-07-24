<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncorpifySiteSettings extends Model
{
    use HasFactory;

    protected $table = 'incorpify_site_setting';
    
    protected $fillable = ['id', 'ui_settings', 'logo_name', 'logo_location', 'board_id', 'status' ,'updated_at', 'created_at'];

}