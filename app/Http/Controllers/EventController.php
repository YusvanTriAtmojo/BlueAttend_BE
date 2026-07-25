<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\BLE;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Exception;

class EventController extends Controller
{
    public function index()
    {
        try {
            $data = Event::with('ble')->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada data event',
                    'status_code' => 200,
                    'data' => [],
                ], 200);
            }

            $formattedData = $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_event' => $item->nama_event,
                    'waktu_mulai' => $item->waktu_mulai,
                    'tenggat_waktu' => $item->tenggat_waktu,
                    'token' => $item->token,
                    'area' => $item->area,
                    'ble' => $item->ble->map(function ($ble) {
                        return [
                            'id' => $ble->id,
                            'uuid' => $ble->uuid,
                            'nama_device' => $ble->nama_device,
                        ];
                    }),
                ];
            });

            return response()->json([
                'message' => 'Data event berhasil diambil',
                'status_code' => 200,
                'data' => $formattedData,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function store(StoreEventRequest $request)
    {
        try {
            $event = Event::create([
                'nama_event' => $request->nama_event,
                'waktu_mulai' => $request->waktu_mulai,
                'tenggat_waktu' => $request->tenggat_waktu,
                'token' => $request->token,
                'area' => $request->area,
            ]);

            if ($request->has('ble')) {
                $ble = (array) $request->ble;
                $bleIds = BLE::whereIn('uuid', $ble)->pluck('id')->toArray();

                $event->ble()->attach($bleIds);
            }

            return response()->json([
                'message' => 'Event berhasil ditambahkan',
                'status_code' => 201,
                'data' => [
                    'id' => $event->id,
                    'nama_event' => $event->nama_event,
                    'waktu_mulai' => $event->waktu_mulai,
                    'tenggat_waktu' => $event->tenggat_waktu,
                    'token' => $event->token,
                    'area' => $event->area,
                ]
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function update(UpdateEventRequest $request, $id)
    {
        try {
            $event = Event::find($id);

            if (!$event) {
                return response()->json([
                    'message' => 'Event tidak ditemukan',
                    'status_code' => 404,
                ], 404);
            }

            $event->update($request->validated());

            if ($request->has('ble')) {
                $ble = (array) $request->ble;
                $bleIds = BLE::whereIn('uuid', $ble)->pluck('id')->toArray();

                $event->ble()->detach();
                $event->ble()->attach($bleIds);
            }

            return response()->json([
                'message' => 'Event berhasil diubah',
                'status_code' => 200,
                'data' => [
                    'id' => $event->id,
                    'nama_event' => $event->nama_event,
                    'waktu_mulai' => $event->waktu_mulai,
                    'tenggat_waktu' => $event->tenggat_waktu,
                    'token' => $event->token,
                    'area' => $event->area,
                    'ble' => $event->ble->pluck('uuid')
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        if ($event->presensi()->exists()) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Event tidak bisa dihapus karena telah digunakan pada presensi',
            ], 400);
        }
        $event->delete();

        return response()->json([
            'message' => 'Event berhasil dihapus',
            'status_code' => 200,
        ]);
    }
}