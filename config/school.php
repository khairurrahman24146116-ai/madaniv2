<?php

return [

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
