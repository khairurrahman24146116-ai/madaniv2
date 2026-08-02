<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Aktif - {{ $letter->student->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12pt; color: #1c1b18; line-height: 1.6; margin: 2cm; }
        .kop { text-align: center; border-bottom: 3px solid #2563eb; padding-bottom: 15px; margin-bottom: 25px; }
        .kop h1 { margin: 0; font-size: 16pt; color: #2563eb; }
        .kop h2 { margin: 5px 0; font-size: 13pt; color: #4d4841; }
        .kop p { margin: 2px 0; font-size: 10pt; color: #7d786f; }
        .title { text-align: center; font-size: 14pt; font-weight: bold; margin: 20px 0; text-decoration: underline; }
        .meta { font-size: 10pt; color: #7d786f; margin-bottom: 20px; }
        .content { text-align: justify; }
        .content table { margin: 15px 0; width: 100%; }
        .content table td { padding: 4px 8px; vertical-align: top; }
        .content table td:first-child { width: 140px; }
        .signature { margin-top: 50px; text-align: right; }
        .signature p { margin: 3px 0; }
        .signature .name { font-weight: bold; margin-top: 80px; }
        .footer { text-align: center; margin-top: 30px; font-size: 9pt; color: #7d786f; border-top: 1px solid #dcd6cd; padding-top: 10px; }
        .spp-status { font-size: 10pt; color: #4d4841; margin-top: 20px; padding: 10px; border: 1px solid #dcd6cd; }
    </style>
</head>
<body>
    <div class="kop">
        <h1>YAYASAN DAYAH MADANI AL-AZIZIYAH</h1>
        <h2>SMA FORMAL</h2>
        <p>Jl. ...... | Email: info@madani.sch.id</p>
    </div>

    <div class="title">SURAT KETERANGAN AKTIF</div>

    <div class="meta">
        Nomor: {{ $letter->letter_number }}<br>
        Lampiran: -<br>
        Perihal: Keterangan Aktif Siswa
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini, Kepala SMA Dayah Madani Al-Aziziyah menerangkan bahwa:</p>

        <table>
            <tr>
                <td>Nama</td>
                <td>: {{ $letter->student->name }}</td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>: {{ $letter->student->nis }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: {{ $letter->student->classroom?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>: {{ $letter->student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <td>Nama Orang Tua</td>
                <td>: {{ $letter->student->parent_name ?? '-' }}</td>
            </tr>
        </table>

        <p>Benar bahwa siswa tersebut di atas adalah <strong>siswa aktif</strong> pada SMA Dayah Madani Al-Aziziyah dan telah memenuhi kewajiban administrasi pendidikan.</p>

        <p>Surat keterangan ini dibuat untuk keperluan: <em>{{ $letter->purpose }}</em>.</p>

        <p>Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>

        <div class="spp-status">
            Status SPP per {{ now()->locale('id')->isoFormat('D MMMM Y') }}: 
            <strong>{{ $letter->spp_verified ? 'LUNAS' : 'BELUM LUNAS' }}</strong>
        </div>
    </div>

    <div class="signature">
        <p>{{ now()->locale('id')->isoFormat('D MMMM Y') }}</p>
        <p>Kepala Sekolah,</p>
        <div class="name">( ______________________ )</div>
    </div>

    <div class="footer">
        Dokumen ini diproses secara elektronik melalui Sistem Informasi Manajemen SMA Dayah Madani Al-Aziziyah<br>
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
