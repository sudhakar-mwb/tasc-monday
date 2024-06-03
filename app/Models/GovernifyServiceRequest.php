<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GovernifyServiceRequest extends Model
{
    use HasFactory;

    protected $table = 'governify_service_requests';
    
    protected $fillable = ['title', 'description', 'image', 'file_location', 'form','service_categorie_id', 'created_at', 'updated_at', 'deleted_at', 'service_categories_request_index'];

    public function serviceCategorie()
    {
        return $this->belongsTo(GovernifyServiceCategorie::class);
    }

    public function form()
    {
        return $this->belongsTo(GovernifyServiceRequestForms::class, 'form', 'id');
    }

    // public function serviceRequests()
    // {
    //     return $this->belongsTo(GovernifyServiceCategorie::class);
    // }
}
