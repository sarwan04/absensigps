<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Cetak Laporan </title>

  <!-- Normalize or reset CSS with your favorite library -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

  <!-- Load paper.css for happy printing -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

  <!-- Set page size here: A5, A4 or A3 -->
  <!-- Set also "landscape" if you need -->
  <style>@page { size: A4 }
    #title {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 18px;
        font-weight: bold;
    }

    .tabeldatakaryawan {
        margin-top: 40px;
     }

     .tabeldatakaryawan tr td {
        padding: 5px;
     }

     .tabelpresensi {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
     }

     .tabelpresensi tr th {
        border: 1px solid #131212;
        padding: 8px;
        background-color: #dbdbdb;
        font-size: 10px;
     }

     .tabelpresensi tr td {
        border: 1px solid #131212;
        padding: 5px;
        font-size: 12px;
     }

     .foto {
        width: 40px; 
        height: 30px;
        object-fit: cover;
     }
  </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4 landscape legal">

  <!-- Each sheet element should have the class "sheet" -->
  <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
  <section class="sheet padding-10mm">

    <table style="width: 100%">
        <tr>

            <td style="width: 100px">
                <img src="{{ asset('assets/img/logorokanhulu.png')}}" width="80" alt="">
            </td>
            <td>
                <span id="title">
                    REKAP ABSENSI PEGAWAI <br>
                    PERIODE {{ strtoupper($namabulan[$bulan])}} {{ $tahun}} <br>
                    KECAMATAN KEPENUHAN <br>
                </span>
                <span>Jl. Syekh Abdul Wahab Rokan, Kepenuhan Tengah, Kec. Kepenuhan, Kabupaten Rokan Hulu, Riau.</span>
            </td>
        </tr>
    </table>

    <table class="tabelpresensi" >
        <tr>
            <th rowspan="2" >NIK</th>
            <th rowspan="2">Nama Karyawan</th>
            <th colspan="31">Tanggal</th>
            <th rowspan="2">TH</th>
            <th rowspan="2">TK</th>
        </tr>
        <tr>
            <?php
            for($i=1; $i<=31; $i++){
            ?>
            <th>{{ $i }}</th> 
            <?php
            } 
            ?>
        </tr>
        @foreach ($rekap as $d)
        <tr>
            <td>{{ $d->nik }}</td>
            <td>{{ $d->nama_lengkap }}</td>

            <?php
            $totalhadir = 0;
            $totalterlambat = 0;
            for($i=1; $i<=31; $i++){
                $tgl = "tgl_".$i;             
                if(empty($d->$tgl)){
                    $hadir = ['',''];
                    $totalhadir += 0;
                }else{
                    $hadir = explode("-",$d->$tgl);
                    $totalhadir += 1;
                    if($hadir[0] > "08:00:00"){
                        $totalterlambat +=1;
                    }
                }
            ?>

            <td>
               <span style="color:{{ $hadir[0]>"08:00:00" ? "red" : ""}}"> {{ $hadir[0] }}</span> <br>
               <span style="color:{{ $hadir[1]>"15:00:00" ? "red" : ""}}"> {{ $hadir[1] }}</span> <br>
            </td> 

            <?php
            } 
            ?>
            <td>{{ $totalhadir}}</td>
            <td>{{ $totalterlambat}}</td>
        </tr>
        @endforeach
    </table>


    <table width="100%" style="margin-top:100px">
        <tr>
            <td></td>
            <td style="text-align: center">Kota Tengah {{ date('d-m-Y')}}</td>
        </tr>
        <tr>
            <td style="text-align: center; vertical-align:bottom"  height="100" >
                <u>Yurida wilis S.Kom M.Si</u><br>
                <i><b>Kasi Pelum</b></i>
            </td>

            <td style="text-align: center; vertical-align:bottom" >
                <u>Gustia Hendri S.Sos M.Si</u><br>
                <i><b>Camat Kepenuhan</b></i>
            </td>
        </tr>
    </table>


  </section>

</body>

</html>