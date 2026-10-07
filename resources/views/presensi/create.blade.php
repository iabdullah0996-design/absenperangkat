@extends('layouts.presensi')

@section('header')
<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Presensi {{ $cek > 0 ? 'Pulang' : 'Masuk' }}</div>
    <div class="right"></div>
</div>

<style>
    .webcam-capture, .webcam-capture video {
        display: inline-block;
        width: 100% !important;
        margin: 0 auto;
        height: auto !important;
        border-radius: 15px;
    }
    #map {
        height: 200px;
        border-radius: 10px;
        margin-top: 10px;
    }
</style>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('content')
<div class="row" style="margin-top: 70px;">
    <div class="col">
        <input type="hidden" id="lokasi">
        
        <!-- AUDIO NOTIFIKASI -->
        <audio id="notif_in" src="{{ asset('assets/sound/notifin.mp3') }}" preload="auto"></audio>
        <audio id="notif_out" src="{{ asset('assets/sound/notifout.mp3') }}" preload="auto"></audio>
        <audio id="notif_radius" src="{{ asset('assets/sound/diluar.mp3') }}" preload="auto"></audio>

        <!-- Webcam Frame -->
        <div class="webcam-capture"></div>
    </div>
</div>

<div class="row mt-2">
    <div class="col">
        @if ($cek > 0)
            <button id="takeabsen" class="btn btn-danger btn-block">
                <ion-icon name="camera-outline"></ion-icon> Absen Pulang
            </button>
        @else
            <button id="takeabsen" class="btn btn-primary btn-block">
                <ion-icon name="camera-outline"></ion-icon> Absen Masuk
            </button>
        @endif
    </div>
</div>

<div class="row mt-2">
    <div class="col">
        <div id="map"></div>
    </div>
</div>
@endsection

@push('myscript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. Inisialisasi Webcam
    Webcam.set({
        height: 480,
        width: 640,
        image_format: 'jpeg',
        jpeg_quality: 80
    });
    Webcam.attach('.webcam-capture');

    // 2. Setting Koordinat & Radius Kantor (Harus Sama dengan Controller)
    var lat_kantor = -6.365333376171102; 
    var long_kantor = 107.52938486931264;
    var radius_kantor = 1000; // dalam Meter

    // 3. Deteksi Lokasi Pengguna & Tampilkan Peta
    var lokasi = document.getElementById('lokasi');
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    } else {
        alert("Browser Anda tidak mendukung Geolocation.");
    }

    function successCallback(position) {
        var lat_user = position.coords.latitude;
        var long_user = position.coords.longitude;
        
        lokasi.value = lat_user + "," + long_user;

        // Inisialisasi Map Leaflet di Titik Pengguna
        var map = L.map('map').setView([lat_user, long_user], 17);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Marker Lokasi Pengguna
        L.marker([lat_user, long_user]).addTo(map).bindPopup("Lokasi Anda").openPopup();

        // Circle Area/Radius Kantor
        L.circle([lat_kantor, long_kantor], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.2,
            radius: radius_kantor
        }).addTo(map);
    }

    function errorCallback(error) {
        Swal.fire({
            title: 'Error!',
            text: 'Gagal mendeteksi lokasi. Pastikan GPS/Izin Lokasi di browser Anda aktif.',
            icon: 'error'
        });
    }

    // 4. Proses Simpan Absen via AJAX
    $("#takeabsen").click(function(e) {
        e.preventDefault();

        Webcam.snap(function(uri) {
            var image = uri;
            var lokasiVal = $("#lokasi").val();

            if (lokasiVal == "") {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Lokasi belum terdeteksi. Silakan izinkan akses lokasi.',
                    icon: 'warning'
                });
                return false;
            }

            $.ajax({
                type: 'POST',
                url: '/presensi/store',
                data: {
                    _token: "{{ csrf_token() }}",
                    image: image,
                    lokasi: lokasiVal
                },
                cache: false,
                success: function(respond) {
                    var status = respond.trim();

                    var notifIn = document.getElementById('notif_in');
                    var notifOut = document.getElementById('notif_out');
                    var notifRadius = document.getElementById('notif_radius');

                    if (status == "in_success") {
                        if (notifIn) { notifIn.currentTime = 0; notifIn.play().catch(e => {}); }

                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Selamat Bekerja, Absen Masuk Berhasil!',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 5000
                        }).then(() => {
                            window.location.href = '/dashboard';
                        });

                    } else if (status == "out_success") {
                        if (notifOut) { notifOut.currentTime = 0; notifOut.play().catch(e => {}); }

                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Hati-hati di jalan, Absen Pulang Berhasil!',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 5000
                        }).then(() => {
                            window.location.href = '/dashboard';
                        });

                    } else if (status == "out_of_radius") {
                        if (notifRadius) { notifRadius.currentTime = 0; notifRadius.play().catch(e => {}); }

                        Swal.fire({
                            title: 'Di Luar Jangkauan!',
                            text: 'Maaf, Anda berada di luar radius kantor.',
                            icon: 'error'
                        });

                    } else if (status == "empty_image") {
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Foto tidak terdeteksi, silakan coba lagi.',
                            icon: 'error'
                        });

                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'Gagal menyimpan data presensi.',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan jaringan atau server.',
                        icon: 'error'
                    });
                }
            });
        });
    });
</script>
@endpush