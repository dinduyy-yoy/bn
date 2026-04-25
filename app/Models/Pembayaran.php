<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;
    protected $table = 'pembayaran';

    protected $fillable = [
        'pendaftaran_id',
        'jumlah_bayar',
        'metode_pembayaran_id',
        'metode_pembayaran_custom',
        'bukti_pembayaran',
        'status_pembayaran'
    ];
    
    protected $appends = ['bukti_pembayaran_url'];

    public function getBuktiPembayaranUrlAttribute()
    {
        if (!$this->bukti_pembayaran) return null;
        $path = str_replace('\\', '/', ltrim($this->bukti_pembayaran, '/'));
        return url('storage/' . $path);
    }

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranEvent::class, 'pendaftaran_id');
    }

    public function metodePembayaran()
    {
        return $this->belongsTo(MetodePembayaran::class, 'metode_pembayaran_id');
    }
}
