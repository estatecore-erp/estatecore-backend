<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin' ||
            $this->user()->role === 'client';
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string',
            'address' => 'sometimes|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Name must be a string',
            'phone.string' => 'Phone must be a string',
            'address.string' => 'Address must be a string',
            'address.max' => 'Address cannot exceed 255 characters',
        ];
    }
}
