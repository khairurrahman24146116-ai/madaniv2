@php
    $identity = config('school.identity');
    $accent = $identity['accent_color'] ?? '#1a5f2a';
    $logoPath = isset($identity['logo_path']) ? public_path($identity['logo_path']) : '';
@endphp
<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td style="width: 100px;">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" style="width: 85px; height: 85px;">
            @endif
        </td>
        <td style="text-align: center; vertical-align: middle;">
            <div style="font-size: 20px; font-weight: bold; color: {{ $accent }};">{{ $identity['nama_yayasan'] }}</div>
            <div style="font-size: 13px; font-weight: bold; color: #4d4841;">{{ $identity['nama_sekolah'] }}</div>
            <div style="font-size: 10px; color: #7d786f;">{{ $identity['alamat'] }}</div>
        </td>
    </tr>
</table>
<div style="border-bottom: 3px solid {{ $accent }}; margin: 8px 0 20px 0;"></div>