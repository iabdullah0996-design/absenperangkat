@extends('layouts.presensi')

@section('header')
<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Pengajuan Izin / Sakit</div>
    <div class="right"></div>
</div>
<!-- * App Header -->
@endsection

@section('content')
<div class="row" style="margin-top: 70px;">
    <div class="col">
        <!-- Alert Success & Error yang Sudah Diperbaiki -->
        @if (Session::get('success'))
            <div class="alert alert-success mb-2">
                {{ Session::get('success') }}
            </div>
        @endif
        @if (Session::get('error'))
            <div class="alert alert-danger mb-2">
                {{ Session::get('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col">
                @foreach ($dataizin as $d)
                    <ul class="listview image-listview">
                        <li>
                            <div class="item">
                                <div class="in">
                                    <div>
                                        <b>{{ date("d-m-Y", strtotime($d->tgl_izin)) }} ({{ $d->status == 'i' ? 'Izin' : 'Sakit' }})</b><br>
                                        <!-- Nama kolom disesuaikan menjadi keterangan -->
                                        <small class="text-muted">{{ $d->keterangan }}</small>
                                    </div>
                                    <!-- Badge Status Persetujuan -->
                                    @if ($d->status_approved == '1')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif ($d->status_approved == '2')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-warning">Waiting</span>
                                    @endif
                                </div>
                            </div>
                        </li>
                    </ul>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="fab-button bottom-right" style="margin-bottom:70px">
    <a href="/presensi/buatizin" class="fab">
        <ion-icon name="add-outline"></ion-icon>
    </a>
</div>
@endsection