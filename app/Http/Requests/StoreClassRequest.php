<?php
// app/Http/Requests/StoreClassRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:classes',
            'grade' => 'required|string|max:10',
            'major' => 'nullable|string|max:50',
            'wali_kelas_id' => 'nullable|exists:users,id',
            'capacity' => 'required|integer|min:1|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kelas harus diisi.',
            'name.unique' => 'Nama kelas sudah ada.',
            'grade.required' => 'Tingkat harus dipilih.',
            'wali_kelas_id.exists' => 'Wali kelas yang dipilih tidak valid.',
            'capacity.required' => 'Kapasitas harus diisi.',
            'capacity.min' => 'Kapasitas minimal 1 siswa.',
            'capacity.max' => 'Kapasitas maksimal 50 siswa.',
        ];
    }
}