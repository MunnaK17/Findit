<?php

namespace App\Http\Requests;

use App\Services\MathCaptchaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClaimStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'pesan_klaim' => ['required', 'string', 'min:20', 'max:1000'],
            'captcha_answer' => ['required', 'integer', 'min:0', 'max:18'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $captchaService = app(MathCaptchaService::class);
            $answer = $this->input('captcha_answer');

            if ($answer === null || $answer === '') {
                $validator->errors()->add('captcha_answer', 'Jawaban captcha wajib diisi.');
                return;
            }

            if (!$captchaService->validate((int) $answer)) {
                $validator->errors()->add('captcha_answer', 'Jawaban captcha salah. Silakan coba lagi.');
            }
        });
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        // Regenerate captcha on validation failure
        $captchaService = app(MathCaptchaService::class);
        $captcha = $captchaService->generate();

        throw new HttpResponseException(
            back()
                ->withErrors($validator)
                ->withInput($this->except('captcha_answer'))
                ->with(['captcha_question' => $captcha['question']])
        );
    }
}
