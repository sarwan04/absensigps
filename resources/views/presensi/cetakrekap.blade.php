<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Cetak Laporan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">
    <style>
        @page {
            size: A4 landscape;
        }

        #title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            font-weight: bold;
        }

        .tabelpresensi {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .tabelpresensi th,
        .tabelpresensi td {
            border: 1px solid #131212;
            padding: 8px;
            font-size: 12px;
            text-align: center;
            color: black;
        }

        .nama_lengkap {
            text-align: start !important;
        }


        .hadir-bg {
            background-color: rgb(1, 190, 1);
            color: black;
        }

        .absen-bg {
            background-color: rgb(255, 34, 34);
            color: black;
        }

        .izin-bg {
            background-color: yellow;
            color: black;
        }

        .sheet {
            page-break-after: always;
        }

        .page-break {
            page-break-before: always;
        }

        .print-buttons {
            margin-top: 10px;
            padding-right: 10mm;
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

        @media print {
            .tabelpresensi .libur {
                background: red !important;
            }

            .print-buttons {
                display: none;
            }

            .page-break {
                page-break-before: always;
            }
        }

        .footer-signatures {
            display: none;
            position: absolute;
            bottom: 100px;
            right: 0;
            width: auto;
            text-align: center;
        }

        .sheet:last-of-type .footer-signatures {
            display: block;
        }

        .footer-signatures td {
            text-align: center;
            vertical-align: bottom;
        }
    </style>
</head>

<body class="A4 landscape legal">
    <div class="print-buttons">
        <button onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 -2h-6a2 2 0 0 1 -2 -2z" />
            </svg>
            Print
        </button>
    </div>

    @foreach (array_chunk($rekap->toArray(), 15) as $chunk)
        <section class="sheet padding-10mm">
            <table style="width: 100%">
                <tr>
                    <td style="width: 100px">
                        <img src="{{ asset('assets/img/logorokanhulu.png') }}" width="80" alt="Logo">
                    </td>
                    <td>
                        <span id="title">
                            REKAP ABSENSI PEGAWAI <br>
                            PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }} <br>
                            KECAMATAN KEPENUHAN <br>
                        </span>
                        <span>Jl. Syekh Abdul Wahab Rokan, Kepenuhan Tengah, Kec. Kepenuhan, Kabupaten Rokan Hulu,
                            Riau.</span>
                    </td>
                </tr>
            </table>

            <table class="tabelpresensi">
                <tr>
                    <th rowspan="2">NIP</th>
                    <th rowspan="2">Nama Pegawai</th>
                    <th rowspan="2">Jabatan</th>
                    <th colspan="{{ cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun) }}">Tanggal</th>
                    <th rowspan="2">TH</th>
                    <th rowspan="2">TA</th>
                    <th rowspan="2">TI</th>
                </tr>
                <tr>
                    @for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun); $i++)
                        <th>{{ $i }}</th>
                    @endfor
                </tr>

                @foreach ($chunk as $d)
                    <tr>
                        <td>{{ $d->nip }}</td>
                        <td class="nama_lengkap">{{ $d->nama_lengkap }}</td>
                        <td>{{ $d->jabatan }}</td>

                        <?php
                        $totalHadir = 0;
                        $totalAbsen = 0;
                        $totalIzin = 0;
                        $izinIds = explode(', ', $d->izin_ids);
                        ?>

                        @for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun); $i++)
                            <?php
                            $tgl = "tgl_$i";
                            $status = $d->$tgl ?: 'A';
                            $class = '';
                            
                            if ($status == 'H') {
                                $class = 'hadir-bg';
                                $totalHadir++;
                            } elseif ($status == 'A') {
                                $class = 'absen-bg';
                                $totalAbsen++;
                            }
                            
                            $currentDate = sprintf('%04d-%02d-%02d', $tahun, $bulan, $i);
                            $izinFound = false;
                            foreach ($izinIds as $izinId) {
                                $izinDetail = DB::table('pengajuan_izin')->where('id', $izinId)->first();
                            
                                if ($izinDetail && $izinDetail->status_approved == 1 && date('Y-m-d', strtotime($izinDetail->tgl_izin)) === $currentDate) {
                                    $status = 'I';
                                    $class = 'izin-bg';
                                    $izinFound = true;
                                    $totalIzin++;
                                    break;
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

            <table width="100%" style="margin-top:100px;" class="footer-signatures">
                <tr>
                    <td style="width: 50%; text-align: center; font-weight: bold;">Kota Tengah, {{ date('d-m-Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; padding-top: 70px; vertical-align: bottom;">
                        <u>Gustia Hendri S.Sos M.Si</u><br>
                        <i><b>Camat Kepenuhan</b></i>
                    </td>
                </tr>
            </table>
        </section>
    @endforeach
</body>

</html>
