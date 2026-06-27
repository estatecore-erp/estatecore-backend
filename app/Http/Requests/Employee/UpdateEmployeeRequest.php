<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string'
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Name must be a string',
            'phone.string' => 'Phone must be a string',
        ];
    }
}
