@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <h2 class="page-title">
          Monitoring Presensi
        </h2>
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
            <!-- Form Filter Tanggal -->
            <div class="row mb-3">
              <div class="col-md-4 col-sm-12">
                <div class="input-icon">
                  <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M11 15h1" /><path d="M12 15v3" /></svg>
                  </span>
                  <input type="text" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" class="form-control" placeholder="Tanggal Presensi" autocomplete="off">
                </div>
              </div>
            </div>

            <!-- Tabel Data Monitoring Presensi -->
            <div class="table-responsive">
              <table class="table table-striped table-hover align-middle">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>NIK</th>
                    <th>Nama Perangkat</th>
                    <th>Jabatan</th>
                    <th>Jam Masuk</th>
                    <th>Foto Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Foto Pulang</th>
                    <th>Keterangan</th>
                    <th>Lokasi</th>
                  </tr>
                </thead>
                <tbody id="loadpresensi">
                  <!-- Data monitoring presensi dipanggil via AJAX -->
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tampilkan Peta / Lokasi Presensi -->
<div class="modal modal-blur fade" id="modal-lokasi" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Lokasi Presensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="loadmap">
        <!-- Peta Leaflet akan dimuat di sini -->
      </div>
    </div>
  </div>
</div>
@endsection

@push('myscript')
<script>
  $(function() {
    $("#tanggal").datepicker({ 
      autoclose: true, 
      todayHighlight: true,
      format: 'yyyy-mm-dd'
    });

    function loadpresensi() {
      var tanggal = $("#tanggal").val();
      $.ajax({
        type: 'POST',
        url: '/getpresensi',
        data: {
          _token: "{{ csrf_token() }}",
          tanggal: tanggal
        },
        cache: false,
        success: function(respond) {
          $("#loadpresensi").html(respond);
        }
      });
    }

    $("#tanggal").change(function() {
      loadpresensi();
    });

    loadpresensi();

    // Event handler klik tombol lokasi di dalam tabel
    $(document).on('click', '.lokasiposisi', function(e) {
      e.preventDefault();
      var id = $(this).attr("id");
      $.ajax({
        type: 'POST',
        url: '/tampilkanpeta',
        data: {
          _token: "{{ csrf_token() }}",
          id: id
        },
        cache: false,
        success: function(respond) {
          $("#loadmap").html(respond);
          $("#modal-lokasi").modal("show");
        }
      });
    });
  });
</script>
@endpush