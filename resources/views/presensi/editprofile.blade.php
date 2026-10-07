@extends('layouts.presensi')

@section('header')
<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Edit Profile</div>
    <div class="right"></div>
</div>
<!-- * App Header -->
@endsection

@section('content')
<div class="row" style="margin-top:4rem">
    <div class="col">
        @php
            $messagesuccess = Session::get('success');
            $messageerror = Session::get('error');
        @endphp

        @if(Session::get('success'))
            <div class="alert alert-success">
                {{ $messagesuccess }}
            </div>
        @endif

        @if(Session::get('error'))
            <div class="alert alert-danger">
                {{ $messageerror }}
            </div>
        @endif
    </div>
</div>

<form action="/presensi/{{ $perangkat->nik }}/updateprofile" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="col">
        <!-- Input Nama Lengkap -->
        <div class="form-group boxed">
            <label class="label" style="display: block; font-size: 16px; font-weight: 600; margin-bottom: 5px; text-align: left;">
                Ubah Nama
            </label>
            <div class="input-wrapper">
                <input type="text" class="form-control" value="{{ $perangkat->nama_lengkap }}" name="nama_lengkap" placeholder="Nama Lengkap" autocomplete="off">
            </div>
        </div>

        <!-- Input No HP -->
        <div class="form-group boxed">
            <label class="label" style="display: block; font-size: 16px; font-weight: 600; margin-bottom: 5px; text-align: left;">
                Ubah Nomor Handphone
            </label>
            <div class="input-wrapper">
                <input type="text" class="form-control" value="{{ $perangkat->no_hp }}" name="no_hp" placeholder="No. HP" autocomplete="off">
            </div>
        </div>

        <!-- Input Password -->
        <div class="form-group boxed">
            <label class="label" style="display: block; font-size: 16px; font-weight: 600; margin-bottom: 5px; text-align: left;">
                Ubah Password
            </label>
            <div class="input-wrapper">
                <input type="password" class="form-control" name="password" placeholder="Password" autocomplete="off">
            </div>
        </div>

        <!-- Input Upload Foto -->
        <div class="custom-file-upload" id="fileUpload1" style="margin-bottom: 15px;">
            <input type="file" name="foto" id="fileuploadInput" accept=".png, .jpg, .jpeg">
            <label for="fileuploadInput">
                <span>
                    <strong>
                        <ion-icon name="cloud-upload-outline" role="img" class="md hydrated" aria-label="cloud upload outline"></ion-icon>
                        <i>Tap to Upload</i>
                    </strong>
                </span>
            </label>
        </div>

        <!-- Tombol Submit -->
        <div class="form-group boxed">
            <div class="input-wrapper">
                <button type="submit" class="btn btn-primary btn-block">
                    <ion-icon name="refresh-outline"></ion-icon>
                    Update
                </button>
            </div>
        </div>
    </div>
</form>
@endsection