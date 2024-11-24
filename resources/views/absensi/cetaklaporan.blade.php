<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Cetak Laporan</title>

    <!-- Normalize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

    <!-- Load paper.css for printing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

    <!-- Load html2pdf.js library for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

    <!-- Set page size here: A4 -->
    <style>
        @page {
            size: A4
        }

        #title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }

        .tabeldatapegawai {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .tabeldatapegawai tr td {
            padding: 5px;
        }

        .tabelabsensi {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            text-align: center;
        }

        .tabelabsensi tr th,
        .tabelabsensi tr td {
            border: 1px solid #131212;
            padding: 5px;
            font-size: 12px;
            vertical-align: middle;
        }

        .tabelabsensi tr th {
            background-color: #dbdbdb;
            font-weight: bold;
        }

        .tabelabsensi .libur {
            background: red;
            /* Warna untuk hari libur */
        }

        .foto {
            width: 40px;
            height: 30px;
            object-fit: cover;
        }

        .sheet {
            page-break-after: always;
        }

        .page-break {
            page-break-before: always;
        }

        /* Styling for buttons */
        .print-buttons {
            margin-top: 10px;
            padding-right: 30mm;
            display: flex;
            justify-content: flex-end;
        }

        .print-buttons button {
            padding: 10px 20px;
            margin: 5px;
            font-size: 18px;
            cursor: pointer;
            background-color: #0054a6;
            color: white;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
        }

        .print-buttons button:hover {
            background-color: #0088cc;
        }

        .print-buttons button .icon {
            margin-right: 8px;
        }

        /* Hide print buttons when printing */
        @media print {
            .tabelabsensi .libur {
                background: red !important;
                /* Pastikan tetap merah saat dicetak */
            }

            .print-buttons {
                display: none;
                /* Sembunyikan tombol cetak saat mencetak */
            }
        }
    </style>
</head>

<body class="A4">
    <!-- Print and Export Buttons -->
    <div class="print-buttons">
        <button onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
            </svg>
            Print
        </button>
    </div>

    <section class="sheet padding-10mm" id="report">
        <table style="width: 100%">
            <tr>
                <td style="width: 100px">
                    <img src="{{ asset('assets/img/logorokanhulu.png') }}" width="80" alt="">
                </td>
                <td>
                    <span id="title">
                        LAPORAN ABSENSI PEGAWAI <br>
                        PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }} <br>
                        KECAMATAN KEPENUHAN <br>
                    </span>
                    <span>Jl. Syekh Abdul Wahab Rokan, Kepenuhan Tengah, Kec. Kepenuhan, Kabupaten Rokan Hulu,
                        Riau.</span>
                </td>
            </tr>
        </table>

        <table class="tabeldatapegawai">
            <tr>
                <td rowspan="6">
                    @php
                        $path = Storage::url('uploads/pegawai/' . $pegawai->foto);
                        $pathImageDefault = Storage::url('uploads/nophoto/nophoto.png');
                    @endphp
                    <img src="{{ $pegawai->foto ? url($path) : url($pathImageDefault) }}" alt="Deskripsi Gambar"
                        style="width: 150px; height: 150px; object-fit: cover;">
                </td>

            </tr>
            <tr>
                <td>NIP</td>
                <td>:</td>
                <td>{{ $pegawai->nip }}</td>
            </tr>
            <tr>
                <td>Nama pegawai</td>
                <td>:</td>
                <td>{{ $pegawai->nama_lengkap }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $pegawai->jabatan }}</td>
            </tr>
            <tr>
                <td>Departemen</td>
                <td>:</td>
                <td>{{ $pegawai->nama_dept }}</td>
            </tr>
            <tr>
                <td>No. HP</td>
                <td>:</td>
                <td>{{ $pegawai?->no_hp }}</td>
            </tr>
        </table>

        <table class="tabelabsensi">
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Foto</th>
                <th>Jam Pulang</th>
                <th>Foto</th>
                <th>Keterangan</th>
            </tr>
            @foreach ($absensi as $d)
                @php
                    $path_in = Storage::url('uploads/absensi/' . $d->foto_in);
                    $path_out = Storage::url('uploads/absensi/' . $d->foto_out);
                    $tanggal = date('d-m-Y', strtotime($d->tgl_absensi));
                    $dayOfWeek = date('N', strtotime($d->tgl_absensi)); // Mendapatkan hari dalam bentuk angka (1=Senin, 7=Minggu)
                @endphp
                <tr class="{{ $dayOfWeek == 6 || $dayOfWeek == 7 ? 'libur' : '' }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $tanggal }}</td>
                    <td>{{ $d->jam_in }}</td>
                    <td><img src="{{ url($path_in) }}" alt="" class="foto"></td>
                    <td>{{ $d->jam_out != null ? $d->jam_out : 'Belum Absen' }}</td>
                    <td>
                        @if ($d->jam_out != null)
                            <img src="{{ url($path_out) }}" alt="" class="foto">
                        @else
                            <img src="{{ asset('assets/img/nophoto.png') }}" alt="" class="foto">
                        @endif
                    </td>
                    <td>
                        @if ($d->jam_in > '08:00')
                            Terlambat
                        @else
                            Tepat waktu
                        @endif
                    </td>
                </tr>
                @if ($loop->iteration == 15)
        </table>
    </section>

    <section class="sheet padding-10mm page-break">
        <table class="tabelabsensi">
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Foto</th>
                <th>Jam Pulang</th>
                <th>Foto</th>
                <th>Keterangan</th>
            </tr>
            @endif
            @endforeach
        </table>
    </section>
</body>

</html>
