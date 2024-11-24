<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {


        $query = Pegawai::query();
        $query->select('pegawai.*', 'nama_dept');
        $query->join('departemen', 'pegawai.kode_dept', '=', 'departemen.kode_dept');
        $query->orderBy('nama_lengkap');

        if (!empty($request->nama_pegawai)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_pegawai . '%');
        }

        if (!empty($request->kode_dept)) {
            $query->where('pegawai.kode_dept', $request->kode_dept);
        }

        $pegawai = $query->paginate(10);

        $departemen = DB::table('departemen')->get();
        return view('pegawai.index', compact('pegawai', 'departemen'));
    }

    public function store(Request $request)
    {
        $nip = $request->nip;
        $nama_lengkap = $request->nama_lengkap;
        $jabatan = $request->jabatan;
        $no_hp = $request->no_hp;
        $kode_dept = $request->kode_dept;
        $password = Hash::make('12345');

        if ($request->hasFile('foto')) {
            $foto = $nip . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = null;
        }

        try {
            $data =  [
                'nip' => $nip,
                'nama_lengkap' => $nama_lengkap,
                'jabatan' => $jabatan,
                'no_hp' => $no_hp,
                'kode_dept' => $kode_dept,
                'foto' => $foto,
                'password' => $password,
            ];

            $simpan = DB::table('pegawai')->insert($data);

            if ($simpan) {
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/pegawai/";
                    $request->file('foto')->storeAs($folderPath, $foto);
                }

                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            }
        } catch (\Exception $e) {
            if ($e->getCode() == 23000) {
                $message = " Data dengan NIP " . $nip . " Sudah Ada";
            }
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan' . $message]);
        }
    }

    public function edit(Request $request)
    {
        $nip = $request->nip;
        $departemen = DB::table('departemen')->get();
        $pegawai = DB::table('pegawai')->where('nip', $nip)->first();

        return view('pegawai.edit', compact('departemen', 'pegawai'));
    }

    public function update($nip, Request $request)
    {
        $nip = $request->nip;
        $nama_lengkap = $request->nama_lengkap;
        $jabatan = $request->jabatan;
        $no_hp = $request->no_hp;
        $kode_dept = $request->kode_dept;
        $password = Hash::make('12345');
        $old_foto = $request->old_foto;

        if ($request->hasFile('foto')) {
            $foto = $nip . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $old_foto;
        }

        try {
            $data =  [
                'nama_lengkap' => $nama_lengkap,
                'jabatan' => $jabatan,
                'no_hp' => $no_hp,
                'kode_dept' => $kode_dept,
                'foto' => $foto,
                'password' => $password,
            ];

            $update = DB::table('pegawai')->where('nip', $nip)->update($data);

            if ($update) {
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/pegawai/";
                    $folderPathOld = "public/uploads/pegawai/" . $old_foto;
                    Storage::delete($folderPathOld);
                    $request->file('foto')->storeAs($folderPath, $foto);
                }

                return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Update']);
        }
    }

    public function delete($nip)
    {
        $delete = DB::table('pegawai')->where('nip', $nip)->delete();

        if ($delete) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Hapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Hapus']);
        }
    }
}
