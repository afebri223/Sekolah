<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'nis' => 'required|string|unique:students,nis,' . $this->student->id . '|max:20',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date|before:today',
            'birth_place' => 'required|string|max:255',
            'address' => 'required|string|max:1000',
            'phone' => 'nullable|string|max:15',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:15',
            'class_id' => 'required|exists:classes,id',
            'status' => 'required|in:active,inactive,graduated',
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required' => 'NIS harus diisi.',
            'nis.unique' => 'NIS sudah digunakan siswa lain.',
            'name.required' => 'Nama lengkap harus diisi.',
            'gender.required' => 'Jenis kelamin harus dipilih.',
            'birth_date.required' => 'Tanggal lahir harus diisi.',
            'birth_date.before' => 'Tanggal lahir tidak valid.',
            'birth_place.required' => 'Tempat lahir harus diisi.',
            'address.required' => 'Alamat harus diisi.',
            'parent_name.required' => 'Nama orang tua harus diisi.',
            'parent_phone.required' => 'Nomor HP orang tua harus diisi.',
            'class_id.required' => 'Kelas harus dipilih.',
            'class_id.exists' => 'Kelas yang dipilih tidak valid.',
            'status.required' => 'Status siswa harus dipilih.',
        ];
    }
}