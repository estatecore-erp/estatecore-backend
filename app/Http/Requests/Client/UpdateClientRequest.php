<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'address' => 'sometimes|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'address.string' => 'Address must be a string',
            'address.max' => 'Address cannot exceed 255 characters',
        ];
    }
}
