<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBleRequest;
use App\Http\Requests\UpdateBleRequest;
use App\Models\BLE;
use App\Models\Sesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Exception;

class BleController extends Controller
{
    public function index()
    {
        try {
            $data = BLE::all();

            if ($data->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada data BLE',
                    'status_code' => 200,
                    'data' => [],
                ], 200);
            }

            $formattedData = $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'uuid' => $item->uuid,
                    'nama_device' => $item->nama_device
                ];
            });

            return response()->json([
                'message' => 'Data BLE berhasil diambil',
                'status_code' => 200,
                'data' => $formattedData,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message'     => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function store(StoreBleRequest $request)
    {
        try {
            $ble = Ble::create([
                'uuid' => $request->uuid,
                'nama_device' => $request->nama_device,
            ]);

            return response()->json([
                'message' => 'BLE berhasil ditambahkan',
                'status_code' => 201,
                'data' => [
                    'id' => $ble->id,
                    'uuid' => $ble->uuid,
                    'nama_device' => $ble->nama_device
                ]
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message'     => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function update(UpdateBleRequest $request, $id)
    {
        try {
            $ble = Ble::find($id);

            if (!$ble) {
                return response()->json([
                    'message' => 'BLE tidak ditemukan',
                    'status_code' => 404,
                ], 404);
            }

            $ble->update($request->validated());

            return response()->json([
                'message' => 'BLE berhasil diubah',
                'status_code' => 200,
                'data' => [
                    'id' => $ble->id,
                    'uuid' => $ble->uuid,
                    'nama_device' => $ble->nama_device
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message'     => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $ble = BLE::find($id);

            if (!$ble) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'BLE tidak ditemukan',
                ], 404);
            }

            if ($ble->sesi()->exists()) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'BLE tidak bisa di hapus karena telah digunakan di presensi',
                ], 400);
            }

            $ble->delete();

            return response()->json([
                'message' => 'BLE berhasil dihapus',
                'status_code' => 200,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message'     => $e->getMessage(),
            ], 500);
        }
    }
}