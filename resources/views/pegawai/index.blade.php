@extends('layouts.admin.tabler')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Data Pegawai
                    </h2>
                </div>

            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    @if (Session::get('success'))
                                        <div class="alert alert-success">
                                            {{ Session::get('success') }}
                                        </div>
                                    @endif

                                    @if (Session::get('warning'))
                                        <div class="alert alert-warning">
                                            {{ Session::get('warning') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <a href="#" class="btn btn-primary" id="btnTambahpegawai">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 5l0 14" />
                                            <path d="M5 12l14 0" />
                                        </svg>
                                        Tambah Data
                                    </a>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <form action="/pegawai" method="GET">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <input type="text" name="nama_pegawai" id="nama_pegawai"
                                                        class="form-control" placeholder="Nama Pegawai"
                                                        value="{{ Request('nama_pegawai') }}">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <select name="kode_dept" id="kode_dept" class="form-select">
                                                        <option value="">Departemen</option>
                                                        @foreach ($departemen as $d)
                                                            <option
                                                                {{ Request('kode_dept') == $d->kode_dept ? 'selected' : '' }}
                                                                value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary"><svg
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                                            <path d="M21 21l-6 -6" />
                                                        </svg>Cari</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="text-center">
                                                <th>No</th>
                                                <th>Nip</th>
                                                <th>Nama</th>
                                                <th>Jabatan</th>
                                                <th>No.HP</th>
                                                <th>Foto</th>
                                                <th>Departemen</th>
                                                <th>Aksi</th>
                                            </tr>
                                        <tbody>
                                            @foreach ($pegawai as $d)
                                                @php
                                                    $path = Storage::url('uploads/pegawai/' . $d->foto);
                                                @endphp

                                                <tr class="align-middle">
                                                    <td>{{ $loop->iteration + $pegawai->firstItem() - 1 }}</td>
                                                    <td>{{ $d->nip }}</td>
                                                    <td>{{ $d->nama_lengkap }}</td>
                                                    <td>{{ $d->jabatan }}</td>
                                                    <td>{{ $d->no_hp }}</td>
                                                    <td
                                                        style="display: flex; justify-content: center; align-items: center;">
                                                        @if (empty($d->foto))
                                                            <img src="{{ asset('assets/img/nophoto.png') }}" class="avatar"
                                                                alt="" style="object-fit: cover;">
                                                        @else
                                                            <img src="{{ url($path) }}" class="avatar" alt=""
                                                                style="object-fit: cover;">
                                                        @endif
                                                    </td>
                                                    <td>{{ $d->nama_dept }}</td>
                                                    <td class="text-center">
                                                        <div class="btn-group">
                                                            <a href="#" class="edit btn btn-primary btn-md mr-2"
                                                                nip="{{ $d->nip }}"><svg
                                                                    xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                    <path
                                                                        d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                                    <path
                                                                        d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                                    <path d="M16 5l3 3" />
                                                                </svg></a>
                                                            <form action="/pegawai/{{ $d->nip }}/delete"
                                                                method="POST" style="margin-left: 5px">
                                                                @csrf
                                                                <a class="btn btn-danger btn-md delete-confirm">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                        height="24" viewBox="0 0 24 24"
                                                                        fill="currentColor"
                                                                        class="icon icon-tabler icons-tabler-filled icon-tabler-trash">
                                                                        <path stroke="none" d="M0 0h24v24H0z"
                                                                            fill="none" />
                                                                        <path
                                                                            d="M20 6a1 1 0 0 1 .117 1.993l-.117 .007h-.081l-.919 11a3 3 0 0 1 -2.824 2.995l-.176 .005h-8c-1.598 0 -2.904 -1.249 -2.992 -2.75l-.005 -.167l-.923 -11.083h-.08a1 1 0 0 1 -.117 -1.993l.117 -.007h16z" />
                                                                        <path
                                                                            d="M14 2a2 2 0 0 1 2 2a1 1 0 0 1 -1.993 .117l-.007 -.117h-4l-.007 .117a1 1 0 0 1 -1.993 -.117a2 2 0 0 1 1.85 -1.995l.15 -.005h4z" />
                                                                    </svg>
                                                                </a>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        </thead>
                                    </table>
                                    {{ $pegawai->links('vendor.pagination.bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal Input data Pegawai --}}
    <div class="modal modal-blur fade" id="modal-inputpegawai" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/pegawai/store" method="POST" id="frmpegawai" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-barcode">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 7v-1a2 2 0 0 1 2 -2h2" />
                                            <path d="M4 17v1a2 2 0 0 0 2 2h2" />
                                            <path d="M16 4h2a2 2 0 0 1 2 2v1" />
                                            <path d="M16 20h2a2 2 0 0 0 2 -2v-1" />
                                            <path d="M5 11h1v2h-1z" />
                                            <path d="M10 11l0 2" />
                                            <path d="M14 11h1v2h-1z" />
                                            <path d="M19 11l0 2" />
                                        </svg>
                                    </span>
                                    <input type="text" value="" id="nip" class="form-control"
                                        name="nip" placeholder="NIP" maxlength="18" required
                                        oninvalid="this.setCustomValidity('Nip tidak boleh kosong!')"
                                        oninput="this.setCustomValidity('')">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        </svg>
                                    </span>
                                    <input type="text" value="" id="nama_lengkap" class="form-control"
                                        name="nama_lengkap" placeholder="Nama Lengkap" maxlength="99" required
                                        oninvalid="this.setCustomValidity('Nama tidak boleh kosong!')"
                                        oninput="this.setCustomValidity('')">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-tie">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M12 22l4 -4l-2.5 -11l.993 -2.649a1 1 0 0 0 -.936 -1.351h-3.114a1 1 0 0 0 -.936 1.351l.993 2.649l-2.5 11l4 4z" />
                                            <path d="M10.5 7h3l5 5.5" />
                                        </svg>
                                    </span>
                                    <input type="text" value="" id="jabatan" class="form-control"
                                        name="jabatan" placeholder="Jabatan" maxlength="49" required
                                        oninvalid="this.setCustomValidity('Jabatan tidak boleh kosong!')"
                                        oninput="this.setCustomValidity('')">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-device-mobile">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M6 5a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2v-14z" />
                                            <path d="M11 4h2" />
                                            <path d="M12 17v.01" />
                                        </svg>
                                    </span>
                                    <input type="text" value="" id="no_hp" class="form-control"
                                        name="no_hp" placeholder="No HP" maxlength="12">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <input type="file" name="foto" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <select name="kode_dept" id="kode_dept" class="form-select" required
                                        oninvalid="this.setCustomValidity('Departemen harus di isi!')"
                                        oninput="this.setCustomValidity('')">
                                        <option value="">Departemen</option>
                                        @foreach ($departemen as $d)
                                            <option {{ Request('kode_dept') == $d->kode_dept ? 'selected' : '' }}
                                                value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <div class="form-group">
                                        <button class="btn btn-primary w-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M10 14l11 -11" />
                                                <path
                                                    d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                                            </svg>
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal edit data pegawai --}}

    <div class="modal modal-blur fade" id="modal-editpegawai" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="loadeditform">

                </div>

            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(document).ready(function() {
            // Menampilkan modal untuk tambah pegawai
            $("#btnTambahpegawai").click(function() {
                $("#modal-inputpegawai").modal("show");
            });

            // Menampilkan SweetAlert jika session 'success' ada
            @if (Session::has('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: "{{ Session::get('success') }}",
                    icon: 'success',
                    confirmButtonText: 'OK',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            // Menampilkan SweetAlert jika session 'warning' ada
            @if (Session::has('warning'))
                Swal.fire({
                    title: 'Gagal!',
                    text: "{{ Session::get('warning') }}",
                    icon: 'error',
                    confirmButtonText: 'OK',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            setTimeout(function() {
                $('.alert-success').fadeOut('slow');
            }, 3000);

            setTimeout(function() {
                $('.alert-warning').fadeOut('slow');
            }, 3000);

            // Menampilkan form edit pegawai dengan AJAX
            $(".edit").click(function() {
                var nip = $(this).attr('nip');
                $.ajax({
                    type: 'POST',
                    url: '/pegawai/edit',
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        nip: nip
                    },
                    success: function(respond) {
                        $("#loadeditform").html(respond);
                    }
                });
                $("#modal-editpegawai").modal("show");
            });

            // Konfirmasi hapus data pegawai
            $(".delete-confirm").click(function(e) {
                var form = $(this).closest('form');
                e.preventDefault();
                Swal.fire({
                    title: "Apakah Anda Yakin Data ini Mau di Hapus?",
                    text: "Jika Ya, Maka Data akan Terhapus Permanen",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, Hapus Saja!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

        });
    </script>
@endpush
