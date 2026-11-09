<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class MathCaptchaService
{
    private const SESSION_KEY = 'math_captcha';

    /**
     * Generate a new math captcha with numbers 1-9 only
     * Returns ['question' => '8 + 7 = ?', 'answer' => 15]
     */
    public function generate(): array
    {
        $num1 = random_int(1, 9);
        $num2 = random_int(1, 9);
        $operator = '+';

        $answer = $num1 + $num2;

        $question = "{$num1} + {$num2} = ?";

        // Store in session
        Session::put(self::SESSION_KEY, [
            'answer' => $answer,
            'expires' => now()->addMinutes(10)->timestamp,
        ]);

        return [
            'question' => $question,
            'answer' => $answer, // Only for testing, remove in production
        ];
    }

    /**
     * Validate the user's answer
     */
    public function validate(int $userAnswer): bool
    {
        $captcha = Session::get(self::SESSION_KEY);

        if (!$captcha) {
            return false;
        }

        // Check if expired
        if (now()->timestamp > $captcha['expires']) {
            Session::forget(self::SESSION_KEY);
            return false;
        }

        // Check answer
        $isValid = (int) $userAnswer === (int) $captcha['answer'];

        // Regenerate after one use
        Session::forget(self::SESSION_KEY);

        return $isValid;
    }

    /**
     * Get the current question (for display)
     */
    public function getQuestion(): ?string
    {
        $captcha = Session::get(self::SESSION_KEY);

        if (!$captcha || now()->timestamp > $captcha['expires']) {
            return null;
        }

        // Extract question from session if stored, or regenerate
        return null; // Will trigger new captcha in view
    }

    /**
     * Check if captcha exists in session
     */
    public function exists(): bool
    {
        return Session::has(self::SESSION_KEY);
    }

    /**
     * Clear the captcha from session
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }
}
