<?php

namespace App\Http\Requests\Sale;

use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()->role, ['admin', 'agent']);
    }

    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'property_id' => [
                'required',
                'exists:properties,id',
                function ($_, $value, $fail) {
                    $property = Property::query()->find($value);
                    if ($property && $property->type !== 'sale') {
                        $fail('This property is not available for sale.');
                    }
                    if ($property && $property->status !== 'available') {
                        $fail('This property is not available.');
                    }
                },
            ],
            'sale_price' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Client is required',
            'client_id.exists' => 'Client not found',
            'property_id.required' => 'Property is required',
            'property_id.exists' => 'Property not found',
            'sale_price.required' => 'Sale price is required',
            'sale_price.numeric' => 'Sale price must be a number',
            'sale_date.required' => 'Sale date is required',
            'sale_date.date' => 'Sale date must be a valid date',
        ];
    }
}
