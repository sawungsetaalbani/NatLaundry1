<?php
// simpan di: app/Models/Pengguna.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'pengguna';

    // primary key tabel pengguna BUKAN "id". Sesuaikan dengan nama kolom di migration-mu.
    protected $primaryKey = 'id_pengguna';

    protected $fillable = [
        'nama_pengguna',
        'no_telpon',
        'username',
        'alamat',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 'hashed' = password otomatis di-hash saat disimpan (Laravel 10+)
    protected $casts = [
        'password' => 'hashed',
    ];
}