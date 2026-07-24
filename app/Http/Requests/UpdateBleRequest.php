<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBleRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        $id = $this->route('id'); 

        return [
            'uuid' => 'sometimes|string|size:36|regex:/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/|unique:ble,uuid,' . $id . ',id',
            'nama_device' => 'sometimes|string|max:50|unique:ble,nama_device,' . $id . ',id',
        ];
    }

    public function messages()
    {
        return [
            'uuid.string' => 'UUID harus berupa teks.',
            'uuid.size' => 'UUID harus 36 karakter.',
            'uuid.regex' => 'Format UUID tidak valid (harus 8-4-4-4-12, hanya 0-9, A-F, dan tanda - tanpa spasi).',
            'uuid.unique' => 'UUID sudah terdaftar.',
            'nama_device.string' => 'Nama device harus berupa teks.',
            'nama_device.max' => 'Nama device maksimal 50 karakter.',
            'nama_device.unique' => 'Nama Device sudah terdaftar.',
        ];
    }
}