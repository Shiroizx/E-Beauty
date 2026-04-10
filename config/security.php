<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA v2 ("I'm not a robot")
    |--------------------------------------------------------------------------
    |
    | Jika RECAPTCHA_SITE_KEY dan RECAPTCHA_SECRET_KEY diisi, formulir auth
    | memakai widget reCAPTCHA. Kosongkan untuk mode demo: tantangan matematika.
    |
    | Daftar kunci: https://www.google.com/recaptcha/admin
    |
    */

    'recaptcha' => [
        'site_key'   => env('RECAPTCHA_SITE_KEY', ''),
        'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),
    ],

];
