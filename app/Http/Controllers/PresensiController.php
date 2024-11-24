<?php

namespace App\Http\Controllers;

use App\Models\Pengajuanizin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function create()
    {
        $hariini = date("Y-m-d");
        $nip = Auth::guard('karyawan')->user()->nip;
        $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nip', $nip)->count();
        $lok_kantor = DB::table('konfigurasi_lokasi')->where('id', 1)->first();

        // cek apakah sudah absen atau belum
        $alreadyCheckedOut = DB::table('presensi')
            ->where('tgl_presensi', $hariini)
            ->where('nip', $nip)
            ->whereNotNull('jam_out')
            ->exists();

        return view('presensi.create', compact('cek', 'alreadyCheckedOut', 'lok_kantor'));
    }

    public function store(Request $request)
    {
        $nip = Auth::guard('karyawan')->user()->nip;
        $tgl_presensi = date("Y-m-d");
        $jam = date('H:i:s');

        $izinDisetujui = DB::table('pengajuan_izin')
            ->where('nip', $nip)
            ->where('tgl_izin', $tgl_presensi)
            ->where('status_approved', 1)
            ->exists();

        if ($izinDisetujui) {
            echo "warning|Anda sudah diizinkan pada hari ini.";
            return;
        }

        $lok_kantor = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        $lok = explode(",", $lok_kantor->lokasi_kantor);

        // Lokasi Kantor
        $latitudekantor = $lok[0];
        $longitudekantor = $lok[1];

        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);

        // Lokasi user
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];

        $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);

        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nip', $nip)->count();

        if ($cek > 0) {
            $ket = "out";
        } else {
            $ket = "in";
        }

        $image = $request->image;
        $folderPath = "public/uploads/absensi/";
        $formatName = $nip . "-" . $tgl_presensi . "-" . $ket;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        if ($radius > $lok_kantor->radius) {
            echo "error|Maaf Anda Berada Diluar Radius, Jarak Anda " . $radius . " Meter dari Kantor|radius";
        } else {
            if ($cek > 0) {
                $data_pulang = [
                    'jam_out' => $jam,
                    'foto_out' => $fileName,
                    'lokasi_out' => $lokasi,
                ];
                $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nip', $nip)->update($data_pulang);
                if ($update) {
                    echo "success|Terimakasih, Hati-Hati di Jalan|out";
                    Storage::put($file, $image_base64);
                } else {
                    echo "error|Maaf Gagal Absen, Silahkan Coba Lagi|out";
                }
            } else {
                $data = [
                    'nip' => $nip,
                    'tgl_presensi' => $tgl_presensi,
                    'jam_in' => $jam,
                    'foto_in' => $fileName,
                    'lokasi_in' => $lokasi,
                ];

                $simpan = DB::table('presensi')->insert($data);
                if ($simpan) {
                    echo "success|Terimakasih, Selamat Bekerja|in";
                    Storage::put($file, $image_base64);
                } else {
                    echo "error|Maaf Gagal Absen, Silahkan Coba Lagi|out";
                }
            }
        }
    }


    private function checkPresence()
    {
        $hariini = date("Y-m-d");
        $nip = Auth::guard('karyawan')->user()->nip;
        $cek_in = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nip', $nip)->whereNotNull('jam_in')->exists();
        $cek_out = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nip', $nip)->whereNotNull('jam_out')->exists();

        if ($cek_in && $cek_out) {
            return "Anda sudah absen hari ini";
        } elseif ($cek_in) {
            return "Anda sudah absen masuk, silahkan absen pulang";
        } else {
            return "Silahkan absen masuk";
        }
    }

    //Menghitung Jarak
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }

    public function editprofile()
    {
        $nip = Auth::guard('karyawan')->user()->nip;
        $karyawan = DB::table('karyawan')->where('nip', $nip)->first();
        return view('presensi.editprofile', compact('karyawan'));
    }

    public function updateprofile(Request $request)
    {
        $nip = Auth::guard('karyawan')->user()->nip;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
        $password = Hash::make($request->password);
        $karyawan = DB::table('karyawan')->where('nip', $nip)->first();

        if ($request->hasFile('foto')) {
            $foto = $nip . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $karyawan->foto;
        }

        if (empty($request->password)) {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'foto' => $foto
            ];
        } else {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'password' => $password,
                'foto' => $foto
            ];
        }

        $update = DB::table('karyawan')->where('nip', $nip)->update($data);
        if ($update) {
            if ($request->hasFile('foto')) {
                $folderPath = "public/uploads/karyawan/";
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return Redirect::back()->with(['success' => 'Data Berhasil di Update']);
        } else {
            return Redirect::back()->with(['error' => 'Data Gagal di Update']);
        }
    }

    public function histori()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        return view('presensi.histori', compact('namabulan'));
    }

    public function gethistori(Request  $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nip = Auth::guard('karyawan')->user()->nip;

        $histori = DB::table('presensi')
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->where('nip', $nip)
            ->orderBy('tgl_presensi')
            ->get();

        return view('presensi.gethistori', compact('histori'));
    }

    public function izin()
    {
        $nip = Auth::guard('karyawan')->user()->nip;
        $dataizin = DB::table('pengajuan_izin')->where('nip', $nip)->get();
        return view('presensi.izin', compact('dataizin'));
    }

    public function buatizin()
    {

        return view('presensi.buatizin');
    }

    public function storeizin(Request $request)
    {
        $nip = Auth::guard('karyawan')->user()->nip;
        $tgl_izin = $request->tgl_izin;
        $status = $request->status;
        $keterangan = $request->keterangan;

        $data = [
            'nip' => $nip,
            'tgl_izin' => $tgl_izin,
            'status' => $status,
            'keterangan' => $keterangan
        ];

        $simpan = DB::table('pengajuan_izin')->insert($data);

        if ($simpan) {
            return redirect('/presensi/izin')->with(['success' => 'Data Berhasil di Simpan']);
        } else {
            return redirect('/presensi/izin')->with(['error' => 'Data Gagal di Simpan']);
        }
    }

    public function updateizin(Request $request, $id)
    {
        $tgl_izin = $request->tgl_izin;
        $status = $request->status;
        $keterangan = $request->keterangan;

        $data = [
            'tgl_izin' => $tgl_izin,
            'status' => $status,
            'keterangan' => $keterangan
        ];

        $update = DB::table('pengajuan_izin')->where('id', $id)->update($data);

        if ($update) {
            return redirect('/presensi/izin')->with(['success' => 'Data Berhasil di Update']);
        } else {
            return redirect('/presensi/izin')->with(['error' => 'Data Gagal di Update']);
        }
    }

    public function editizin($id)
    {
        $izin = DB::table('pengajuan_izin')->where('id', $id)->first();
        return view('presensi.editizin', compact('izin'));
    }



    public function deleteizin($id)
    {
        $delete = DB::table('pengajuan_izin')->where('id', $id)->delete();

        if ($delete) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Hapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Hapus']);
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
            ->select('presensi.*', 'nama_lengkap', 'nama_dept')
            ->join('karyawan', 'presensi.nip', '=', 'karyawan.nip')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->where('tgl_presensi', $tanggal)
            ->get();

        return view('presensi.getpresensi', compact('presensi'));
    }

    public function tampilkanpeta(Request $request)
    {
        $id = $request->id;
        $presensi = DB::table('presensi')->where('id', $id)
            ->join('karyawan', 'presensi.nip', '=', 'karyawan.nip')
            ->first();
        return view('presensi.showmap', compact('presensi'));
    }

    public function laporan()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $karyawan = DB::table('karyawan')->orderBy('nama_lengkap')->get();
        return view('presensi.laporan', compact('namabulan', 'karyawan'));
    }

    public function cetaklaporan(Request $request)
    {

        $nip = $request->nip;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $karyawan = DB::table('karyawan')->where('nip', $nip)
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->first();
        $presensi = DB::table('presensi')
            ->where('nip', $nip)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->orderBy('tgl_presensi')
            ->get();

        return view('presensi.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'karyawan', 'presensi'));
    }

    public function rekap()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.rekap', compact('namabulan',));
    }

    public function cetakrekap(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        // Mendefinisikan fields untuk query
        $fields = 'presensi.nip, karyawan.nama_lengkap, departemen.nama_dept';
        for ($i = 1; $i <= 31; $i++) {
            $fields .= ", MAX(IF(DAY(tgl_presensi) = $i, 
                        IF(jam_in IS NOT NULL, 'H', 
                            IF(EXISTS (
                                SELECT 1 
                                FROM pengajuan_izin 
                                WHERE pengajuan_izin.nip = karyawan.nip 
                                AND DATE(pengajuan_izin.tgl_izin) = DATE(CONCAT('$tahun-', '$bulan', '-', $i))
                                AND pengajuan_izin.status_approved = 1
                            ), 'I', 'A')), 
                        '')) as tgl_$i";
        }

        $rekap = DB::table('presensi')
            ->join('karyawan', 'presensi.nip', '=', 'karyawan.nip')
            ->leftJoin('pengajuan_izin', function ($join) use ($bulan, $tahun) {
                $join->on('karyawan.nip', '=', 'pengajuan_izin.nip')
                    ->whereMonth('pengajuan_izin.tgl_izin', $bulan)
                    ->whereYear('pengajuan_izin.tgl_izin', $tahun);
            })
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                'presensi.nip',
                'karyawan.nama_lengkap',
                'karyawan.jabatan',
                'departemen.nama_dept',
                DB::raw($fields),
                DB::raw("GROUP_CONCAT(DISTINCT DATE_FORMAT(pengajuan_izin.tgl_izin, '%Y-%m-%d') SEPARATOR ', ') as tgl_izin"),
                DB::raw("GROUP_CONCAT(DISTINCT pengajuan_izin.status_approved SEPARATOR ', ') as status_approved"),
                DB::raw("GROUP_CONCAT(DISTINCT pengajuan_izin.id SEPARATOR ', ') as izin_ids")
            )
            ->whereMonth('presensi.tgl_presensi', $bulan)
            ->whereYear('presensi.tgl_presensi', $tahun)
            ->groupBy('presensi.nip', 'karyawan.nama_lengkap', 'departemen.nama_dept')
            ->get();

        return view('presensi.cetakrekap', compact('bulan', 'tahun', 'namabulan', 'rekap'));
    }

    public function izinsakit(Request $request)
    {

        $query = Pengajuanizin::query();
        $query->select('id', 'tgl_izin', 'pengajuan_izin.nip', 'nama_lengkap', 'jabatan', 'status', 'status_approved', 'keterangan');
        $query->join('karyawan', 'pengajuan_izin.nip', '=', 'karyawan.nip');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_izin', [$request->dari, $request->sampai]);
        }

        if (!empty($request->nip)) {
            $query->where('pengajuan_izin.nip', $request->nip);
        }

        if (!empty($request->nama_lengkap)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_lengkap . '%');
        }

        if ($request->status_approved === '0' || $request->status_approved === '1' || $request->status_approved === '2') {
            $query->where('status_approved', $request->status_approved);
        }

        $query->orderBy('tgl_izin', 'desc');
        $izinsakit = $query->paginate(10);
        $izinsakit->appends($request->all());
        return view('presensi.izinsakit', compact('izinsakit'));
    }

    public function approveizinsakit(Request $request)
    {
        $status_approved = $request->status_approved;
        $id_izinsakit_form = $request->id_izinsakit_form;
        $update = DB::table('pengajuan_izin')->where('id', $id_izinsakit_form)->update([
            'status_approved' => $status_approved
        ]);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal di Update']);
        }
    }

    public function batalkanizinsakit($id)
    {
        $update = DB::table('pengajuan_izin')->where('id', $id)->update([
            'status_approved' => 0
        ]);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal di Update']);
        }
    }

    public function cekpengajuanizin(Request $request)
    {
        $tgl_izin = $request->tgl_izin;
        $nip = Auth::guard('karyawan')->user()->nip;

        $cek = DB::table('pengajuan_izin')->where('nip', $nip)->where('tgl_izin', $tgl_izin)->count();
        return $cek;
    }

    public function lokasi()
    {
        $nip = Auth::guard('karyawan')->user()->nip;
        $lok_kantor = DB::table('konfigurasi_lokasi')->where('id', 1)->first();

        return view('presensi.lokasi_user', compact('lok_kantor'));
    }
}
