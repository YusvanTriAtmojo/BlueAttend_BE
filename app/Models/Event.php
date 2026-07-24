<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BLE;
use App\Models\Presensi;


class Event extends Model
{
    use HasFactory;
    protected $table = 'event';
    public $timestamps = false; 
    protected $fillable = [
        'nama_event',
        'waktu_mulai',
        'tenggat_waktu',
        'token',
        'area',
    ];

    public function ble()
    {
        return $this->belongsToMany(BLE::class, 'sesi', 'id_event', 'id_ble');
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'id_event');
    }
    
}
