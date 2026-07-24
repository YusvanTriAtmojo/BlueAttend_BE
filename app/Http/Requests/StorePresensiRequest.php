<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePresensiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_user' => 'required|integer|exists:users,id',
            'id_event' => 'required|integer|exists:event,id',
        ];
    }

    public function messages()
    {
        return [
            'id_user.required' => 'ID User wajib diisi.',
            'id_user.exists'   => 'User tidak ditemukan.',
            'id_event.required' => 'ID Event wajib diisi.',
            'id_event.exists'   => 'Event tidak ditemukan.',
        ];
    }
}