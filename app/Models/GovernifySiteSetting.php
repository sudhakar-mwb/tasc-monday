<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GovernifySiteSetting extends Model
{
    use HasFactory;

    protected $table = 'governify_site_setting';
    
    protected $fillable = ['id', 'ui_settings', 'logo_name', 'logo_location','status' ,'updated_at', 'created_at'];

}