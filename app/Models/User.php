<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Role; 

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    public $timestamps = false; 
    protected $fillable = [
        'id_role',
        'nip',
        'nama',
        'notlp',
        'alamat',
        'email',
        'password',
        'foto_profile',
        'face_embedding',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $casts = [
        'face_embedding' => 'array',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class,'id_role');
    }



    /**
     * Untuk JWT (login dengan token)
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role->nama_role
        ];
    }
}
