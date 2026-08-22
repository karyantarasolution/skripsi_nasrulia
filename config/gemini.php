<?php

return [
    'api_key' => env('GEMINI_API_KEY', ''),

    // Model Gemini yang digunakan (bisa diganti sesuai kebutuhan)
    'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),

    'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
];
