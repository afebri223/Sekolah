<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:classes,name,' . $this->class->id,
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

    protected function prepareForValidation()
    {
        // Additional validation: check if capacity is not less than current students
        if ($this->capacity && $this->class) {
            $currentStudents = $this->class->students()->where('status', 'active')->count();
            if ($this->capacity < $currentStudents) {
                $this->merge([
                    'capacity_error' => "Kapasitas tidak boleh kurang dari jumlah siswa aktif saat ini ({$currentStudents})"
                ]);
            }
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('capacity_error')) {
                $validator->errors()->add('capacity', $this->capacity_error);
            }

            // Validate wali kelas availability
            if ($this->wali_kelas_id && $this->wali_kelas_id != $this->class->wali_kelas_id) {
                $user = \App\Models\User::find($this->wali_kelas_id);
                if ($user && !$user->is_wali_kelas) {
                    $validator->errors()->add('wali_kelas_id', 'User yang dipilih bukan wali kelas.');
                }
                
                if ($user && $user->waliKelas && $user->waliKelas->id != $this->class->id) {
                    $validator->errors()->add('wali_kelas_id', 'User sudah menjadi wali kelas di kelas lain.');
                }
            }
        });
    }
}