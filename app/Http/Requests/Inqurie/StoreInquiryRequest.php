<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Property; 

class StoreInquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'property_id' => [
                'required', 
                'exists:properties,id',
               
                function ($attribute, $value, $fail) {
                    $property = Property::find($value);
                    if ($property && $property->status !== 'available') {
                        $fail('The selected property is not available for inquiry.');
                    }
                },
            ],
            'message' => 'nullable|string|max:1000',
        ];
    }
}