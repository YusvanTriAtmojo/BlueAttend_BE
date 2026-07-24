<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event; 

class BLE extends Model
{
    use HasFactory;
    protected $table = 'ble';
    public $timestamps = false; 
    protected $fillable = [
        'uuid',
        'nama_device',
    ];

    public function sesi()
    {
        return $this->belongsToMany(
            Event::class,
            'sesi',
            'id_ble',
            'id_event'
        );
    }
}
