<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin' ||
            $this->user()->role === 'agent';
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:sale,rent',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string',
            'agent_id' => 'sometimes|exists:users,id',
            'image' => 'nullable|image|max:2048',
        ];

        if ($this->user()->role === 'admin') {
            $rules['agent_id'] = 'required|exists:users,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Property title is required',
            'type.in' => 'Type must be sale or rent',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'location.required' => 'Location is required',
            'agent_id.required' => 'Agent is required',
            'agent_id.exists' => 'Selected agent does not exist',
        ];
    }
}
