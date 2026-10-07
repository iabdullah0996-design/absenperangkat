<form action="/konfigurasi/updateadmin/{{ $admin->id }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" class="form-control" name="name" value="{{ $admin->name }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" value="{{ $admin->email }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Password Baru <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
        <input type="password" class="form-control" name="password">
    </div>
    <div class="modal-footer">
        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>