<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hariini = date("Y-m-d");
        $bulanini = date("m") * 1; // 1 - 12
        $tahunini = date("Y"); 

        // Ambil NIK user yang sedang login
        $nik = Auth::guard('perangkat')->user()->nik;

        // Data presensi hari ini khusus user yang login
        $presensihariini = DB::table('presensi')
            ->where('nik', $nik)
            ->where('tgl_presensi', $hariini)
            ->first();

        // Histori presensi bulan ini khusus user yang login
        $historibulanini = DB::table('presensi')
            ->where('nik', $nik)
            ->whereMonth('tgl_presensi', $bulanini)
            ->whereYear('tgl_presensi', $tahunini)
            ->orderBy('tgl_presensi', 'desc')
            ->get();

        // Array Nama Bulan
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        // 1. Hitung Total Hadir (memiliki jam_in)
        $totalhadir = DB::table('presensi')
            ->where('nik', $nik)
            ->whereMonth('tgl_presensi', $bulanini)
            ->whereYear('tgl_presensi', $tahunini)
            ->whereNotNull('jam_in')
            ->count();

        // 2. Hitung Total Telat (jam_in > 07:00:00)
        $totaltelat = DB::table('presensi')
            ->where('nik', $nik)
            ->whereMonth('tgl_presensi', $bulanini)
            ->whereYear('tgl_presensi', $tahunini)
            ->where('jam_in', '>', '07:00:00')
            ->count();

        // 3. Total Izin & Sakit (Diambil dari tabel pengajuan_izin yang disetujui)
        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('
                SUM(IF(status = "i", 1, 0)) as total_izin,
                SUM(IF(status = "s", 1, 0)) as total_sakit
            ')
            ->where('nik', $nik)
            ->whereMonth('tgl_izin', $bulanini)
            ->whereYear('tgl_izin', $tahunini)
            ->where('status_approved', '1')
            ->first();

        $totalizin = $rekapizin->total_izin ?? 0;
        $totalsakit = $rekapizin->total_sakit ?? 0;

        // 4. Data Leaderboard Presensi Hari Ini
        $leaderboard = DB::table('presensi')
            ->join('perangkat', 'presensi.nik', '=', 'perangkat.nik')
            ->where('tgl_presensi', $hariini)
            ->orderBy('jam_in', 'asc')
            ->get();

        return view('dashboard.dashboard', compact(
            'presensihariini', 
            'historibulanini', 
            'namabulan', 
            'bulanini', 
            'tahunini',
            'totalhadir',
            'totaltelat',
            'totalizin',
            'totalsakit',
            'leaderboard'
        ));
    }

    public function dashboardadmin()
    {
        $hariini = date("Y-m-d");

        // Hitung Total Perangkat Desa
        $totalperangkat = DB::table('perangkat')->count();

        // Rekap Presensi Hari Ini
        $rekappresensi = DB::table('presensi')
            ->selectRaw('COUNT(nik) as jmlhadir, SUM(IF(jam_in > "07:00",1,0)) as jmlterlambat')
            ->where('tgl_presensi', $hariini)
            ->first();
       
        // Rekap Izin Hari Ini
        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('SUM(IF(status="i",1,0)) as jmlizin, SUM(IF(status="s",1,0)) as jmlsakit')
            ->where('tgl_izin', $hariini)
            ->where('status_approved', 1)
            ->first();

        return view('dashboard.dashboardadmin', compact('totalperangkat', 'rekappresensi', 'rekapizin'));
    }
}