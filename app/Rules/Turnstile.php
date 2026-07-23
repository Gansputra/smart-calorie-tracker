<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Turnstile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secretKey = config('services.turnstile.secret_key');

        if (!$secretKey) {
            // If secret key is not set (e.g. local dev), allow bypassing
            return;
        }

        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret'   => $secretKey,
            'response' => $value,
        ]);

        if (!$response->successful() || !$response->json('success')) {
            $fail('Verifikasi "Saya bukan robot" gagal. Silakan coba lagi.');
        }
    }
}
