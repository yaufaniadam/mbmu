<?php

return [

    'title' => 'Masuk',

    'heading' => 'Masuk ke akun Anda',

    'actions' => [

        'register' => [
            'before' => 'atau',
            'label' => 'daftar akun baru',
        ],

        'request_password_reset' => [
            'label' => 'Lupa kata sandi?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Alamat Email',
        ],

        'password' => [
            'label' => 'Kata Sandi',
        ],

        'remember' => [
            'label' => 'Ingat saya',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Masuk',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Kredensial ini tidak cocok dengan data kami.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Terlalu banyak percobaan masuk',
            'body' => 'Silakan coba lagi dalam :seconds detik.',
        ],

    ],

];
