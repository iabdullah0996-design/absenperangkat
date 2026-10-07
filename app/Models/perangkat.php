<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Perangkat extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'perangkat';
    protected $primaryKey = 'nik';

    // Penting agar NIK bertipe String/Char tidak dianggap Auto-Increment Integer
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'jabatan',
        'no_hp',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}