<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Cetak Laporan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">
    <style>
        @page { size: A4 landscape }
        #title { font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; }
        .tabelpresensi { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .tabelpresensi th, .tabelpresensi td { border: 1px solid #131212; padding: 5px; font-size: 12px; text-align: center; color: black; }
        .hadir-bg { background-color: rgb(1, 190, 1); color: black; }
        .absen-bg { background-color: rgb(255, 34, 34); color: black; }
        .izin-bg { background-color: yellow; color: black; }
    </style>
</head>

<body class="A4 landscape">
    <section class="sheet padding-10mm">
        <table style="width: 100%">
            <tr>
                <td style="width: 100px">
                    <img src="{{ asset('assets/img/logorokanhulu.png') }}" width="80" alt="">
                </td>
                <td>
                    <span id="title">
                        REKAP ABSENSI PEGAWAI <br>
                        PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }} <br>
                        KECAMATAN KEPENUHAN <br>
                    </span>
                    <span>Jl. Syekh Abdul Wahab Rokan, Kepenuhan Tengah, Kec. Kepenuhan, Kabupaten Rokan Hulu, Riau.</span>
                </td>
            </tr>
        </table>

        <table class="tabelpresensi">
            <tr>
                <th rowspan="2">NIP</th>
                <th rowspan="2">Nama Pegawai</th>
                <th rowspan="2">Jabatan</th>
                <th colspan="31">Tanggal</th>
                <th rowspan="2">Hadir</th>
                <th rowspan="2">Absen</th>
                <th rowspan="2">Izin</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= 31; $i++)
                    <th>{{ $i }}</th>
                @endfor
            </tr>
            @foreach ($rekap as $d)
            <tr>
                <td>{{ $d->nik }}</td>
                <td>{{ $d->nama_lengkap }}</td>
                <td>{{ $d->jabatan }}</td>
                
                <?php
                    $totalHadir = 0;
                    $totalAbsen = 0;
                    $totalIzin = 0;
                    $izinIds = explode(', ', $d->izin_ids); // Ambil array ID izin
                ?>
                
                @for ($i = 1; $i <= 31; $i++)
                    <?php
                        $tgl = "tgl_$i"; // Ambil nama kolom untuk tanggal
                        $status = $d->$tgl ?: 'A'; // Tampilkan 'A' jika kolom kosong
                        $class = '';

                        // Cek status untuk hadir dan absen
                        if ($status == 'H') {
                            $class = 'hadir-bg';
                            $totalHadir++;
                        } elseif ($status == 'A') {
                            $class = 'absen-bg';
                            $totalAbsen++;
                        }

                        // Cek apakah ada izin pada tanggal tertentu berdasarkan ID
                        $currentDate = sprintf('%04d-%02d-%02d', $tahun, $bulan, $i); // Format tanggal

                        // Logika untuk mengecek ID dan tanggal izin
                        $izinFound = false; // Variabel untuk menandakan apakah izin ditemukan
                        foreach ($izinIds as $izinId) {
                            $izinDetail = DB::table('pengajuan_izin')->where('id', $izinId)->first();

                            if ($izinDetail && $izinDetail->status_approved == 1 && date('Y-m-d', strtotime($izinDetail->tgl_izin)) === $currentDate) {
                                $status = 'I'; // Tampilkan 'I' untuk izin
                                $class = 'izin-bg';
                                $izinFound = true;
                                $totalIzin++; // Tambah total izin
                                break; // Keluar dari loop jika izin ditemukan
                            }
                        }
                    ?>
                    <td class="{{ $class }}">{{ $status }}</td>
                @endfor
                
                <td>{{ $totalHadir }}</td>
                <td>{{ $totalAbsen }}</td>
                <td>{{ $totalIzin }}</td>
            </tr>
            @endforeach
        </table>

        <table width="100%" style="margin-top:100px">
            <tr>
                <td></td>
                <td style="text-align: center">Kota Tengah {{ date('d-m-Y') }}</td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align:bottom" height="100">
                    <u>Muhammad Yassin SKM</u><br>
                    <i><b>Sekretaris Camat</b></i>
                </td>
                <td style="text-align: center; vertical-align:bottom">
                    <u>Gustia Hendri S.Sos M.Si</u><br>
                    <i><b>Camat Kepenuhan</b></i>
                </td>
            </tr>
        </table>
    </section>
</body>

</html>
