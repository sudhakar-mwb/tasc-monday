<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryServiceFormMapping extends Model
{
    use HasFactory;

    protected $table = 'category_service_form_mapping';

    protected $fillable = ['service_form_id', 'service_id', 'categorie_id', 'created_at', 'updated_at'];
}
