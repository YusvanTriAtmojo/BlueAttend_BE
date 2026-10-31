<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ble;
use App\Models\Event;
use App\Models\Presensi;

class DashboardController extends Controller
{
    public function index()
    {
        $presensiTerbaru = Presensi::with([
            'user:id,nama',
            'event:id,nama',
        ])
            ->whereDate('created_at', today())
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'total_user' => User::count(),
            'total_ble' => Ble::count(),
            'total_event' => Event::count(),
            'total_presensi' => Presensi::count(),

            'presensi_terbaru' => $presensiTerbaru,
        ]);
    }
}