@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    DATA
                </div>
                <h2 class="page-title">
                    Perangkat Desa Jatisari
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <!-- Tombol Pemicu Modal Tambah Data -->
                    <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-tambahperangkat">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Tambah Data
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Alert Pesan Sukses / Warning -->
                        @if (Session::get('success'))
                            <div class="alert alert-success alert-dismissible" role="alert">
                                <div>{{ Session::get('success') }}</div>
                                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                            </div>
                        @endif
                        @if (Session::get('warning'))
                            <div class="alert alert-warning alert-dismissible" role="alert">
                                <div>{{ Session::get('warning') }}</div>
                                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                            </div>
                        @endif

                        <!-- Form Pencarian -->
                        <form action="/panel/perangkat" method="GET">
                            <div class="row mb-3">
                                <div class="col-10">
                                    <input type="text" name="nama_perangkat" id="nama_perangkat" class="form-control" placeholder="Cari Nama Perangkat..." value="{{ request('nama_perangkat') }}">
                                </div>
                                <div class="col-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                            <path d="M21 21l-6 -6" />
                                        </svg>
                                        Cari
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Tabel Data Perangkat -->
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-striped">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>NIK</th>
                                        <th>NAMA</th>
                                        <th>JABATAN</th>
                                        <th>NO. HP</th>
                                        <th>FOTO</th>
                                        <th class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($perangkat as $d)
                                        @php
                                            $path = Storage::url('uploads/perangkat/'.$d->foto);
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration + $perangkat->firstItem() - 1 }}</td>
                                            <td>{{ $d->nik }}</td>
                                            <td><strong>{{ $d->nama_lengkap }}</strong></td>
                                            <td><span class="badge bg-blue-lt">{{ $d->jabatan }}</span></td>
                                            <td>{{ $d->no_hp }}</td>
                                            <td>
                                                @if (!empty($d->foto))
                                                    <img src="{{ url($path) }}" alt="" class="avatar avatar-sm">
                                                @else
                                                    <span class="avatar avatar-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-list flex-nowrap justify-content-center">
                                                    <!-- Tombol Edit (Diperbaiki) -->
                                                    <a href="javascript:void(0)" class="btn btn-primary btn-sm btn-edit-perangkat" data-nik="{{ $d->nik }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" style="pointer-events: none;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                                    </a>
                                                    <!-- Form Hapus -->
                                                    <form action="/panel/perangkat/{{ $d->nik }}/delete" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Data tidak ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $perangkat->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Data -->
<div class="modal modal-blur fade" id="modal-tambahperangkat" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Perangkat Desa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/panel/perangkat/store" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">NIK</label>
                        <input type="text" class="form-control" name="nik" placeholder="Masukkan NIK" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" placeholder="Masukkan Nama Lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <input type="text" class="form-control" name="jabatan" placeholder="Masukkan Jabatan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" class="form-control" name="no_hp" placeholder="Masukkan No HP" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Login</label>
                        <input type="password" class="form-control" name="password" placeholder="Masukkan Password Login" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Perangkat</label>
                        <input type="file" class="form-control" name="foto">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary ms-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Data -->
<div class="modal modal-blur fade" id="modal-editperangkat" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Perangkat Desa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="loadeditform">
                <!-- Form dari edit.blade.php dimuat via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        $(document).on('click', '.btn-edit-perangkat', function(e) {
            e.preventDefault();
            
            var nik = $(this).data('nik');

            $.ajax({
                type: 'POST',
                url: '/panel/perangkat/edit',
                data: {
                    _token: "{{ csrf_token() }}",
                    nik: nik
                },
                cache: false,
                success: function(respond) {
                    $('#loadeditform').html(respond);
                    $('#modal-editperangkat').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error response:', xhr.responseText);
                    alert('Gagal memuat form edit. Pastikan route /panel/perangkat/edit ada.');
                }
            });
        });
    });
</script>
@endpush