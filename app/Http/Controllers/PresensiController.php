<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePresensiRequest;
use App\Models\Presensi;
use App\Models\Sesi;
use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

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

            // mencari event dari token
            $event = Event::with('ble')
                ->where('token', $token)
                ->first();

            if (!$event) {
                return response()->json([
                    'message' => 'Event tidak ditemukan',
                    'status_code' => 404,
                ], 404);
            }

            // memvalidasi BLE dari UUID
            $bleValid = $event->ble
                ->pluck('uuid')
                ->toArray();

            if (!array_intersect($ble, $bleValid)) {
                return response()->json([
                    'message' => 'BLE tidak sesuai dengan event',
                    'status_code' => 400,
                ], 400);
            }


            // validasi data BLE
            if (!$bleKey || !is_numeric($bleKey)) {
                return response()->json([
                    'message' => 'Kunci tidak valid',
                    'status_code' => 400,
                ], 400);
            }


            // validasi nilai RSSI dengan area
            $rssi = match ((string) $event->area) {
                'Sangat Dekat' => -60,
                'Dekat'       => -67,
                'Menengah'    => -72,
                'Cukup Jauh'  => -75,
                'Jauh'        => -80,
            };

            if ($rssi === null) {
                return response()->json([
                    'message' => 'Area event tidak valid',
                    'status_code' => 400,
                ], 400);
            }

            if ($area === null || $area < $rssi) {
                return response()->json([
                    'message' => 'Anda berada di luar area presensi',
                    'status_code' => 400,
                ], 400);
            }

            // validasi data waktu dari BLE
            $bleSeconds = $bleKey / 19;

            if ($bleSeconds <= 0) {
                return response()->json([
                    'message' => 'Kunci tidak valid',
                    'status_code' => 400,
                ], 400);
            }

            $now = now();
            $serverSeconds =
                ($now->hour * 3600) +
                ($now->minute * 60) +
                $now->second;
            $selisih = abs(
                $bleSeconds - $serverSeconds
            );

            if ($selisih > 60) {
                return response()->json([
                    'message' => 'Terlalu lama dalam presensi',
                    'status_code' => 400,
                ], 400);
            }

            // validasi waktu tenggat event
            if ($now->greaterThan($event->tenggat_waktu)) {
                return response()->json([
                    'message' => 'Presensi ditutup',
                    'status_code' => 400,
                ], 400);
            }

            // cek apakah sudah presensi atau belum
            $sudahPresensi = Presensi::where(
                    'id_user',
                    $idUser
                )
                ->where(
                    'id_event',
                    $event->id
                )
                ->whereDate(
                    'created_at',
                    today()
                )
                ->where(
                    'status',
                    'hadir'
                )
                ->exists();

            if ($sudahPresensi) {
                return response()->json([
                    'message' => 'Anda sudah presensi',
                    'status_code' => 400,
                ], 400);
            }

            // mencari user yang presensi
            $user = User::find($idUser);

            if (!$user) {
                return response()->json([
                    'message' => 'User tidak ditemukan',
                    'status_code' => 404,
                ], 404);
            }

            // validasi data face embedding
            if (!$user->face_embedding) {
                return response()->json([
                    'message' => 'Data wajah pengguna belum terdaftar',
                    'status_code' => 400,
                ], 400);
            }

            // mengambil data face embedding yang dikirim
            $faceEmbedding = $request->face_embedding;

            if (!$faceEmbedding) {
                return response()->json([
                    'message' => 'Face embedding wajib dikirim',
                    'status_code' => 400,
                ], 400);
            }

            if (is_string($faceEmbedding)) {
                $faceEmbedding = json_decode(
                    $faceEmbedding,
                    true
                );
            }

            if (!is_array($faceEmbedding)) {
                return response()->json([
                    'message' => 'Face embedding tidak valid',
                    'status_code' => 400,
                ], 400);
            }

            // validasi dimensi embedding
            if (count($faceEmbedding) !== 192) {
                return response()->json([
                    'message' => 'Dimensi face embedding tidak valid',
                    'status_code' => 400,
                    'data' => [
                        'dimension' => count($faceEmbedding),
                        'expected' => 192,
                    ],
                ], 400);
            }

            // mengambil data face embedding yang terdaftar di DB
            $storedEmbedding = json_decode(
                $user->face_embedding,
                true
            );

            if (!is_array($storedEmbedding)) {
                return response()->json([
                    'message' => 'Face embedding pengguna tidak valid',
                    'status_code' => 400,
                ], 400);
            }

            if (count($storedEmbedding) !== 192) {
                return response()->json([
                    'message' => 'Dimensi face embedding pengguna tidak valid',
                    'status_code' => 400,
                    'data' => [
                        'dimension' => count($storedEmbedding),
                        'expected' => 192,
                    ],
                ], 400);
            }


            // cek kemiripan face embedding
            $dotProduct = 0.0;
            $normNew = 0.0;
            $normOld = 0.0;

            for ($i = 0; $i < 192; $i++) {

                $newValue = (float) $faceEmbedding[$i];
                $oldValue = (float) $storedEmbedding[$i];

                $dotProduct +=
                    $newValue * $oldValue;

                $normNew +=
                    $newValue * $newValue;

                $normOld +=
                    $oldValue * $oldValue;
            }

            $normNew = sqrt($normNew);
            $normOld = sqrt($normOld);

            if ($normNew == 0 || $normOld == 0) {
                return response()->json([
                    'message' => 'Embedding wajah tidak valid',
                    'status_code' => 400,
                ], 400);
            }

            $similarity =
                $dotProduct /
                ($normNew * $normOld);


            // Batas kemiripan
            $threshold = 0.60;

            if ($similarity < $threshold) {
                return response()->json([
                    'message' => 'Wajah tidak sesuai',
                    'status_code' => 400,
                    'data' => [
                        'similarity' => round(
                            $similarity,
                            4
                        ),
                        'threshold' => $threshold,
                    ],
                ], 400);
            }

            // simpan presensi
            Presensi::create([
                'id_user' => $idUser,
                'id_event' => $event->id,
                'status' => 'hadir',
                'created_at' => now(),
            ]);

            return response()->json([
                'message' => 'Presensi berhasil',
                'status_code' => 201,
                'data' => [
                    'similarity' => round(
                        $similarity,
                        4
                    ),
                    'threshold' => $threshold,
                ],
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status_code' => 500,
                'data' => null,
            ], 500);
        }
    }

    public function riwayatPresensi(Request $request)
    {
        try {
            $userId = auth()->id();

            $tigaBulanLalu = Carbon::now()->subMonths(3);

            $presensi = Presensi::where('id_user', $userId)
                ->where('created_at', '>=', $tigaBulanLalu) 
                ->with(['event:id,nama_event'])
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $presensi->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_event' => $item->event->nama_event ?? '-', 
                    'tanggal' => $item->created_at->format('Y-m-d H:i:s')
                ];
            });

            return response()->json([
                'message' => $data->isEmpty()
                    ? 'Tidak ada riwayat presensi'
                    : 'Riwayat presensi berhasil diambil',
                'status_code' => 200,
                'data' => $data,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status_code' => 500,
                'data' => null,
            ], 500);
        }
    }
}