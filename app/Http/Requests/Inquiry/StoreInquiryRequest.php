<?php

namespace App\Http\Requests\Inquiry;

use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;

class StoreInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'client';
    }

    public function rules(): array
    {
        return [
            'property_id' => [
                'required',
                'exists:properties,id',
                function ($_, $value, $fail) {
                    $property = Property::query()->find($value);
                    if ($property && $property->status !== 'available') {
                        $fail('This property is not available for inquiry.');
                    }
                },
            ],
            'message' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'property_id.required' => 'Property is required',
            'property_id.exists' => 'Property not found',
            'message.required' => 'Message is required',
            'message.string' => 'Message must be a string',
            'message.max' => 'Message cannot exceed 1000 characters',
        ];
    }
}
