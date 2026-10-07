<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KonfigurasiController extends Controller
{
    // Menampilkan daftar admin & form tambah
    public function lokasikantor(Request $request)
    {
        $admin = DB::table('users')->get();
        return view('konfigurasi.lokasikantor', compact('admin'));
    }

    // Tambah Admin Baru
    public function storeadmin(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $simpan = DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($simpan) {
            return redirect()->back()->with(['success' => 'Admin Berhasil Ditambahkan']);
        }
        return redirect()->back()->with(['error' => 'Gagal Menambahkan Admin']);
    }

    // Form Edit Admin (Ajax Modal)
    public function editadmin(Request $request)
    {
        $id = $request->id;
        $admin = DB::table('users')->where('id', $id)->first();
        return view('konfigurasi.editadmin', compact('admin'));
    }

    // Update Data Admin
    public function updateadmin(Request $request, $id)
    {
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;

        $data = [
            'name' => $name,
            'email' => $email,
            'updated_at' => now(),
        ];

        if (!empty($password)) {
            $data['password'] = Hash::make($password);
        }

        $update = DB::table('users')->where('id', $id)->update($data);

        if ($update) {
            return redirect()->back()->with(['success' => 'Data Admin Berhasil Diupdate']);
        }
        return redirect()->back()->with(['error' => 'Gagal Mengupdate Data Admin']);
    }

    // Hapus Admin
    public function deleteadmin($id)
    {
        $delete = DB::table('users')->where('id', $id)->delete();
        if ($delete) {
            return redirect()->back()->with(['success' => 'Admin Berhasil Dihapus']);
        }
        return redirect()->back()->with(['error' => 'Gagal Menghapus Admin']);
    }
}