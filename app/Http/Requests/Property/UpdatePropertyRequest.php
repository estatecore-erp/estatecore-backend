<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin' ||
            $this->user()->role === 'agent';
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:sale,rent',
            'status' => 'sometimes|in:available,sold,rented',
            'price' => 'sometimes|numeric|min:0',
            'location' => 'sometimes|string',
            'image_path' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'Type must be sale or rent',
            'status.in' => 'Status must be available, sold or rented',
            'price.numeric' => 'Price must be a number',
        ];
    }
}
