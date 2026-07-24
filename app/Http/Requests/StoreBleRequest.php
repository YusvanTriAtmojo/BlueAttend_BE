<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBleRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            'uuid' => 'required|string|size:36|regex:/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/|unique:ble,uuid',
            'nama_device' => 'required|string|max:50|unique:ble,nama_device',
        ];
    }

    public function messages()
    {
        return [
            'uuid.required' => 'UUID wajib diisi.',
            'uuid.string' => 'UUID harus berupa teks.',
            'uuid.size' => 'UUID harus 36 karakter.',
            'uuid.regex' => 'Format UUID tidak valid (harus 8-4-4-4-12, hanya 0-9, A-F, dan tanda - tanpa spasi).',
            'uuid.unique' => 'UUID sudah terdaftar.',
            'nama_device.required' => 'Nama sesi wajib diisi.',
            'nama_device.string' => 'Nama device harus berupa teks.',
            'nama_device.max' => 'Nama device maksimal 50 karakter.',
            'nama_device.unique' => 'Nama Device sudah terdaftar.',
        ];
    }
}