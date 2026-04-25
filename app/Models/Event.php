<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'event';
    protected $fillable = [
        'user_id',
        'nama_event',
        'event_type',
        'kategori_id',
        'deskripsi',
        'tanggal',
        'lokasi',
        'meeting_link',
        'harga',
        'foto_event',
        'custom_form_schema',
        'metode_pembayaran',
        'detail_pembayaran',
        'sertifikat_template',
        'sertifikat_config'
    ];
    
    protected $appends = ['foto_event_url'];

    public function getFotoEventUrlAttribute()
    {
        if (!$this->foto_event) return null;
        if (filter_var($this->foto_event, FILTER_VALIDATE_URL)) {
            return $this->foto_event;
        }
        return url('event/' . $this->foto_event);
    }

    protected $casts = [
        'custom_form_schema' => 'array',
        'metode_pembayaran' => 'array',
        'sertifikat_config' => 'array',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function penyelenggara()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranEvent::class, 'event_id');
    }
}
