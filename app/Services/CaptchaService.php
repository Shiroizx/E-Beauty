<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CaptchaService
{
    public function recaptchaEnabled(): bool
    {
        $site = config('security.recaptcha.site_key');
        $secret = config('security.recaptcha.secret_key');

        return is_string($site) && $site !== ''
            && is_string($secret) && $secret !== '';
    }

    /**
     * Data untuk ditampilkan di view (reCAPTCHA atau soal matematika).
     *
     * @return array{mode: string, site_key?: string, question?: string, form: string}
     */
    public function challengeForForm(string $form): array
    {
        if ($this->recaptchaEnabled()) {
            return [
                'mode'     => 'recaptcha',
                'site_key' => config('security.recaptcha.site_key'),
                'form'     => $form,
            ];
        }

        $a = random_int(2, 12);
        $b = random_int(2, 12);
        session(['auth_captcha_'.$form => $a + $b]);

        return [
            'mode'     => 'math',
            'question' => "{$a} + {$b}",
            'form'     => $form,
        ];
    }

    /**
     * Validasi CAPTCHA untuk formulir auth (reCAPTCHA atau jawaban matematika).
     *
     * @throws ValidationException
     */
    public function validateRequest(Request $request, string $form): void
    {
        $this->rejectHoneypot($request);

        if ($this->recaptchaEnabled()) {
            $token = (string) $request->input('g-recaptcha-response', '');
            if ($token === '' || ! $this->verifyRecaptcha($token, $request->ip())) {
                throw ValidationException::withMessages([
                    'captcha' => 'Verifikasi CAPTCHA gagal atau kedaluwarsa. Silakan coba lagi.',
                ]);
            }

            return;
        }

        $request->validate(
            ['captcha_answer' => ['required', 'integer']],
            ['captcha_answer.required' => 'Jawaban verifikasi keamanan wajib diisi.']
        );

        $key = 'auth_captcha_'.$form;
        $expected = session($key);

        if ($expected === null || (int) $request->input('captcha_answer') !== (int) $expected) {
            throw ValidationException::withMessages([
                'captcha' => 'Jawaban verifikasi keamanan salah. Silakan hitung ulang.',
            ]);
        }

        session()->forget($key);
    }

    protected function verifyRecaptcha(string $token, ?string $ip): bool
    {
        try {
            $response = Http::asForm()->timeout(10)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('security.recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => $ip,
            ]);

            if (! $response->successful()) {
                return false;
            }

            $data = $response->json();

            return ($data['success'] ?? false) === true;
        } catch (\Throwable $e) {
            Log::warning('reCAPTCHA verify failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Lapisan tambahan anti-bot: field tersembunyi yang tidak boleh diisi.
     */
    protected function rejectHoneypot(Request $request): void
    {
        if (filled($request->input('bottrap'))) {
            Log::notice('Auth honeypot triggered', ['ip' => $request->ip()]);

            throw ValidationException::withMessages([
                'captcha' => 'Permintaan tidak dapat diproses. Jika Anda manusia, kosongkan field tersembunyi (jangan gunakan autofill pada form ini).',
            ]);
        }
    }
}
