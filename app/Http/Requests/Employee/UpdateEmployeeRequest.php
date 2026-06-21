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
            'hire_date' => 'sometimes|date',
        ];
    }

    public function messages(): array
    {
        return [
            'hire_date.date' => 'Hire date must be a valid date',
        ];
    }
}
