<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TascInvitation extends Model
{
    use HasFactory;

    protected $table = 'tasc_invitation';

    protected $fillable = [
        'inviter_id',
        'invitee_email',
        'invitation_status',
        'onboardify_status',
        'incorpify_status',
        'governify_status',
    ];

    // If you have a User model and want to define the relationship
    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }
}
