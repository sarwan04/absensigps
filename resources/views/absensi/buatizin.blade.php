@extends('layouts.absensi')

@section('header')
    {{-- Materialize css date picker  --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
    <style>
        .datepicker-modal {
            max-height: 430px !important;
        }

        .datepicker-date-display {
            background-color: #0f3a7e !important;
        }

        .btn-flat {
            color: #0f3a7e !important;
        }
    </style>

    {{--  App Header --}}
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Form Izin</div>
        <div class="right"></div>
    </div>
    {{-- end App Header --}}
@endsection

@section('content')
    <div class="row" style="margin-top: 70px">
        <div class="col">
            <form method="POST" action="/absensi/storeizin" id="formizin" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <input type="text" id="tgl_izin" name="tgl_izin" class="form-control datepicker"
                        placeholder="Tanggal">
                </div>

                <div class="form-group">
                    <select name="status" id="status" class="form-control">
                        <option value="">Izin / Sakit</option>
                        <option value="i">Izin</option>
                        <option value="s">Sakit</option>
                        <option value="s">Lainnya</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label for="bukti_izin" class="form-label" style="font-size: 14px;">Unggah Bukti Izin</label>
                    <input class="form-control" type="file" id="bukti_izin" name="bukti_izin"
                        accept=".jpg,.jpeg,.png,.pdf">
                </div>

                <div class="form-group">
                    <textarea name="keterangan" id="keterangan" cols="30" rows="5" class="form-control" placeholder="Keterangan"
                        maxlength="255" oninput="updateCharCount()"></textarea>
                    <small id="charCount">255 karakter tersisa</small>
                </div>

                <div class="form-group">
                    <button class="btn btn-primary w-100">Kirim</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        var currYear = (new Date()).getFullYear();
        $(document).ready(function() {
            $(".datepicker").datepicker({
                format: "yyyy-mm-dd"
            });
        });

        $("#tgl_izin").change(function(e) {
            var tgl_izin = $(this).val();
            $.ajax({
                type: 'POST',
                url: '/absensi/cekpengajuanizin',
                data: {
                    _token: "{{ csrf_token() }}",
                    tgl_izin: tgl_izin
                },
                cache: false,
                success: function(respond) {
                    if (respond == 1) {
                        Swal.fire({
                            title: 'Opps !',
                            text: 'Anda Sudah Izin pada Tanggal Tersebut',
                            icon: 'warning'
                        }).then((result) => {
                            $("#tgl_izin").val("");
                        });
                    }
                }
            })
        })

        $("#formizin").submit(function() {

            var tgl_izin = $("#tgl_izin").val();
            var status = $("#status").val();
            var keterangan = $("#keterangan").val();

            if (tgl_izin === "") {
                Swal.fire({
                    title: 'Opps !',
                    text: 'Tanggal Harus di Isi',
                    icon: 'warning'
                });
                return false;
            } else if (status === "") {
                Swal.fire({
                    title: 'Opps !',
                    text: 'Status Harus di Isi',
                    icon: 'warning'
                });
                return false;
            } else if (keterangan === "") {
                Swal.fire({
                    title: 'Opps !',
                    text: 'Keterangan Harus di Isi',
                    icon: 'warning'
                });
                return false;
            }

        });

        function updateCharCount() {
            const maxChars = 255;
            const textArea = document.getElementById('keterangan');
            const charCount = document.getElementById('charCount');
            const remainingChars = maxChars - textArea.value.length;
            charCount.textContent = `${remainingChars} karakter tersisa`;
        }
    </script>
@endpush