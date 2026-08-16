<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Rapor {{ $student->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11pt; color: #1c1b18; line-height: 1.5; }
        .stamp { text-align: center; margin: 15px 0; }
        .stamp span { display: inline-block; padding: 8px 20px; border: 2px solid #1a5f2a; color: #1a5f2a; font-weight: bold; font-size: 10pt; letter-spacing: 0.15em; }
        .rapor-title { text-align: center; margin: 5px 0 20px 0; }
        .rapor-title h2 { margin: 0; font-size: 15pt; font-weight: bold; text-decoration: underline; }
        .rapor-title p { margin: 3px 0; font-size: 10.5pt; color: #4d4841; }
        .info-table { width: 100%; margin-bottom: 20px; font-size: 10pt; }
        .info-table td { padding: 3px 5px; }
        .info-table td:first-child { width: 140px; font-weight: bold; color: #4d4841; }
        table.subjects { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 9.5pt; }
        table.subjects th { background: #1a5f2a; color: #fff; padding: 8px 5px; text-align: center; font-size: 9pt; }
        table.subjects td { padding: 6px 5px; border-bottom: 1px solid #dcd6cd; text-align: center; }
        table.subjects tr:nth-child(even) { background: #f0ece6; }
        table.subjects .subject-name { text-align: left; }
        .grade-pass { color: #1a7a3a; font-weight: bold; }
        .grade-fail { color: #ba1a1a; font-weight: bold; }
        .grade-null { color: #bcb5aa; }
        .summary { margin-top: 20px; padding: 15px; background: #f0ece6; border-radius: 5px; font-size: 10pt; border-left: 4px solid #1a5f2a; }
        .summary table { width: 100%; }
        .summary td { padding: 3px 10px; }
        .summary td:first-child { font-weight: bold; width: 200px; color: #4d4841; }
        .footer { text-align: center; margin-top: 30px; font-size: 9pt; color: #7d786f; border-top: 1px solid #dcd6cd; padding-top: 10px; }
        .attendance-box { display: inline-block; margin: 5px 10px 0 0; padding: 5px 12px; border-radius: 3px; font-size: 9pt; }
        .attendance-h { background: #d5f5e3; color: #1e8449; }
        .attendance-s { background: #fdebd0; color: #935116; }
        .attendance-i { background: #d6eaf8; color: #1a5276; }
        .attendance-a { background: #fadbd8; color: #922b21; }
        .grade-number { font-size: 12pt; font-weight: bold; }
        .signature-area { margin-top: 30px; }
        .signature-table { width: 100%; }
        .signature-table td { width: 50%; text-align: center; padding: 5px; vertical-align: top; }
        .signature-table .role { font-size: 10pt; font-weight: bold; color: #4d4841; margin-bottom: 5px; }
        .signature-box { position: relative; display: inline-block; text-align: center; margin-top: 40px; }
        .signature-box .ttd { position: absolute; top: -46px; left: 50%; transform: translateX(-50%); width: 180px; }
        .signature-box .ttd img { width: 180px; height: 50px; object-fit: contain; }
        .signature-box .name { font-size: 11pt; font-weight: bold; border-top: 1px solid #1c1b18; padding-top: 2px; }
        .signature-box .nip { font-size: 9pt; color: #7d786f; }
        .verify-section { margin-top: 25px; padding: 12px; border: 1px dashed #7d786f; border-radius: 5px; text-align: center; font-size: 8.5pt; color: #7d786f; }
        .verify-section strong { color: #1a5f2a; }
        .verify-code { font-family: 'DejaVu Sans Mono', monospace; font-size: 9pt; letter-spacing: 0.1em; color: #1a5f2a; font-weight: bold; margin: 5px 0; }
        .verify-qr img { width: 70px; height: 70px; margin: 6px auto; }
    </style>
</head>
<body>
    @include('pdf.partials.kop-yayasan')

    <div class="stamp">
        <span>DOKUMEN RESMI</span>
    </div>

    <div class="rapor-title">
        <h2>LAPORAN HASIL BELAJAR SISWA</h2>
        <p>Semester {{ ucfirst($semester) }} &mdash; Tahun Ajaran {{ $academic_year }}</p>
    </div>

    <table class="info-table">
        <tr><td>Nama Santri</td><td>: <strong>{{ $student->name }}</strong></td></tr>
        <tr><td>NIS</td><td>: {{ $student->nis }}</td></tr>
        <tr><td>Kelas</td><td>: {{ $classroom->name }} ({{ $classroom->grade }})</td></tr>
        <tr><td>Wali Kelas</td><td>: {{ $classroom->waliKelas->name ?? '-' }}</td></tr>
        <tr><td>Semester</td><td>: {{ ucfirst($semester) }}</td></tr>
        <tr><td>Tahun Ajaran</td><td>: {{ $academic_year }}</td></tr>
    </table>

    <table class="subjects">
        <thead>
            <tr>
                <th>No</th>
                <th class="subject-name">Mata Pelajaran</th>
                <th>Rata Tugas</th>
                <th>Rata PH</th>
                <th>UTS</th>
                <th>UAS</th>
                <th>NA</th>
                <th>Predikat</th>
                <th>Ket</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $idx => $subject)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td class="subject-name">{{ $subject['subject_name'] }} ({{ $subject['subject_code'] }})</td>
                @if($subject['components'])
                    <td>{{ $subject['components']['tugas']['average_score'] ?? '-' }}</td>
                    <td>{{ $subject['components']['ph']['average_score'] ?? '-' }}</td>
                    <td>{{ $subject['components']['uts']['average_score'] ?? '-' }}</td>
                    <td>{{ $subject['components']['uas']['average_score'] ?? '-' }}</td>
                    <td class="grade-number {{ $subject['passed'] === true ? 'grade-pass' : ($subject['passed'] === false ? 'grade-fail' : 'grade-null') }}">
                        {{ $subject['final_grade'] ?? '-' }}
                    </td>
                    <td class="{{ $subject['passed'] === false ? 'grade-fail' : 'grade-pass' }}">
                        {{ $subject['final_grade'] !== null ? \App\Services\PredikatHelper::dariNilai($subject['final_grade']) : '-' }}
                    </td>
                    <td>
                        @if($subject['passed'] === true)
                            <span class="grade-pass">LULUS</span>
                        @elseif($subject['passed'] === false)
                            <span class="grade-fail">TIDAK</span>
                        @else
                            <span class="grade-null">-</span>
                        @endif
                    </td>
                @else
                    <td colspan="7" class="grade-null">Belum ada nilai</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr><td>Rata-Rata Keseluruhan (NA)</td><td>: <span class="grade-number {{ $overall_average !== null && $overall_average >= 75 ? 'grade-pass' : 'grade-fail' }}">{{ $overall_average ?? 'Belum tersedia' }}</span></td></tr>
            <tr><td>Jumlah Mapel</td><td>: {{ $subjectCount }}</td></tr>
            <tr><td>Status Kelulusan</td>
                <td>:
                    @php
                        $allPassed = collect($subjects)->every(fn($s) => $s['passed'] !== false);
                    @endphp
                    @if($allPassed && $subjectCount > 0)
                        <span class="grade-pass">LULUS SEMUA</span>
                    @elseif($subjectCount > 0)
                        <span class="grade-fail">BELUM LULUS SEMUA</span>
                    @else
                        <span class="grade-null">-</span>
                    @endif
                </td>
            </tr>
            <tr><td>Ringkasan Absensi</td>
                <td>:
                    <span class="attendance-box attendance-h">Hadir: {{ $attendance['H'] }}</span>
                    <span class="attendance-box attendance-s">Sakit: {{ $attendance['S'] }}</span>
                    <span class="attendance-box attendance-i">Izin: {{ $attendance['I'] }}</span>
                    <span class="attendance-box attendance-a">Alpa: {{ $attendance['A'] }}</span>
                    <span style="font-size:9pt;color:#7d786f;">Total: {{ $attendance['total'] }} hari</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="signature-area">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="role">Mengetahui,</div>
                    <div class="role">Wali Kelas</div>
                    <div class="signature-box">
                        @if($ttdWaliKelas ?? null)
                            <span class="ttd"><img src="{{ public_path('storage/'.$ttdWaliKelas->file_path) }}" alt="TTD"></span>
                        @endif
                        <div class="name">{{ $classroom->waliKelas->name ?? '-' }}</div>
                        <div class="nip">{{ $classroom->waliKelas?->role ? 'Guru' : '' }}</div>
                    </div>
                </td>
                <td>
                    <div class="role">Aceh Besar, {{ now()->locale('id')->isoFormat('D MMMM Y') }}</div>
                    <div class="role">Kepala Sekolah</div>
                    <div class="signature-box">
                        @if($ttdKepsek ?? null)
                            <span class="ttd"><img src="{{ public_path('storage/'.$ttdKepsek->file_path) }}" alt="TTD"></span>
                        @endif
                        <div class="name">{{ config('school.identity.kepala_sekolah') }}</div>
                        <div class="nip">{{ config('school.identity.kepala_sekolah_nip') ? 'NIP. '.config('school.identity.kepala_sekolah_nip') : '' }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="verify-section">
        <strong>&#10003; TANDA TANGAN DIGITAL</strong><br>
        Dokumen ini telah diproses secara elektronik melalui <strong>{{ config('school.identity.nama_sekolah') }}</strong>.<br>
        Kode Verifikasi: <div class="verify-code">{{ $verification_code }}</div>
        @if($verifyUrl ?? null)
            <div class="verify-qr">
                <img src="{{ app(\App\Services\QrCodeService::class)->dataUri($verifyUrl, 70) }}" alt="QR Verifikasi">
            </div>
            <em>Scan QR untuk verifikasi keaslian rapor</em>
        @else
            <em>Verifikasi keaslian dokumen melalui sistem informasi sekolah.</em>
        @endif
    </div>

    <div class="footer">
        Dicetak pada: {{ $generated_at }}<br>
        <em>Dokumen ini sah dan diproses secara elektronik melalui {{ config('school.identity.nama_sekolah') }}</em>
    </div>
</body>
</html>