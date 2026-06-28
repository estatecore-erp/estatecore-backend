<?php

namespace App\Http\Requests\Lease;

use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeaseRequest extends FormRequest
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
                    if ($property && $property->type !== 'rent') {
                        $fail('This property is not available for rent.');
                    }
                    if ($property && $property->status !== 'available') {
                        $fail('This property is not available.');
                    }
                },
            ],
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Client is required',
            'client_id.exists' => 'Client not found',
            'property_id.required' => 'Property is required',
            'property_id.exists' => 'Property not found',
            'start_date.required' => 'Start date is required',
            'start_date.after_or_equal' => 'Start date must be today or later',
            'end_date.required' => 'End date is required',
            'end_date.after' => 'End date must be after start date',
            'monthly_rent.required' => 'Monthly rent is required',
            'monthly_rent.numeric' => 'Monthly rent must be a number',
        ];
    }
}
