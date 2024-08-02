<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GovernifyCreateServiceRecords extends Model
{
    use HasFactory;

    protected $table = 'governify_create_service_records';
    
    protected $fillable = ['user_id','category_id', 'service_id', 'form_id','governify_item_id'];
}