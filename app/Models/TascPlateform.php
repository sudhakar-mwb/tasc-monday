<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TascPlateform extends Model
{
    use HasFactory;
    // Specify the table name if it's not the plural form of the model name
    protected $table = 'tasc_plateform';

    // Specify the fillable fields
    protected $fillable = [
        'plateform_name',
        'plateform_signuplink',
    ];
}
