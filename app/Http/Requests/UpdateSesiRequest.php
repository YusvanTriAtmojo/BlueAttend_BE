<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSesiRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            'id_event' => 'sometimes|exists:event,id',
            'id_ble' => 'sometimes|exists:ble,id',
        ];
    }

    public function messages()
    {
        return [

            'id_event.exists' => 'Event yang dipilih tidak valid.',

            'id_ble.exists' => 'BLE yang dipilih tidak valid.',
        ];
    }
}