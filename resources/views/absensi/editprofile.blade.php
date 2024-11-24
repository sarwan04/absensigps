@extends('layouts.absensi')

@section('header')
    {{-- App Header --}}
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Edit Profile</div>
        <div class="right"></div>
    </div>
    {{-- end App Header --}}
@endsection

@section('content')
    <form style="margin-top: 4rem" action="/absensi/{{ $pegawai->nip }}/updateprofile" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="col">
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <input type="text" class="form-control" name="nama_lengkap" value="{{ $pegawai->nama_lengkap }}"
                        placeholder="Nama Lengkap" autocomplete="off" maxlength="100" id="namaLengkapInput">
                    <small id="charWarningNama" style="color: red; display: none;">Maksimal 100 karakter!</small>
                </div>
            </div>

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <input type="text" class="form-control" name="no_hp" value="{{ $pegawai->no_hp }}"
                        placeholder="No. HP" autocomplete="off" maxlength="14" id="noHpInput">
                    <small id="charWarningnoHp" style="color: red; display: none;">Maksimal 14 karakter!</small>
                </div>
            </div>

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <input type="password" class="form-control" name="password" placeholder="Password" autocomplete="off"
                        minlength="5" id="passwordInput">
                    <small id="passwordWarning" style="color: red; display: none;">Password harus memiliki minimal 5
                        karakter!</small>
                </div>
            </div>

            <div class="custom-file-upload" id="fileUpload1">
                <input type="file" name="foto" id="fileuploadInput" accept=".png, .jpg, .jpeg">
                <label for="fileuploadInput" style="cursor: pointer">
                    <span>
                        <strong>
                            <ion-icon name="cloud-upload-outline" role="img" class="md hydrated"
                                aria-label="cloud upload outline"></ion-icon>
                            <i>Tap to Upload</i>
                        </strong>
                    </span>
                </label>
            </div>

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <button type="submit" class="btn btn-primary btn-block">
                        <ion-icon name="refresh-outline"></ion-icon>
                        Update
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('myscript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (Session::has('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "Data berhasil di update",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (Session::has('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: "Data gagal di update",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(function(element) {
                    element.style.transition = "opacity 2s ease";
                    element.style.opacity = "0";

                    setTimeout(function() {
                        element.remove();
                    }, 1000);
                });
            }, 3000);
        });

        const inputs = [{
                id: 'namaLengkapInput',
                maxChars: 100,
                warningId: 'charWarningNama'
            },
            {
                id: 'noHpInput',
                maxChars: 14,
                warningId: 'charWarningnoHp'
            },
            {
                id: 'passwordInput',
                minChars: 5,
                warningId: 'passwordWarning'
            }
        ];

        inputs.forEach(input => {
            const inputElement = document.getElementById(input.id);
            const warningMessage = document.getElementById(input.warningId);

            inputElement.addEventListener('input', function() {
                if (input.minChars) {
                    warningMessage.style.display = this.value.length < input.minChars ? 'block' : 'none';
                } else {
                    warningMessage.style.display = this.value.length >= input.maxChars ? 'block' : 'none';
                }
            });

            inputElement.addEventListener('focus', function() {
                warningMessage.style.display = 'none';
            });
        });
    </script>
@endpush
