<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Member extends Model
{
    protected $fillable = [
        'member_number',
        'full_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'emergency_contact',
        'status',
        'qr_token',
        'face_descriptor',
        'face_enrolled_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'face_enrolled_at' => 'datetime',
        ];
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    
       
        }
        
    public function memberAccount(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MemberAccount::class);
    }
}
