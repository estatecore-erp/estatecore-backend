<?php

namespace App\Services;

use App\Models\Employee;

class EmployeeService
{
    public function getAll()
    {
        return Employee::with('user')->latest()->get();
    }

    public function getById(int $id)
    {
        return Employee::with('user')->findOrFail($id);
    }

    public function updateEmployee(int $id, array $data): Employee
    {
        $employee = Employee::findOrFail($id);
        $user = $employee->user;

        // update users table
        $user->update([
            'name'  => $request->name  ?? $user->name,
            'phone' => $request->phone ?? $user->phone,
        ]);

        return $employee->fresh('user');
    }

    public function deleteEmployee(int $id): void
    {
        Employee::findOrFail($id)->delete();
    }
}
