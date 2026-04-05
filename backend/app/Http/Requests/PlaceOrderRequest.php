<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Get valid payment method IDs from config
        $paymentMethodIds = array_keys(config('order.payment_methods', [1]));
        $paymentMethodIdsStr = implode(',', $paymentMethodIds);
        
        return [
            'address_id'     => ['required', 'integer', 'exists:addresses,id'],
            'payment_method' => ['required', 'string', 'in:' . implode(',', array_values(config('order.payment_methods', ['cod'])))],
        ];
    }

    public function messages(): array
    {
        return [
            'address_id.required'    => 'Please select a delivery address.',
            'address_id.exists'      => 'The selected address is invalid.',
            'payment_method.in'      => 'Invalid payment method selected.',
        ];
    }
}
