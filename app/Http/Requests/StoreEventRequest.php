<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            'nama_event' => 'required|string|max:50|unique:event,nama_event',
            'waktu_mulai' => 'required|date',
            'tenggat_waktu' => 'required|date',
            'token' => 'required|string|max:10|min:6', 
            'area' => 'required|string|max:50', 
        ];
    }

    public function messages()
    {
        return [
            'nama_event.required' => 'Nama event wajib diisi.',
            'nama_event.string' => 'Nama event harus berupa teks.',
            'nama_event.max' => 'Nama event maksimal 50 karakter.',
            'nama_event.unique' => 'Nama event sudah terdaftar.',

            'waktu_mulai.required' => 'Waktu mulai wajib diisi.',
            'waktu_mulai.date' => 'Waktu mulai harus berupa tanggal yang valid.',

            'tenggat_waktu.required' => 'Tenggat waktu wajib diisi.',
            'tenggat_waktu.date' => 'Tenggat waktu harus berupa tanggal yang valid.',

            'token.required' => 'Token wajib diisi.',
            'token.string' => 'Token harus berupa teks.',
            'token.max' => 'Token harus maksimal 10 karakter.',
            'token.min' => 'Token harus minimal 6 karakter.',

            'area.required' => 'Area wajib diisi.',
            'area.string' => 'Area harus berupa teks.',
            'area.max' => 'Area harus maksimal 50 karakter.',
        ];
    }
}