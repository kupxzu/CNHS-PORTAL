<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'u_users';

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'role',
        'activate'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'activate' => 'boolean',
        'email_verified_at' => 'datetime',
    ];
}