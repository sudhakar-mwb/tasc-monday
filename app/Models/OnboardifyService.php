<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardifyService extends Model
{
    use HasFactory;

    protected $table = 'onboardify_service';
    
    protected $fillable = ['title', 'description', 'image', 'file_location', 'service_setting_data', 'board_id', 'service_column_value_filter', 'service_form_link', 'service_chart_link', 'created_at', 'updated_at', 'deleted_at', 'profile_id'];

    public function profile()
    {
        return $this->belongsTo(OnboardifyProfile::class, 'profile_id');
    }
}