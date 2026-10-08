<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Throwable;

class PresensiController extends Controller
{
    public function create()
    {
        $today = date("Y-m-d");
        $user = Auth::guard('perangkat')->user();
        
        $nik = $user->nik ?? $user->id;

        $cek = DB::table('presensi')
            ->where('tgl_presensi', $today)
            ->where('nik', $nik)
            ->count();
        
        return view('presensi.create', compact('cek'));
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::guard('perangkat')->user();
            $nik = $user->nik ?? $user->id;
            $tgl_presensi = date("Y-m-d");
            $jam = date("H:i:s");

            // -------------------------------------------------------------
            // SETTING KOORDINAT KANTOR & RADIUS
            // -------------------------------------------------------------
            $lat_kantor = -6.365333376171102;   // Latitude Kantor
            $long_kantor = 107.52938486931264; // Longitude Kantor
            $max_radius = 1000;                // Radius dalam meter

            // 1. Validasi Input Lokasi
            $lokasi = $request->lokasi;
            if (empty($lokasi)) {
                return response("empty_location", 200);
            }

            $lokasi_user = explode(",", $lokasi);
            if (count($lokasi_user) < 2) {
                return response("empty_location", 200);
            }
            $lat_user = (float) $lokasi_user[0];
            $long_user = (float) $lokasi_user[1];

            // 2. Hitung Radius Jarak
            $jarak = $this->distance($lat_kantor, $long_kantor, $lat_user, $long_user);
            $radius = round($jarak['meters']);

            if ($radius > $max_radius) {
                return response("out_of_radius", 200);
            }

            // 3. Validasi & Proses Gambar Foto (Base64)
            $image = $request->image;
            if (empty($image)) {
                return response("empty_image", 200);
            }

            $image_parts = explode(";base64,", $image);
            if (count($image_parts) < 2) {
                return response("empty_image", 200);
            }

            $image_base64 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));

            if (!$image_base64) {
                return response("empty_image", 200);
            }

            // 4. Cek Status Presensi Hari Ini
            $cek = DB::table('presensi')
                ->where('tgl_presensi', $tgl_presensi)
                ->where('nik', $nik)
                ->count();

            $ket = $cek > 0 ? "out" : "in";
            $fileName = $nik . "-" . $tgl_presensi . "-" . $ket . ".png";
            $folderPath = "uploads/absensi/";

            if (!Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->makeDirectory($folderPath);
            }

            $file = $folderPath . $fileName;

            if ($cek > 0) {
                // ABSEN PULANG
                $data_pulang = [
                    'jam_out' => $jam,
                    'foto_out' => $fileName,
                    'lokasi' => $lokasi
                ];

                DB::table('presensi')
                    ->where('tgl_presensi', $tgl_presensi)
                    ->where('nik', $nik)
                    ->update($data_pulang);

                Storage::disk('public')->put($file, $image_base64);

                return response("out_success", 200);

            } else {
                // ABSEN MASUK
                $data_masuk = [
                    'nik' => $nik,
                    'tgl_presensi' => $tgl_presensi,
                    'jam_in' => $jam,
                    'foto_in' => $fileName,
                    'lokasi' => $lokasi
                ];

                try {
                    DB::table('presensi')->insert($data_masuk);
                    Storage::disk('public')->put($file, $image_base64);

                    return response("in_success", 200);
                } catch (\Exception $ex) {
                    Log::error("Database Insert Error: " . $ex->getMessage());
                    return response("db_error:" . $ex->getMessage(), 200);
                }
            }

        } catch (Throwable $e) {
            Log::error("Presensi Store Error: " . $e->getMessage());
            return response("error", 200);
        }
    }

    private function distance($latitude1, $longitude1, $latitude2, $longitude2)
    {
        $theta = $longitude1 - $longitude2;
        $val = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        
        if ($val > 1) {
            $val = 1;
        } else if ($val < -1) {
            $val = -1;
        }

        $miles = acos($val);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;

        return compact('meters', 'kilometers');
    }

    public function editprofile()
    {
        $nik = Auth::guard('perangkat')->user()->nik;
        $perangkat = DB::table('perangkat')->where('nik', $nik)->first();
        return view('presensi.editprofile', compact('perangkat'));
    }

    public function updateprofile(Request $request)
    {
        $nik = Auth::guard('perangkat')->user()->nik;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
        
        $perangkat = DB::table('perangkat')->where('nik', $nik)->first();

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $foto = $nik . "." . $file->getClientOriginalExtension();
            $folderPath = "uploads/perangkat/";

            if (!Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->makeDirectory($folderPath);
            }

            Storage::disk('public')->putFileAs($folderPath, $file, $foto);
        } else {
            $foto = $perangkat->foto ?? null;
        }

        $data = [
            'nama_lengkap' => $nama_lengkap,
            'no_hp'        => $no_hp,
            'foto'         => $foto,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        try {
            DB::table('perangkat')->where('nik', $nik)->update($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Di Perbarui']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => 'Data Gagal Di Perbarui: ' . $e->getMessage()]);
        }
    }

    public function histori()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.histori', compact('namabulan'));
    }

    public function gethistori(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nik = Auth::guard('perangkat')->user()->nik;

        $namabulan = [
            "", "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];

        // Diperbaiki menggunakan whereMonth & whereYear (aman dari SQL Injection)
        $histori = DB::table('presensi')
            ->whereMonth('tgl_presensi', $bulan)
            ->whereYear('tgl_presensi', $tahun)
            ->where('nik', $nik)
            ->orderBy('tgl_presensi')
            ->get();

        return view('presensi.gethistori', compact('histori', 'namabulan'));
    }

    public function izin()
    {
        $nik = Auth::guard('perangkat')->user()->nik;
        $dataizin = DB::table('pengajuan_izin')->where('nik', $nik)->get();    
        return view('presensi.izin', compact('dataizin'));
    }

    public function buatizin()
    {
        return view('presensi.buatizin');
    }

    public function storeizin(Request $request)
    {
        $nik = Auth::guard('perangkat')->user()->nik;
        $tgl_izin = $request->tgl_izin;
        $status = $request->status;
        $keterangan = $request->keterangan;

        $data = [
            'nik' => $nik,
            'tgl_izin' => $tgl_izin,
            'status' => $status,
            'keterangan' => $keterangan,
            'status_approved' => '0'
        ];

        $simpan = DB::table('pengajuan_izin')->insert($data);

        if ($simpan) {
            return redirect('/presensi/izin')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('/presensi/izin')->with(['error' => 'Data Gagal Disimpan']);
        }
    }

    public function monitoring()
    {
        return view('presensi.monitoring');
    }

    public function getpresensi(Request $request)
    {
        $tanggal = $request->tanggal;

        $presensi = DB::table('presensi')
            ->select('presensi.*', 'perangkat.nama_lengkap', 'perangkat.jabatan')
            ->join('perangkat', 'presensi.nik', '=', 'perangkat.nik')
            ->where('tgl_presensi', $tanggal)
            ->get();

        return view('presensi.getpresensi', compact('presensi'));
    }

    public function izinsakit(Request $request)
    {
        $query = DB::table('pengajuan_izin')
            ->select('pengajuan_izin.*', 'perangkat.nama_lengkap', 'perangkat.jabatan')
            ->join('perangkat', 'pengajuan_izin.nik', '=', 'perangkat.nik');

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_izin', [$request->dari, $request->sampai]);
        }

        if (!empty($request->nik)) {
            $query->where('pengajuan_izin.nik', $request->nik);
        }

        if (!empty($request->nama_lengkap)) {
            $query->where('perangkat.nama_lengkap', 'like', '%' . $request->nama_lengkap . '%');
        }

        if ($request->status_approved !== null && $request->status_approved !== '') {
            $query->where('status_approved', $request->status_approved);
        }

        $izinsakit = $query->orderBy('tgl_izin', 'desc')->paginate(10);
        $izinsakit->appends($request->all());

        return view('presensi.izinsakit', compact('izinsakit'));
    }

    public function approveizinsakit(Request $request)
    {
        $id_izinsakit = $request->id_izinsakit_form;
        $status_approved = $request->status_approved;

        $update = DB::table('pengajuan_izin')
            ->where('id', $id_izinsakit)
            ->update(['status_approved' => $status_approved]);

        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diperbarui']);
        } else {
            return Redirect::back()->with(['error' => 'Data Gagal Diperbarui']);
        }
    }

    public function batalkanizinsakit($id)
    {
        $update = DB::table('pengajuan_izin')
            ->where('id', $id)
            ->update(['status_approved' => 0]);

        if ($update) {
            return Redirect::back()->with(['success' => 'Persetujuan Berhasil Dibatalkan']);
        } else {
            return Redirect::back()->with(['error' => 'Persetujuan Gagal Dibatalkan']);
        }
    }

    public function laporan()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $perangkat = DB::table('perangkat')->orderBy('nama_lengkap')->get();
        return view('presensi.laporan', compact('namabulan', 'perangkat'));
    }

    public function cetaklaporan(Request $request)
    {
        $nik = $request->nik;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $perangkat = DB::table('perangkat')->where('nik', $nik)->first();

        // Diperbaiki menggunakan whereMonth & whereYear
        $presensi = DB::table('presensi')
            ->where('nik', $nik)
            ->whereMonth('tgl_presensi', $bulan)
            ->whereYear('tgl_presensi', $tahun)
            ->orderBy('tgl_presensi')
            ->get();

        return view('presensi.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'perangkat', 'presensi'));
    }

    public function rekap()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.rekap', compact('namabulan'));
    }

    public function cetakrekap(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $query = DB::table('perangkat')
            ->select('perangkat.nik', 'perangkat.nama_lengkap', 'perangkat.jabatan');

        for ($i = 1; $i <= 31; $i++) {
            $query->selectRaw("MAX(IF(DAY(presensi.tgl_presensi) = $i, CONCAT(presensi.jam_in, '-', IFNULL(presensi.jam_out, '00:00:00')), '')) as tgl_$i");
        }

        $rekap = $query->leftJoin('presensi', function ($join) use ($bulan, $tahun) {
                $join->on('perangkat.nik', '=', 'presensi.nik')
                    ->whereRaw('MONTH(presensi.tgl_presensi) = ?', [$bulan])
                    ->whereRaw('YEAR(presensi.tgl_presensi) = ?', [$tahun]);
            })
            ->groupBy('perangkat.nik', 'perangkat.nama_lengkap', 'perangkat.jabatan')
            ->orderBy('perangkat.nama_lengkap')
            ->get();

        $izinsakit = DB::table('pengajuan_izin')
            ->whereRaw('MONTH(tgl_izin) = ?', [$bulan])
            ->whereRaw('YEAR(tgl_izin) = ?', [$tahun])
            ->where('status_approved', '1')
            ->get();

        return view('presensi.cetakrekap', compact('bulan', 'tahun', 'namabulan', 'rekap', 'izinsakit'));
    }
}