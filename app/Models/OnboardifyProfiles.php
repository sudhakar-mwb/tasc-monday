<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardifyProfiles extends Model
{
    use HasFactory;

    protected $table = 'onboardify_profiles';
    
    protected $fillable = ['title','users', 'created_at', 'updated_at'];

    public function services()
    {
        return $this->hasMany(OnboardifyService::class, 'profile_id');
    }
}
