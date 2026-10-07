@php
    function selisih($jam_masuk, $jam_keluar) {
        list($h, $m, $s) = explode(":", $jam_masuk);
        $dtAwal = mktime($h, $m, $s, "1", "1", "1");
        list($h, $m, $s) = explode(":", $jam_keluar);
        $dtAkhir = mktime($h, $m, $s, "1", "1", "1");
        $dtSelisih = $dtAkhir - $dtAwal;
        $totalmenit = $dtSelisih / 60;
        $jam = explode(".", $totalmenit / 60);
        $sisamenit = ($totalmenit / 60) - $jam[0];
        $menit = floor($sisamenit * 60);
        return $jam[0] . " Jam " . $menit . " Menit";
    }
@endphp

@forelse ($presensi as $d)
    @php
        $foto_in = Storage::url('uploads/absensi/' . $d->foto_in);
        $foto_out = !empty($d->foto_out) ? Storage::url('uploads/absensi/' . $d->foto_out) : null;
    @endphp
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $d->nik }}</td>
        <td>{{ $d->nama_lengkap }}</td>
        <td>{{ $d->jabatan }}</td>
        <td>{{ $d->jam_in }}</td>
        <td>
            @if(!empty($d->foto_in))
                <img src="{{ url($foto_in) }}" class="avatar" alt="Foto Masuk">
            @else
                <span class="badge bg-secondary-lt">No Photo</span>
            @endif
        </td>
        <td>{!! $d->jam_out != null ? $d->jam_out : '<span class="badge bg-danger-lt">Belum Absen</span>' !!}</td>
        <td>
            @if(!empty($d->foto_out))
                <img src="{{ url($foto_out) }}" class="avatar" alt="Foto Pulang">
            @else
                <span class="badge bg-danger-lt">Belum Absen</span>
            @endif
        </td>
        <td>
            @if ($d->jam_in > '08:00:00')
                @php
                    $jamterlambat = selisih('08:00:00', $d->jam_in);
                @endphp
                <span class="badge bg-danger-lt">Terlambat {{ $jamterlambat }}</span>
            @else
                <span class="badge bg-success-lt">Tepat Waktu</span>
            @endif
        </td>
        <td>
            <a href="#" class="btn btn-primary btn-sm lokasiposisi" id="{{ $d->id }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                    <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                </svg>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="text-center text-muted">Data Presensi Belum Ada</td>
    </tr>
@endforelse