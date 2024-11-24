<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hariini = date('Y-m-d');
        $bulanini = date('m') * 1;
        $tahunini = date('Y');
        $nip = Auth::guard('pegawai')->user()->nip;

        $absensihariini = DB::table('absensi')->where('nip', $nip)->where('tgl_absensi', $hariini)->first();

        $historibulanini = DB::table('absensi')
            ->where('nip', $nip)
            ->whereRaw('MONTH(tgl_absensi)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_absensi)="' . $tahunini . '"')
            ->orderBy('tgl_absensi')
            ->get();

        $rekapabsensi = DB::table('absensi')
            ->selectRaw('COUNT(nip) as jmlhadir, SUM(IF(jam_in > "08:00", 1,0)) as jmlterlambat')
            ->where('nip', $nip)
            ->whereRaw('MONTH(tgl_absensi)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_absensi)="' . $tahunini . '"')
            ->first();

        $leaderboard = DB::table('absensi')
            ->join('pegawai', 'absensi.nip', '=', 'pegawai.nip')
            ->where('tgl_absensi', $hariini)
            ->orderBy('jam_in')
            ->get();

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('SUM(IF(status="i",1,0)) as jmlizin, SUM(IF(status="s",1,0)) as jmlsakit')
            ->where('nip', $nip)
            ->whereRaw('MONTH(tgl_izin)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_izin)="' . $tahunini . '"')
            ->where('status_approved', 1)
            ->first();

        $izinHariIni = DB::table('pengajuan_izin')
            ->join('pegawai', 'pengajuan_izin.nip', '=', 'pegawai.nip')
            ->where('tgl_izin', $hariini)
            ->where('status_approved', 1)
            ->select('nama_lengkap', 'jabatan', 'status')
            ->get();

        return view('dashboard.dashboard', compact(
            'absensihariini',
            'historibulanini',
            'namabulan',
            'bulanini',
            'tahunini',
            'rekapabsensi',
            'leaderboard',
            'rekapizin',
            'izinHariIni'
        ));
    }

    public function dashboardadmin()
    {
        $hariini = date('Y-m-d');
        $rekapabsensi = DB::table('absensi')
            ->selectRaw('COUNT(nip) as jmlhadir, SUM(IF(jam_in > "08:00", 1,0)) as jmlterlambat')
            ->where('tgl_absensi', $hariini)
            ->first();

        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('SUM(IF(status="i",1,0)) as jmlizin, SUM(IF(status="s",1,0)) as jmlsakit')
            ->where('tgl_izin', $hariini)
            ->where('status_approved', 1)
            ->first();

        return view('dashboard.dashboardadmin', compact('rekapabsensi', 'rekapizin'));
    }
}
