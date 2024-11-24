<?php

namespace App\Http\Controllers;

use App\Models\Pengajuanizin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    public function create()
    {
        $hariini = date("Y-m-d");
        $nip = Auth::guard('karyawan')->user()->nip;
        $cek = DB::table('absensi')->where('tgl_absensi', $hariini)->where('nip', $nip)->count();
        $lok_kantor = DB::table('konfigurasi_lokasi')->where('id', 1)->first();

        // cek apakah sudah absen atau belum
        $alreadyCheckedOut = DB::table('absensi')
            ->where('tgl_absensi', $hariini)
            ->where('nip', $nip)
            ->whereNotNull('jam_out')
            ->exists();

        return view('absensi.create', compact('cek', 'alreadyCheckedOut', 'lok_kantor'));
    }

    public function store(Request $request)
    {
        $nip = Auth::guard('karyawan')->user()->nip;
        $tgl_absensi = date("Y-m-d");
        $jam = date('H:i:s');

        $izinDisetujui = DB::table('pengajuan_izin')
            ->where('nip', $nip)
            ->where('tgl_izin', $tgl_absensi)
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

        $cek = DB::table('absensi')->where('tgl_absensi', $tgl_absensi)->where('nip', $nip)->count();

        if ($cek > 0) {
            $ket = "out";
        } else {
            $ket = "in";
        }

        $image = $request->image;
        $folderPath = "public/uploads/absensi/";
        $formatName = $nip . "-" . $tgl_absensi . "-" . $ket;
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
                $update = DB::table('absensi')->where('tgl_absensi', $tgl_absensi)->where('nip', $nip)->update($data_pulang);
                if ($update) {
                    echo "success|Terimakasih, Hati-Hati di Jalan|out";
                    Storage::put($file, $image_base64);
                } else {
                    echo "error|Maaf Gagal Absen, Silahkan Coba Lagi|out";
                }
            } else {
                $data = [
                    'nip' => $nip,
                    'tgl_absensi' => $tgl_absensi,
                    'jam_in' => $jam,
                    'foto_in' => $fileName,
                    'lokasi_in' => $lokasi,
                ];

                $simpan = DB::table('absensi')->insert($data);
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
        $cek_in = DB::table('absensi')->where('tgl_absensi', $hariini)->where('nip', $nip)->whereNotNull('jam_in')->exists();
        $cek_out = DB::table('absensi')->where('tgl_absensi', $hariini)->where('nip', $nip)->whereNotNull('jam_out')->exists();

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
        return view('absensi.editprofile', compact('karyawan'));
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

        return view('absensi.histori', compact('namabulan'));
    }

    public function gethistori(Request  $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nip = Auth::guard('karyawan')->user()->nip;

        $histori = DB::table('absensi')
            ->whereRaw('MONTH(tgl_absensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_absensi)="' . $tahun . '"')
            ->where('nip', $nip)
            ->orderBy('tgl_absensi')
            ->get();

        return view('absensi.gethistori', compact('histori'));
    }

    public function izin()
    {
        $nip = Auth::guard('karyawan')->user()->nip;
        $dataizin = DB::table('pengajuan_izin')->where('nip', $nip)->get();
        return view('absensi.izin', compact('dataizin'));
    }

    public function buatizin()
    {

        return view('absensi.buatizin');
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
            return redirect('/absensi/izin')->with(['success' => 'Data Berhasil di Simpan']);
        } else {
            return redirect('/absensi/izin')->with(['error' => 'Data Gagal di Simpan']);
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
            return redirect('/absensi/izin')->with(['success' => 'Data Berhasil di Update']);
        } else {
            return redirect('/absensi/izin')->with(['error' => 'Data Gagal di Update']);
        }
    }

    public function editizin($id)
    {
        $izin = DB::table('pengajuan_izin')->where('id', $id)->first();
        return view('absensi.editizin', compact('izin'));
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
        return view('absensi.monitoring');
    }

    public function getabsensi(Request $request)
    {
        $tanggal = $request->tanggal;
        $absensi = DB::table('absensi')
            ->select('absensi.*', 'nama_lengkap', 'nama_dept')
            ->join('karyawan', 'absensi.nip', '=', 'karyawan.nip')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->where('tgl_absensi', $tanggal)
            ->get();

        return view('absensi.getabsensi', compact('absensi'));
    }

    public function tampilkanpeta(Request $request)
    {
        $id = $request->id;
        $absensi = DB::table('absensi')->where('id', $id)
            ->join('karyawan', 'absensi.nip', '=', 'karyawan.nip')
            ->first();
        return view('absensi.showmap', compact('absensi'));
    }

    public function laporan()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $karyawan = DB::table('karyawan')->orderBy('nama_lengkap')->get();
        return view('absensi.laporan', compact('namabulan', 'karyawan'));
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
        $absensi = DB::table('absensi')
            ->where('nip', $nip)
            ->whereRaw('MONTH(tgl_absensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_absensi)="' . $tahun . '"')
            ->orderBy('tgl_absensi')
            ->get();

        return view('absensi.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'karyawan', 'absensi'));
    }

    public function rekap()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('absensi.rekap', compact('namabulan',));
    }

    public function cetakrekap(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        // Mendefinisikan fields untuk query
        $fields = 'absensi.nip, karyawan.nama_lengkap, departemen.nama_dept';
        for ($i = 1; $i <= 31; $i++) {
            $fields .= ", MAX(IF(DAY(tgl_absensi) = $i, 
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

        $rekap = DB::table('absensi')
            ->join('karyawan', 'absensi.nip', '=', 'karyawan.nip')
            ->leftJoin('pengajuan_izin', function ($join) use ($bulan, $tahun) {
                $join->on('karyawan.nip', '=', 'pengajuan_izin.nip')
                    ->whereMonth('pengajuan_izin.tgl_izin', $bulan)
                    ->whereYear('pengajuan_izin.tgl_izin', $tahun);
            })
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                'absensi.nip',
                'karyawan.nama_lengkap',
                'karyawan.jabatan',
                'departemen.nama_dept',
                DB::raw($fields),
                DB::raw("GROUP_CONCAT(DISTINCT DATE_FORMAT(pengajuan_izin.tgl_izin, '%Y-%m-%d') SEPARATOR ', ') as tgl_izin"),
                DB::raw("GROUP_CONCAT(DISTINCT pengajuan_izin.status_approved SEPARATOR ', ') as status_approved"),
                DB::raw("GROUP_CONCAT(DISTINCT pengajuan_izin.id SEPARATOR ', ') as izin_ids")
            )
            ->whereMonth('absensi.tgl_absensi', $bulan)
            ->whereYear('absensi.tgl_absensi', $tahun)
            ->groupBy('absensi.nip', 'karyawan.nama_lengkap', 'departemen.nama_dept')
            ->get();

        return view('absensi.cetakrekap', compact('bulan', 'tahun', 'namabulan', 'rekap'));
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
        return view('absensi.izinsakit', compact('izinsakit'));
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

        return view('absensi.lokasi_user', compact('lok_kantor'));
    }
}
