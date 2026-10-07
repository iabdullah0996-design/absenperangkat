<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Cetak Rekap Presensi Perangkat Desa</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.3.0/paper.css">

    <style>
        @page {
            size: A4 landscape;
        }

        #title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            font-weight: bold;
        }

        .tabelpresensi {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .tabelpresensi tr th {
            border: 1px solid #131212;
            padding: 8px;
            background-color: #dbdbdb;
            font-size: 12px;
        }

        .tabelpresensi tr td {
            border: 1px solid #131212;
            padding: 5px;
            font-size: 11px;
        }
    </style>
</head>

<body class="A4 landscape">

    <section class="sheet padding-10mm">

        <!-- KOP LAPORAN -->
        <table style="width: 100%">
            <tr>
                <td style="width: 80px">
                    @php
                        $pathLogo = public_path('assets/img/login.png');
                        if (file_exists($pathLogo)) {
                            $typeLogo = pathinfo($pathLogo, PATHINFO_EXTENSION);
                            $dataLogo = file_get_contents($pathLogo);
                            $base64Logo = 'data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);
                        } else {
                            $base64Logo = '';
                        }
                    @endphp

                    @if(!empty($base64Logo))
                        <img src="{{ $base64Logo }}" width="75" alt="Logo">
                    @endif
                </td>
                <td>
                    <span id="title">
                        REKAP PRESENSI PERANGKAT DESA<br>
                        PERIODE {{ strtoupper($namabulan[(int)$bulan]) }} {{ $tahun }}<br>
                        PEMERINTAH DESA JATISARI
                    </span><br>
                    <span style="font-size: 12px;"><i>Jl. Raya Jatisari Jurusan Cirebon - Jatisari – Karawang 41374</i></span>
                </td>
            </tr>
        </table>

        <!-- TABEL REKAP -->
        <table class="tabelpresensi">
            <thead>
                <tr>
                    <th rowspan="2">NIK</th>
                    <th rowspan="2">Nama Perangkat</th>
                    <th rowspan="2">Jabatan</th>
                    <th colspan="31">Tanggal</th>
                </tr>
                <tr>
                    @for ($i = 1; $i <= 31; $i++)
                        <th>{{ $i }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach ($rekap as $d)
                    <tr>
                        <td>{{ $d->nik }}</td>
                        <td>{{ $d->nama_lengkap }}</td>
                        <td>{{ $d->jabatan }}</td>

                        @for ($i = 1; $i <= 31; $i++)
                            @php
                                $tgl = 'tgl_' . $i;
                                $val = property_exists($d, $tgl) ? $d->$tgl : '';
                                
                                // Format tanggal dua digit (01, 02, ..., 31)
                                $tgl_dua_digit = $i < 10 ? '0' . $i : $i;
                                $tgl_lengkap = $tahun . '-' . sprintf('%02d', $bulan) . '-' . $tgl_dua_digit;

                                $status = '-';

                                // 1. Cek jika Hadir dari query rekap (presensi)
                                if (!empty($val)) {
                                    $status = '<span style="color: green; font-weight: bold;">H</span>';
                                } else {
                                    // 2. Cek apakah ada data Izin/Sakit yang disetujui jika variabel $izinsakit dikirim dari Controller
                                    if (isset($izinsakit)) {
                                        foreach ($izinsakit as $iz) {
                                            if ($iz->nik == $d->nik && $iz->tgl_izin == $tgl_lengkap) {
                                                if ($iz->status == 'i') {
                                                    $status = '<span style="color: blue; font-weight: bold;">I</span>';
                                                } elseif ($iz->status == 's') {
                                                    $status = '<span style="color: orange; font-weight: bold;">S</span>';
                                                }
                                                break;
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <td style="text-align: center;">
                                {!! $status !!}
                            </td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- TANDA TANGAN -->
        <table style="width: 100%; margin-top: 40px;">
            <tr>
                <td style="text-align: center;"></td>
                <td style="text-align: center; width: 400px;">
                    Jatisari, {{ date('d') }} {{ $namabulan[(int)date('m')] }} {{ date('Y') }}<br>
                    <strong>Kepala Desa Jatisari</strong>
                    <br><br><br><br><br>
                    <u><b>( CASMITA )</b></u>
                </td>
            </tr>
        </table>

    </section>

</body>

</html>