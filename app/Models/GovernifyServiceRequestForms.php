<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GovernifyServiceRequestForms extends Model
{
    use HasFactory;

    protected $table = 'governify_service_request_forms';
    
    protected $fillable = ['name', 'description','form_data', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'form_data' => 'array', // Automatically cast form_data to array
    ];

    public function serviceRequests()
    {
        return $this->hasMany(GovernifyServiceRequest::class, 'form', 'id');
    }

    // public function serviceRequests()
    // {
    //     return $this->hasMany(ServiceRequest::class, 'form_id', 'id');
    // }
}
