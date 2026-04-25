<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontakEvent extends Model
{
    use HasFactory;

    protected $table = 'kontak_event';

    protected $fillable = [
        'nama',
        'email',
        'no_hp',
        'judul_event',
        'deskripsi_event',
        'pesan',
        'status',
        'balasan',
        'replied_at'
    ];
}
