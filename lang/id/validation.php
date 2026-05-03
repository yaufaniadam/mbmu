<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines (Indonesian)
    |--------------------------------------------------------------------------
    | Override specific validation messages in Indonesian.
    */

    'accepted'             => 'Kolom :attribute harus diterima.',
    'active_url'           => 'Kolom :attribute bukan URL yang valid.',
    'after'                => 'Kolom :attribute harus berupa tanggal setelah :date.',
    'after_or_equal'       => 'Kolom :attribute harus berupa tanggal setelah atau sama dengan :date.',
    'alpha'                => 'Kolom :attribute hanya boleh berisi huruf.',
    'alpha_dash'           => 'Kolom :attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'alpha_num'            => 'Kolom :attribute hanya boleh berisi huruf dan angka.',
    'array'                => 'Kolom :attribute harus berupa array.',
    'before'               => 'Kolom :attribute harus berupa tanggal sebelum :date.',
    'before_or_equal'      => 'Kolom :attribute harus berupa tanggal sebelum atau sama dengan :date.',
    'between'              => [
        'numeric' => 'Kolom :attribute harus bernilai antara :min dan :max.',
        'file'    => 'Ukuran file :attribute harus antara :min dan :max kilobyte.',
        'string'  => 'Jumlah karakter :attribute harus antara :min dan :max.',
        'array'   => 'Jumlah anggota :attribute harus antara :min dan :max.',
    ],
    'boolean'              => 'Kolom :attribute harus bernilai true atau false.',
    'confirmed'            => 'Konfirmasi :attribute tidak cocok.',
    'date'                 => 'Kolom :attribute bukan tanggal yang valid.',
    'date_format'          => 'Kolom :attribute tidak sesuai format :format.',
    'different'            => 'Kolom :attribute dan :other harus berbeda.',
    'digits'               => 'Panjang angka kolom :attribute harus :digits digit.',
    'digits_between'       => 'Panjang angka kolom :attribute harus antara :min dan :max digit.',
    'dimensions'           => 'Dimensi gambar pada kolom :attribute tidak valid.',
    'distinct'             => 'Kolom :attribute memiliki nilai yang duplikat.',
    'email'                => 'Kolom :attribute harus berupa alamat email yang valid.',
    'exists'               => 'Kolom :attribute yang dipilih tidak valid.',
    'file'                 => 'Kolom :attribute harus berupa file.',
    'filled'               => 'Kolom :attribute tidak boleh kosong.',
    'gt'                   => [
        'numeric' => 'Kolom :attribute harus lebih besar dari :value.',
        'file'    => 'Ukuran file :attribute harus lebih besar dari :value kilobyte.',
        'string'  => 'Jumlah karakter :attribute harus lebih besar dari :value.',
        'array'   => 'Jumlah anggota :attribute harus lebih besar dari :value.',
    ],
    'gte'                  => [
        'numeric' => 'Kolom :attribute harus lebih besar dari atau sama dengan :value.',
        'file'    => 'Ukuran file :attribute harus lebih besar dari atau sama dengan :value kilobyte.',
        'string'  => 'Jumlah karakter :attribute harus lebih besar dari atau sama dengan :value.',
        'array'   => 'Jumlah anggota :attribute harus lebih besar dari atau sama dengan :value.',
    ],
    'image'                => 'Kolom :attribute harus berupa gambar.',
    'in'                   => 'Kolom :attribute yang dipilih tidak valid.',
    'in_array'             => 'Kolom :attribute tidak ditemukan di dalam :other.',
    'integer'              => 'Kolom :attribute harus berupa bilangan bulat.',
    'ip'                   => 'Kolom :attribute harus berupa alamat IP yang valid.',
    'ipv4'                 => 'Kolom :attribute harus berupa alamat IPv4 yang valid.',
    'ipv6'                 => 'Kolom :attribute harus berupa alamat IPv6 yang valid.',
    'json'                 => 'Kolom :attribute harus berupa JSON string yang valid.',
    'lt'                   => [
        'numeric' => 'Kolom :attribute harus kurang dari :value.',
        'file'    => 'Ukuran file :attribute harus kurang dari :value kilobyte.',
        'string'  => 'Jumlah karakter :attribute harus kurang dari :value.',
        'array'   => 'Jumlah anggota :attribute harus kurang dari :value.',
    ],
    'lte'                  => [
        'numeric' => 'Kolom :attribute harus kurang dari atau sama dengan :value.',
        'file'    => 'Ukuran file :attribute harus kurang dari atau sama dengan :value kilobyte.',
        'string'  => 'Jumlah karakter :attribute harus kurang dari atau sama dengan :value.',
        'array'   => 'Jumlah anggota :attribute harus kurang dari atau sama dengan :value.',
    ],
    'max'                  => [
        'numeric' => 'Kolom :attribute tidak boleh lebih dari :max.',
        'file'    => 'Ukuran file :attribute tidak boleh lebih dari :max kilobyte.',
        'string'  => 'Jumlah karakter :attribute tidak boleh lebih dari :max karakter.',
        'array'   => 'Jumlah anggota :attribute tidak boleh lebih dari :max.',
    ],
    'mimes'                => 'Kolom :attribute harus berupa file dengan tipe: :values.',
    'mimetypes'            => 'Kolom :attribute harus berupa file dengan tipe: :values.',
    'min'                  => [
        'numeric' => 'Kolom :attribute minimal bernilai :min.',
        'file'    => 'Ukuran file :attribute minimal :min kilobyte.',
        'string'  => 'Jumlah karakter :attribute minimal :min karakter.',
        'array'   => 'Jumlah anggota :attribute minimal :min.',
    ],
    'not_in'               => 'Kolom :attribute yang dipilih tidak valid.',
    'not_regex'            => 'Format kolom :attribute tidak valid.',
    'numeric'              => 'Kolom :attribute harus berupa angka.',
    'present'              => 'Kolom :attribute harus ada.',
    'regex'                => 'Format kolom :attribute tidak valid.',
    'required'             => 'Kolom :attribute wajib diisi.',
    'required_if'          => 'Kolom :attribute wajib diisi jika :other adalah :value.',
    'required_unless'      => 'Kolom :attribute wajib diisi kecuali :other bernilai :values.',
    'required_with'        => 'Kolom :attribute wajib diisi jika terdapat :values.',
    'required_with_all'    => 'Kolom :attribute wajib diisi jika terdapat :values.',
    'required_without'     => 'Kolom :attribute wajib diisi jika tidak terdapat :values.',
    'required_without_all' => 'Kolom :attribute wajib diisi jika tidak terdapat :values.',
    'same'                 => 'Kolom :attribute dan :other harus sama.',
    'size'                 => [
        'numeric' => 'Kolom :attribute harus bernilai :size.',
        'file'    => 'Ukuran file :attribute harus :size kilobyte.',
        'string'  => 'Jumlah karakter :attribute harus :size karakter.',
        'array'   => 'Jumlah anggota :attribute harus :size.',
    ],
    'starts_with'          => 'Kolom :attribute harus diawali dengan salah satu dari: :values.',
    'string'               => 'Kolom :attribute harus berupa string.',
    'timezone'             => 'Kolom :attribute harus berupa zona waktu yang valid.',
    'unique'               => ':attribute sudah digunakan.',
    'url'                  => 'Format :attribute tidak valid.',
    'uuid'                 => 'Kolom :attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | File Upload Specific
    |--------------------------------------------------------------------------
    | "uploaded" is triggered by Livewire when file upload validation fails
    | (e.g. max size exceeded). This replaces the generic "failed to upload".
    */
    'uploaded' => 'File :attribute gagal diproses. Pastikan ukuran file tidak melebihi batas yang ditentukan.',

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    'attributes' => [],
];
