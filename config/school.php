<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identitas Sekolah & Yayasan
    |--------------------------------------------------------------------------
    |
    | Dipakai pada kop surat resmi dan rapor (PDF). Nama kepala sekolah dan
    | alamat bisa disesuaikan tanpa mengubah kode.
    |
    */

    'identity' => [
        'nama_yayasan' => 'YAYASAN DAYAH MADANI AL-AZIZIYAH',
        'nama_sekolah' => 'SMA Dayah Madani Al-Aziziyah',
        'alamat' => 'Jln. T. Imum Hamzah, Dusun Kutaran, Gampong Lampeuneurut Ujong Blang',
        'email' => 'info@madani.sch.id',
        'telepon' => '-',
        'kepala_sekolah' => 'Fahmi, S.Sos., MA',
        'kepala_sekolah_nip' => null,
        'logo_path' => 'images/logo-yayasan.png',
        'accent_color' => '#1a5f2a',
    ],

    /*
    |--------------------------------------------------------------------------
    | Informasi Rekening & QRIS Resmi
    |--------------------------------------------------------------------------
    |
    | Ditampilkan kepada wali murid di halaman Pembayaran SPP sebagai
    | tujuan transfer/scan. Sesuaikan nomor rekening dan atas nama sesuai
    | akun resmi madrasah.
    |
    */

    'payment_accounts' => [
        [
            'bank' => 'Bank Syariah Indonesia (BSI)',
            'account_number' => '7200 0000 1234',
            'account_name' => 'Yayasan Pendidikan Madani Al-Aziziyah',
            'type' => 'transfer',
        ],
        [
            'bank' => 'QRIS',
            'account_number' => 'QRIS — scan dari aplikasi e-wallet/bank',
            'account_name' => 'Madani Al-Aziziyah',
            'type' => 'qris',
        ],
    ],

    'payment_instructions' => 'Lakukan pembayaran SPP melalui rekening/QRIS resmi di atas, lalu hubungi bendahara sekolah untuk konfirmasi dan pencatatan kwitansi.',

];
