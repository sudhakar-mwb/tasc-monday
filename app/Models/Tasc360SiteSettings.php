<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasc360SiteSettings extends Model
{
    use HasFactory;

    protected $table = 'tasc360_site_settings';
    
    protected $fillable = ['id', 'ui_settings', 'logo_name', 'logo_location','status' ,'updated_at', 'created_at'];
}
