<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        $id = $this->route('id'); 

        return [
            'nama_event' => 'sometimes|string|max:50|unique:event,nama_event,' . $id . ',id',
            'waktu_mulai' => 'sometimes|date',
            'tenggat_waktu' => 'sometimes|date',
            'token' => 'sometimes|string|max:10|min:6',
            'area' => 'sometimes|string|max:50',
        ];
    }

    public function messages()
    {
        return [
            'nama_event.string' => 'Nama event harus berupa teks.',
            'nama_event.max' => 'Nama event maksimal 20 karakter.',
            'nama_event.unique' => 'Nama event sudah terdaftar.',
            'waktu_mulai.date' => 'Waktu mulai harus berupa tanggal yang valid.',

            'tenggat_waktu.date' => 'Tenggat waktu harus berupa tanggal yang valid.',

            'token.string' => 'Token harus berupa teks.',
            'token.max' => 'Token harus maksimal 10 karakter.',
            'token.min' => 'Token harus minimal 6 karakter.',

            'area.string' => 'Area harus berupa teks.',
            'area.max' => 'Area harus maksimal 50 karakter.',
        ];
    }
}