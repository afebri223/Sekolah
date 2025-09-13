<?php
// app/Http/Requests/UpdateUserRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,kepala_sekolah,guru',
            'nip' => 'nullable|string|unique:users,nip,' . $this->user->id . '|max:20',
            'phone' => 'nullable|string|max:15',
            'is_wali_kelas' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role harus dipilih.',
            'role.in' => 'Role tidak valid.',
            'nip.unique' => 'NIP sudah digunakan.',
        ];
    }

    protected function prepareForValidation()
    {
        // Convert empty strings to null
        if ($this->nip === '') {
            $this->merge(['nip' => null]);
        }
        if ($this->phone === '') {
            $this->merge(['phone' => null]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate wali kelas constraints
            if ($this->is_wali_kelas && $this->role !== 'guru') {
                $validator->errors()->add('is_wali_kelas', 'Hanya guru yang dapat menjadi wali kelas.');
            }

            // Check if user is currently wali kelas and being changed
            if (!$this->is_wali_kelas && $this->user->waliKelas) {
                $validator->errors()->add('is_wali_kelas', 'User ini sedang menjadi wali kelas. Hapus dari kelas terlebih dahulu.');
            }
        });
    }
}