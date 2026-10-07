@extends('layouts.presensi')
@section('content')
<div class="section" id="user-section">
    <div id="user-detail" class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <div class="avatar me-3">
                @if(!empty(Auth::guard('perangkat')->user()->foto))
                    @php
                        $path = Storage::url('uploads/perangkat/' . Auth::guard('perangkat')->user()->foto);
                    @endphp
                    <img src="{{ url($path) }}" alt="avatar" class="imaged w64" style="object-fit: cover; object-position: top; width: 64px; height: 64px; border-radius: 50%;">
                @else
                    <img src="{{ asset('assets/img/sample/avatar/avatar1.jpg') }}" alt="avatar" class="imaged w64" style="object-fit: cover; object-position: top; width: 64px; height: 64px; border-radius: 50%;">
                @endif
            </div>
            <div id="user-info">
                <h2 id="user-name" style="margin-bottom: 1px; line-height: 1.2; color: #ffffff;">
                    {{ Auth::guard('perangkat')->user()->nama_lengkap }}
                </h2>
                <span style="color: #ffffff; font-size: 14px; opacity: 0.85; display: block; line-height: 1.2;">
                    {{ Auth::guard('perangkat')->user()->jabatan }}
                </span>
            </div>
        </div>

        <!-- Tombol Logout -->
        <div>
            <a href="/proseslogout" class="text-white" style="font-size: 28px;" title="Logout">
                <ion-icon name="log-out-outline"></ion-icon>
            </a>
        </div>
    </div>
</div>

<div class="section" id="menu-section">
    <div class="card">
        <div class="card-body text-center">
            <div class="list-menu">
                <div class="item-menu text-center">
                    <div class="menu-icon">
                        <a href="/editprofile" class="green" style="font-size: 40px;">
                            <ion-icon name="person-sharp"></ion-icon>
                        </a>
                    </div>
                    <div class="menu-name">
                        <span class="text-center">Profil</span>
                    </div>
                </div>
                <div class="item-menu text-center">
                    <div class="menu-icon">
                        <a href="/presensi/izin" class="danger" style="font-size: 40px;">
                            <ion-icon name="calendar-number"></ion-icon>
                        </a>
                    </div>
                    <div class="menu-name">
                        <span class="text-center">Izin / Sakit</span>
                    </div>
                </div>
                <div class="item-menu text-center">
                    <div class="menu-icon">
                        <a href="/presensi/histori" class="warning" style="font-size: 40px;">
                            <ion-icon name="document-text"></ion-icon>
                        </a>
                    </div>
                    <div class="menu-name">
                        <span class="text-center">Histori</span>
                    </div>
                </div>
                <div class="item-menu text-center">
                    <div class="menu-icon">
                        <a href="/presensi/create" class="orange" style="font-size: 40px;">
                            <ion-icon name="location"></ion-icon>
                        </a>
                    </div>
                    <div class="menu-name">
                        Lokasi
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section mt-2" id="presence-section">
    <div class="todaypresence">
        <div class="row">
            <div class="col-6">
                <div class="card gradasigreen">
                    <div class="card-body">
                        <div class="presencecontent">
                            <div class="iconpresence">
                                @if ($presensihariini != null && $presensihariini->foto_in != null)
                                    @php
                                        $path = Storage::url('uploads/absensi/' . $presensihariini->foto_in);
                                    @endphp
                                    <img src="{{ url($path) }}" alt="Foto Masuk" class="imaged" 
                                        style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px; margin-right: 12px;">
                                @else
                                    <ion-icon name="camera" style="font-size: 32px; margin-right: 12px;"></ion-icon>
                                @endif
                            </div>
                            <div class="presencedetail">
                                <h4 class="presencetitle">Masuk</h4>
                                <span>{{ $presensihariini != null ? $presensihariini->jam_in : 'Belum Absen' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card gradasired">
                    <div class="card-body">
                        <div class="presencecontent">
                            <div class="iconpresence">
                                @if ($presensihariini != null && $presensihariini->foto_out != null)
                                    @php
                                        $path = Storage::url('uploads/absensi/' . $presensihariini->foto_out);
                                    @endphp
                                    <img src="{{ url($path) }}" alt="Foto Pulang" class="imaged" 
                                        style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px; margin-right: 12px;">
                                @else
                                    <ion-icon name="camera" style="font-size: 32px; margin-right: 12px;"></ion-icon>
                                @endif
                            </div>
                            <div class="presencedetail">
                                <h4 class="presencetitle">Pulang</h4>
                                <span>{{ $presensihariini != null && $presensihariini->jam_out != null ? $presensihariini->jam_out : 'Belum Absen' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="rekappresensi" class="mt-2">
        <h3 class="text-xs font-bold text-gray-600 mb-2">
            Rekap Bulan {{ $namabulan[(int)$bulanini] }} Tahun {{ $tahunini }}
        </h3>
        
        <div class="row g-2 text-center">
            <!-- CARD 1: HADIR -->
            <div class="col-3">
                <div class="card border-0 shadow-sm position-relative" style="border-radius: 12px; background: #ffffff;">
                    <div class="card-body text-center p-2" style="line-height: 1.2;">
                        <span class="badge bg-danger rounded-pill position-absolute" 
                              style="top: -4px; right: -4px; font-size: 0.6rem; padding: 3px 6px; z-index: 10;">
                            {{ $totalhadir }}
                        </span>
                        <ion-icon name="hand-right-outline" style="font-size: 1.7rem;" class="text-primary mb-1"></ion-icon>
                        <br>
                        <span style="font-size: 0.75rem; font-weight: 500;">Hadir</span>
                    </div>
                </div>
            </div>

            <!-- CARD 2: IZIN -->
            <div class="col-3">
                <div class="card border-0 shadow-sm position-relative" style="border-radius: 12px; background: #ffffff;">
                    <div class="card-body text-center p-2" style="line-height: 1.2;">
                        <span class="badge bg-danger rounded-pill position-absolute" 
                              style="top: -4px; right: -4px; font-size: 0.6rem; padding: 3px 6px; z-index: 10;">
                            {{ $totalizin }}
                        </span>
                        <ion-icon name="newspaper-outline" style="font-size: 1.7rem;" class="text-success mb-1"></ion-icon>
                        <br>
                        <span style="font-size: 0.75rem; font-weight: 500;">Izin</span>
                    </div>
                </div>
            </div>

            <!-- CARD 3: SAKIT -->
            <div class="col-3">
                <div class="card border-0 shadow-sm position-relative" style="border-radius: 12px; background: #ffffff;">
                    <div class="card-body text-center p-2" style="line-height: 1.2;">
                        <span class="badge bg-danger rounded-pill position-absolute" 
                              style="top: -4px; right: -4px; font-size: 0.6rem; padding: 3px 6px; z-index: 10;">
                            {{ $totalsakit }}
                        </span>
                        <ion-icon name="medkit-outline" style="font-size: 1.7rem;" class="text-warning mb-1"></ion-icon>
                        <br>
                        <span style="font-size: 0.75rem; font-weight: 500;">Sakit</span>
                    </div>
                </div>
            </div>

            <!-- CARD 4: TELAT -->
            <div class="col-3">
                <div class="card border-0 shadow-sm position-relative" style="border-radius: 12px; background: #ffffff;">
                    <div class="card-body text-center p-2" style="line-height: 1.2;">
                        <span class="badge bg-danger rounded-pill position-absolute" 
                              style="top: -4px; right: -4px; font-size: 0.6rem; padding: 3px 6px; z-index: 10;">
                            {{ $totaltelat }}
                        </span>
                        <ion-icon name="time-outline" style="font-size: 1.7rem;" class="text-danger mb-1"></ion-icon>
                        <br>
                        <span style="font-size: 0.75rem; font-weight: 500;">Telat</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
                
    <div class="presencetab mt-2">
        <div class="tab-pane fade show active" id="pilled" role="tabpanel">
            <ul class="nav nav-tabs style1" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                        Bulan Ini
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#profile" role="tab">
                        Leaderboard
                    </a>
                </li>
            </ul>
        </div>
        <div class="tab-content mt-2" style="margin-bottom:100px;">
            <div class="tab-pane fade show active" id="home" role="tabpanel">
                <ul class="listview image-listview">
                    @foreach ($historibulanini as $d)
                        <li>
                            <div class="item">
                                <div class="icon-box {{ isset($d->status) && $d->status == 'i' ? 'bg-success' : (isset($d->status) && $d->status == 's' ? 'bg-warning' : 'bg-primary') }}">
                                    @if(isset($d->status) && $d->status == 'i')
                                        <ion-icon name="newspaper-outline"></ion-icon>
                                    @elseif(isset($d->status) && $d->status == 's')
                                        <ion-icon name="medkit-outline"></ion-icon>
                                    @else
                                        <ion-icon name="finger-print-outline"></ion-icon>
                                    @endif
                                </div>
                                <div class="in">
                                    <div>{{ date("d-m-Y", strtotime($d->tgl_presensi)) }}</div>
                                    @if (isset($d->status) && $d->status == 'i')
                                        <span class="badge badge-warning">Izin</span>
                                    @elseif (isset($d->status) && $d->status == 's')
                                        <span class="badge badge-info">Sakit</span>
                                    @else
                                        <span class="badge badge-success">{{ $d->jam_in }}</span>
                                        <span class="badge badge-danger">{{ $d->jam_out != null ? $d->jam_out : 'Belum Absen' }}</span>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel">
                <ul class="listview image-listview">
                    @foreach ($leaderboard as $d)
                        <li>
                            <div class="item">
                                @if(!empty($d->foto))
                                    @php
                                        $path = Storage::url('uploads/perangkat/' . $d->foto);
                                    @endphp
                                    <img src="{{ url($path) }}" alt="image" class="image" style="object-fit: cover; width: 40px; height: 40px; border-radius: 50%;">
                                @else
                                    <img src="{{ asset('assets/img/sample/avatar/avatar1.jpg') }}" alt="image" class="image" style="object-fit: cover; width: 40px; height: 40px; border-radius: 50%;">
                                @endif
                                <div class="in">
                                    <div style="display: flex; flex-direction: column; text-align: left;">
                                        <b style="line-height: 1.2;">{{ $d->nama_lengkap }}</b>
                                        <small class="text-muted" style="font-size: 11px; line-height: 1.2; margin-top: 2px;">
                                            {{ $d->jabatan }}
                                        </small>
                                    </div>
                                    <span class="badge {{ $d->jam_in > '07:00:00' ? 'badge-danger' : 'badge-success' }}">
                                        {{ $d->jam_in }}
                                    </span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- App Bottom Menu -->
<div class="appBottomMenu">
    <a href="/dashboard" class="item {{ Request::is('dashboard') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="home-outline" role="img" class="md hydrated"></ion-icon>
            <strong>Home</strong>
        </div>
    </a>
    <a href="/presensi/histori" class="item {{ Request::is('presensi/histori') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="document-text-outline" role="img" class="md hydrated"></ion-icon>
            <strong>Histori</strong>
        </div>
    </a>
    <a href="/presensi/create" class="item">
        <div class="col">
            <div class="action-button large">
                <ion-icon name="camera" role="img" class="md hydrated"></ion-icon>
            </div>
        </div>
    </a>
    <a href="/presensi/izin" class="item {{ Request::is('presensi/izin*') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="calendar-outline" role="img" class="md hydrated"></ion-icon>
            <strong>Izin</strong>
        </div>
    </a>
    <a href="/editprofile" class="item {{ Request::is('editprofile') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="person-outline" role="img" class="md hydrated"></ion-icon>
            <strong>Profil</strong>
        </div>
    </a>
</div>
@endsection