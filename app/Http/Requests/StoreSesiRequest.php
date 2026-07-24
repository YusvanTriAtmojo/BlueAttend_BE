<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSesiRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            'id_ble' => 'required|exists:ble,id',
            'id_event' => 'required|exists:event,id',
        ];
    }

    public function messages()
    {
        return [
            'id_ble.required' => 'UUID wajib dipilih.',
            'id_ble.exists' => 'UUID yang dipilih tidak valid.',

            'id_event.required' => 'Event wajib dipilih.',
            'id_event.exists' => 'Event yang dipilih tidak valid.',
        ];
    }
}