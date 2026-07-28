<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $letter->title }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12pt; color: #1c1b18; line-height: 1.6; margin: 2cm; }
        .kop { text-align: center; border-bottom: 3px solid #2563eb; padding-bottom: 15px; margin-bottom: 25px; }
        .kop h1 { margin: 0; font-size: 16pt; color: #2563eb; }
        .kop h2 { margin: 5px 0; font-size: 13pt; color: #4d4841; }
        .kop p { margin: 2px 0; font-size: 10pt; color: #7d786f; }
        .title { text-align: center; font-size: 14pt; font-weight: bold; margin: 20px 0; text-decoration: underline; }
        .meta { font-size: 10pt; color: #7d786f; margin-bottom: 20px; }
        .content { text-align: justify; }
        .signature { margin-top: 50px; text-align: right; }
        .signature p { margin: 3px 0; }
        .signature .name { font-weight: bold; margin-top: 60px; }
        .footer { text-align: center; margin-top: 30px; font-size: 9pt; color: #7d786f; border-top: 1px solid #dcd6cd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="kop">
        <h1>YAYASAN DAYAH MADANI AL-AZIZIYAH</h1>
        <h2>SMA FORMAL</h2>
        <p>Jl. ...... | Email: info@madani.sch.id</p>
    </div>

    <div class="title">{{ $letter->title }}</div>

    <div class="meta">
        Nomor: {{ $letter->id }} /SMA-MA/{{ $letter->created_at->format('Y') }}<br>
        Lampiran: -<br>
        Perihal: {{ $letter->title }}
    </div>

    <div class="content">
        {{ $letter->content }}
    </div>

    <div class="signature">
        <p>{{ $letter->created_at->locale('id')->isoFormat('D MMMM Y') }}</p>
        <p>Kepala Sekolah,</p>
        <div class="name">( ______________________ )</div>
    </div>

    <div class="footer">
        Dokumen ini diproses secara elektronik melalui Sistem Informasi Manajemen SMA Dayah Madani Al-Aziziyah<br>
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
