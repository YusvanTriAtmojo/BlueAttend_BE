<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateByUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function getProfile(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'message' => 'Data user berhasil diambil',
            'status_code' => 200,
            'data' => [
                'id'       => $user->id,
                'id_role'  => $user->id_role,
                'role'     => $user->role->nama_role,
                'id_project' => $user->id_project,
                'project'  => $user->project ? $user->project->nama_project : null,
                'nip'      => $user->nip,
                'nama'     => $user->nama,
                'notlp'    => $user->notlp,
                'alamat'   => $user->alamat,
                'email'    => $user->email,
                'foto_profile'=> $item->foto_profile ? asset('storage/' . $item->foto_profile) : null,
            ],
        ]);
    }
}
