<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Presensi; 
use App\Models\BLE; 
use App\Models\Event; 

class Sesi extends Model
{
    use HasFactory;
    protected $table = 'sesi';
    public $timestamps = false;
    protected $fillable = [
        'id_event',
        'id_ble',
    ];
}
