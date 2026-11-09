<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // email sengaja tidak ada: email login dikelola Supabase Auth.
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^(\+62|62|0)8[0-9]{8,13}$/'],
            'email_notifications' => ['required', 'boolean'],
            'id_campus' => ['nullable', 'exists:campuses,id'],
            'prodi_unit' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Nomor telepon harus nomor Indonesia yang valid, contoh: 081234567890 atau 6281234567890.',
        ];
    }
}
