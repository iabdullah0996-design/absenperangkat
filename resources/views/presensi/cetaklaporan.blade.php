<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Cetak Laporan Presensi Perangkat Desa</title>

    <!-- Normalize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

    <!-- Paper CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.3.0/paper.css">

    <style>
        @page {
            size: A4;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
        }

        /* KOP SURAT */
        .kop-surat {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .kop-surat td {
            vertical-align: middle;
        }

        #title {
            font-size: 16px;
            font-weight: bold;
            line-height: 1.3;
        }

        .sub-title {
            font-size: 11px;
            font-style: italic;
            margin-top: 4px;
            display: block;
        }

        /* DATA PERANGKAT DESA */
        .tabeldataperangkat {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .tabeldataperangkat td {
            padding: 4px 6px;
            font-size: 13px;
            vertical-align: top;
        }

        .foto-profil {
            width: 95px;
            height: 120px;
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* TABEL PRESENSI */
        .tabelpresensi {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .tabelpresensi th {
            border: 1px solid #000;
            padding: 8px 4px;
            background-color: #e2e8f0;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        .tabelpresensi td {
            border: 1px solid #000;
            padding: 6px 4px;
            font-size: 11px;
            vertical-align: middle;
        }

        .foto-absensi {
            width: 35px;
            height: 35px;
            object-fit: cover;
            border-radius: 3px;
            border: 1px solid #ddd;
        }

        .badge-terlambat {
            color: #dc2626;
            font-weight: bold;
        }

        .badge-tepat {
            color: #16a34a;
            font-weight: bold;
        }

        /* TANDA TANGAN */
        .ttd-table {
            width: 100%;
            margin-top: 40px;
        }

        .ttd-table td {
            font-size: 12px;
        }
    </style>
</head>

<body class="A4">

    <section class="sheet padding-10mm">

        <!-- KOP LAPORAN -->
        <table class="kop-surat">
            <tr>
                <td style="width: 80px; text-align: center;">
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
                        <img src="{{ $base64Logo }}" width="70" height="auto" alt="Logo">
                    @else
                        <span style="color:red; font-size:10px;">Logo Tidak Ditemukan</span>
                    @endif
                </td>
                <td style="text-align: center; padding-right: 80px;">
                    <div id="title">
                        LAPORAN PRESENSI PERANGKAT DESA<br>
                        PERIODE {{ strtoupper($namabulan[(int)$bulan]) }} {{ $tahun }}<br>
                        PEMERINTAH DESA JATISARI
                    </div>
                    <span class="sub-title">Jl. Raya Jatisari Jurusan Cirebon - Jatisari – Karawang 41374</span>
                </td>
            </tr>
        </table>

        <!-- DATA PERANGKAT DESA -->
        <table class="tabeldataperangkat">
            <tr>
                <td style="width: 110px; text-align: center;" rowspan="4">
                    @php
                        $fotoPath = public_path('storage/uploads/perangkat/' . $perangkat->foto);
                        if (!empty($perangkat->foto) && file_exists($fotoPath)) {
                            $typeFoto = pathinfo($fotoPath, PATHINFO_EXTENSION);
                            $dataFoto = file_get_contents($fotoPath);
                            $base64Foto = 'data:image/' . $typeFoto . ';base64,' . base64_encode($dataFoto);
                        } else {
                            $base64Foto = '';
                        }
                    @endphp

                    @if (!empty($base64Foto))
                        <img src="{{ $base64Foto }}" class="foto-profil" alt="Foto Profil">
                    @else
                        <img src="{{ asset('assets/img/nophoto.png') }}" class="foto-profil" alt="No Photo">
                    @endif
                </td>
                <td style="width: 110px;"><strong>NIK / ID</strong></td>
                <td style="width: 10px;">:</td>
                <td>{{ $perangkat->nik }}</td>
            </tr>
            <tr>
                <td><strong>Nama Lengkap</strong></td>
                <td>:</td>
                <td>{{ $perangkat->nama_lengkap }}</td>
            </tr>
            <tr>
                <td><strong>Jabatan</strong></td>
                <td>:</td>
                <td>{{ $perangkat->jabatan }}</td>
            </tr>
            <tr>
                <td><strong>No. HP</strong></td>
                <td>:</td>
                <td>{{ $perangkat->no_hp }}</td>
            </tr>
        </table>

        <!-- TABEL DETAIL PRESENSI -->
        <table class="tabelpresensi">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 12%;">Jam Masuk</th>
                    <th style="width: 13%;">Foto Masuk</th>
                    <th style="width: 12%;">Jam Pulang</th>
                    <th style="width: 13%;">Foto Pulang</th>
                    <th style="width: 18%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($presensi as $d)
                    @php
                        $path_in_file = public_path('storage/uploads/absensi/' . $d->foto_in);
                        $path_out_file = public_path('storage/uploads/absensi/' . $d->foto_out);

                        $b64_in = null;
                        if (!empty($d->foto_in) && file_exists($path_in_file)) {
                            $ext_in = pathinfo($path_in_file, PATHINFO_EXTENSION);
                            $b64_in = 'data:image/' . $ext_in . ';base64,' . base64_encode(file_get_contents($path_in_file));
                        }

                        $b64_out = null;
                        if (!empty($d->foto_out) && file_exists($path_out_file)) {
                            $ext_out = pathinfo($path_out_file, PATHINFO_EXTENSION);
                            $b64_out = 'data:image/' . $ext_out . ';base64,' . base64_encode(file_get_contents($path_out_file));
                        }
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td style="text-align: center;">{{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</td>
                        <td style="text-align: center;">{{ $d->jam_in }}</td>
                        <td style="text-align: center;">
                            @if ($b64_in)
                                <img src="{{ $b64_in }}" class="foto-absensi" alt="In">
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if ($d->jam_out != null && $d->jam_out != '00:00:00')
                                {{ $d->jam_out }}
                            @else
                                <span style="color: #6b7280; font-style: italic;">Belum Absen</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if ($b64_out)
                                <img src="{{ $b64_out }}" class="foto-absensi" alt="Out">
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if ($d->jam_in > '07:00:00')
                                <span class="badge-terlambat">Terlambat</span>
                            @else
                                <span class="badge-tepat">Tepat Waktu</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- TANDA TANGAN -->
        <table class="ttd-table">
            <tr>
                <td></td>
                <td style="text-align: center; width: 300px;">
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