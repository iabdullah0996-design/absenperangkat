<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PerangkatController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('perangkat');
        
        if (!empty($request->nama_perangkat)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_perangkat . '%');
        }

        $perangkat = $query->orderBy('nama_lengkap')->paginate(10);
        
        return view('perangkat.index', compact('perangkat'));
    }

    public function store(Request $request)
    {
        $nik = $request->nik;
        $nama_lengkap = $request->nama_lengkap;
        $jabatan = $request->jabatan;
        $no_hp = $request->no_hp;
        $password = Hash::make($request->password ?? '12345');

        if ($request->hasFile('foto')) {
            $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
            $folderPath = "public/uploads/perangkat/";
            $request->file('foto')->storeAs($folderPath, $foto);
        } else {
            $foto = null;
        }

        try {
            DB::table('perangkat')->insert([
                'nik' => $nik,
                'nama_lengkap' => $nama_lengkap,
                'jabatan' => $jabatan,
                'no_hp' => $no_hp,
                'foto' => $foto,
                'password' => $password
            ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan: ' . $e->getMessage()]);
        }
    }

    // Perbaikan Method Edit
    public function edit(Request $request)
    {
        $nik = $request->nik;
        $perangkat = DB::table('perangkat')->where('nik', $nik)->first();

        if (!$perangkat) {
            return response('<div class="alert alert-danger">Data perangkat tidak ditemukan.</div>', 404);
        }

        // Pastikan file view ini ada di: resources/views/perangkat/edit.blade.php
        return view('perangkat.edit', compact('perangkat'));
    }

    public function update(Request $request, $nik)
    {
        $nama_lengkap = $request->nama_lengkap;
        $jabatan = $request->jabatan;
        $no_hp = $request->no_hp;

        $perangkat = DB::table('perangkat')->where('nik', $nik)->first();
        $old_foto = $perangkat ? $perangkat->foto : null;

        if (!empty($request->password)) {
            $password = Hash::make($request->password);
        } else {
            $password = $perangkat->password;
        }

        if ($request->hasFile('foto')) {
            $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
            $folderPath = "public/uploads/perangkat/";

            if ($old_foto && Storage::exists($folderPath . $old_foto)) {
                Storage::delete($folderPath . $old_foto);
            }

            $request->file('foto')->storeAs($folderPath, $foto);
        } else {
            $foto = $old_foto;
        }

        try {
            DB::table('perangkat')->where('nik', $nik)->update([
                'nama_lengkap' => $nama_lengkap,
                'jabatan' => $jabatan,
                'no_hp' => $no_hp,
                'foto' => $foto,
                'password' => $password
            ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate: ' . $e->getMessage()]);
        }
    }

    public function delete($nik)
    {
        $perangkat = DB::table('perangkat')->where('nik', $nik)->first();
        if ($perangkat && $perangkat->foto) {
            $folderPath = "public/uploads/perangkat/";
            if (Storage::exists($folderPath . $perangkat->foto)) {
                Storage::delete($folderPath . $perangkat->foto);
            }
        }

        $delete = DB::table('perangkat')->where('nik', $nik)->delete();
        if ($delete) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
}