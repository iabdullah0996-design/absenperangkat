@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Persetujuan Izin / Sakit</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                @if (Session::get('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
                @if (Session::get('warning'))
                    <div class="alert alert-warning">{{ Session::get('warning') }}</div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama Perangkat</th>
                            <th>Jabatan</th>
                            <th>Tanggal Izin</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Status Approval</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($izinsakit as $d)
                        <tr>
                            <td>{{ $loop->iteration + $izinsakit->firstItem() - 1 }}</td>
                            <td>{{ $d->nik }}</td>
                            <td>{{ $d->nama_lengkap }}</td>
                            <td>{{ $d->jabatan }}</td>
                            <td>{{ date('d-m-Y', strtotime($d->tgl_izin)) }}</td>
                            <td>
                                @if ($d->status == 'i')
                                    <span class="badge bg-info text-white">Izin</span>
                                @else
                                    <span class="badge bg-warning text-white">Sakit</span>
                                @endif
                            </td>
                            <td>{{ $d->keterangan }}</td>
                            <td>
                                @if ($d->status_approved == '1')
                                    <span class="badge bg-success text-white">Disetujui</span>
                                @elseif($d->status_approved == '2')
                                    <span class="badge bg-danger text-white">Ditolak</span>
                                @else
                                    <span class="badge bg-warning text-white">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if ($d->status_approved == '0')
                                    <button class="btn btn-sm btn-primary btnApprove" id_izinsakit="{{ $d->id }}">
                                        Pilih Aksi
                                    </button>
                                @else
                                    <a href="/presensi/{{ $d->id }}/batalkanizinsakit" class="btn btn-sm btn-danger">Batal</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $izinsakit->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Modal Approval --}}
<div class="modal fade" id="modal-izinsakit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Persetujuan Izin / Sakit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('approveizinsakit') }}" method="POST">
                    @csrf
                    <input type="hidden" id="id_izinsakit_form" name="id_izinsakit_form">
                    <div class="form-group mb-3">
                        <select name="status_approved" class="form-select" required>
                            <option value="1">Disetujui</option>
                            <option value="2">Ditolak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary w-100" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        $(".btnApprove").click(function(e) {
            e.preventDefault();
            var id_izinsakit = $(this).attr("id_izinsakit");
            $("#id_izinsakit_form").val(id_izinsakit);
            $("#modal-izinsakit").modal("show");
        });
    });
</script>
@endpush