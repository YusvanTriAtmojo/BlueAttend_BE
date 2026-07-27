<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePresensiRequest;
use App\Models\Presensi;
use App\Models\Sesi;
use App\Models\Event;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class PresensiController extends Controller
{
    
    public function store(StorePresensiRequest $request)
    {
        try {
            $idUser = $request->id_user;
            $token = $request->token;
            $area = $request->area;
            $ble = (array) $request->service_uuid;
            $bleKey = $request->data;

            $event = Event::with('ble')
                ->where('token', $token)
                ->first();

            if (!$event) {
                return response()->json([
                    'message' => 'Event tidak ditemukan',
                    'status_code' => 404
                ], 404);
            }

            $bleValid = $event->ble->pluck('uuid')->toArray();

            if (!array_intersect($ble, $bleValid)) {
                return response()->json([
                    'message' => 'BLE tidak sesuai dengan event',
                    'status_code' => 400
                ], 400);
            }

            if (!$bleKey || !is_numeric($bleKey)) {
                return response()->json([
                    'message' => 'Kunci tidak valid',
                    'status_code' => 400
                ], 400);
            }
            
            $rssi = match ((string) $event->area) {
                'Sangat Dekat' => -60,
                'Dekat'        => -67,
                'Menengah'     => -72,
                'Cukup Jauh'   => -75,
                'Jauh'         => -80,
            };

            if ($area === null || $area < $rssi) {
                return response()->json([
                    'message' => 'Anda berada di luar area presensi',
                    'status_code' => 400
                ], 400);
            }

            $bleSeconds = $bleKey / 19;

            if ($bleSeconds <= 0) {
                return response()->json([
                    'message' => 'Kunci tidak valid',
                    'status_code' => 400
                ], 400);
            }

            $now = now();
            $serverSeconds = ($now->hour * 3600) +
                            ($now->minute * 60) +
                            $now->second;

            $selisih = abs($bleSeconds - $serverSeconds);

            if ($selisih > 60) {
                return response()->json([
                    'message' => "Terlalu lama dalam presensi",
                    'status_code' => 400
                ], 400);
            }

            if ($now->greaterThan($event->tenggat_waktu)) {
                return response()->json([
                    'message' => 'Presensi ditutup',
                    'status_code' => 400
                ], 400);
            }

            $sudahPresensi = Presensi::where('id_user', $idUser)
                ->where('id_event', $event->id)
                ->whereDate('created_at', today())
                ->where('status', 'hadir')
                ->exists();

            if ($sudahPresensi) {
                return response()->json([
                    'message' => 'Anda sudah presensi',
                    'status_code' => 400
                ], 400);
            }

            Presensi::create([
                'id_user' => $idUser,
                'id_event' => $event->id,
                'status' => 'hadir',
                'created_at' => now(),
            ]);

            return response()->json([
                'message' => 'Presensi berhasil',
                'status_code' => 201
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
}