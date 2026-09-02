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
        ];
    }

    public function messages()
    {
        return [
            'id_user.required' => 'ID User wajib diisi.',
            'id_user.exists'   => 'User tidak ditemukan.',
        ];
    }
}