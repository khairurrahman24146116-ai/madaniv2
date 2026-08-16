<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $letter->title }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12pt; color: #1c1b18; line-height: 1.6; margin: 2cm; }
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
    @include('pdf.partials.kop-yayasan')

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
        <div class="name">{{ config('school.identity.kepala_sekolah') }}</div>
    </div>

    <div class="footer">
        Dokumen ini diproses secara elektronik melalui Sistem Informasi Manajemen SMA Dayah Madani Al-Aziziyah<br>
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
