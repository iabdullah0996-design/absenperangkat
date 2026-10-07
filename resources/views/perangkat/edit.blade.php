<form action="/panel/perangkat/{{ $perangkat->nik }}/update" method="POST" id="formEditPerangkat" enctype="multipart/form-data">
    @csrf
    
    <!-- Input NIK (Readonly/Disabled) -->
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-barcode" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 5v14" /><path d="M7 5v14" /><path d="M10 5v14" /><path d="M14 5v14" /><path d="M17 5v14" /><path d="M20 5v14" />
                    </svg>
                </span>
                <input type="text" value="{{ $perangkat->nik }}" class="form-control" name="nik" placeholder="NIK" readonly style="background-color: #f5f5f5;">
            </div>
        </div>
    </div>

    <!-- Input Nama Lengkap -->
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="12" cy="7" r="4" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                    </svg>
                </span>
                <input type="text" id="nama_lengkap_edit" value="{{ $perangkat->nama_lengkap }}" class="form-control" name="nama_lengkap" placeholder="Nama Lengkap" required>
            </div>
        </div>
    </div>

    <!-- Input Jabatan -->
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-briefcase" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <rect x="3" y="7" width="18" height="13" rx="2" />
                        <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                    </svg>
                </span>
                <input type="text" id="jabatan_edit" value="{{ $perangkat->jabatan }}" class="form-control" name="jabatan" placeholder="Jabatan" required>
            </div>
        </div>
    </div>

    <!-- Input No HP -->
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-phone" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                    </svg>
                </span>
                <input type="text" id="no_hp_edit" value="{{ $perangkat->no_hp }}" class="form-control" name="no_hp" placeholder="No. HP" required>
            </div>
        </div>
    </div>

    <!-- Input Password -->
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-key" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="8" cy="15" r="4" />
                        <line x1="10.85" y1="12.15" x2="19" y2="4" />
                        <line x1="18" y1="5" x2="20" y2="7" />
                        <line x1="15" y1="8" x2="17" y2="10" />
                    </svg>
                </span>
                <input type="password" class="form-control" name="password" placeholder="Password (Kosongkan jika tidak diubah)">
            </div>
        </div>
    </div>

    <!-- Preview & Upload Foto -->
    <div class="row mb-3">
        <div class="col-12">
            <label class="form-label">Foto Perangkat</label>
            <div class="d-flex align-items-center gap-3 mb-2">
                @if ($perangkat->foto)
                    @php
                        $path = Storage::url('uploads/perangkat/' . $perangkat->foto);
                    @endphp
                    <img src="{{ url($path) }}" alt="Foto {{ $perangkat->nama_lengkap }}" class="avatar avatar-md rounded-circle me-2" style="object-fit: cover; width: 60px; height: 60px;">
                @else
                    <img src="{{ asset('assets/img/nophoto.png') }}" alt="No Photo" class="avatar avatar-md rounded-circle me-2" style="object-fit: cover; width: 60px; height: 60px;">
                @endif
                <small class="text-muted">Biarkan kosong jika tidak ingin mengganti foto saat ini.</small>
            </div>
            <input type="file" name="foto" class="form-control" accept="image/png, image/jpeg, image/jpg">
        </div>
    </div>

    <!-- Tombol Submit -->
    <div class="row mt-3">
        <div class="col-12">
            <button class="btn btn-primary w-100" type="submit" id="btnSimpanEdit">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <line x1="10" y1="14" x2="21" y2="3" />
                    <path d="M21 3l-6.5 18a0.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a0.55 .55 0 0 1 0 -1l18 -6.5" />
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('#formEditPerangkat').submit(function(e) {
            var nama_lengkap = $('#nama_lengkap_edit').val();
            var jabatan = $('#jabatan_edit').val();
            var no_hp = $('#no_hp_edit').val();

            if (nama_lengkap == "") {
                alert('Nama Lengkap Harus Diisi!');
                $('#nama_lengkap_edit').focus();
                return false;
            }
            if (jabatan == "") {
                alert('Jabatan Harus Diisi!');
                $('#jabatan_edit').focus();
                return false;
            }
            if (no_hp == "") {
                alert('No. HP Harus Diisi!');
                $('#no_hp_edit').focus();
                return false;
            }
        });
    });
</script>