@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Smart Absen</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->

    <style>
        .webcam-capture,
        .webcam-capture video,
        #map {
            display: block;
            width: 100% !important;
            height: auto !important;
            border-radius: 15px;
            min-height: 100%;
        }

        .wrapper {
            display: flex;
            justify-content: space-around;
            align-items: center;
            width: 100%;
        }

        .leaflet-container .leaflet-control-attribution {
            display: none;
        }

        /* Gaya khusus untuk iPad dan tablet */
        @media (max-width: 810px) and (min-width: 768px) {
            .row {
                justify-content: center;
            }

            .col-md-5 {
                width: 100%;
                margin: 0;
            }

            .webcam-capture,
            #map {
                min-height: 300px;
            }

            .btn {
                width: calc(100% - 40px);
                min-height: 50px;
                font-size: 16px;
                padding: 10px;
                margin: 10px 20px;
            }
        }

        /* Gaya untuk Mobile */
        @media (max-width: 768px) {
            .row {
                flex-direction: column;
                margin-top: 20px;
                align-items: center;
            }

            .col-md-5 {
                width: calc(100% - 40px);
                margin: 0 20px;
                margin-bottom: 20px;
            }

            .webcam-capture {
                min-height: 180px;
            }

            #map {
                min-height: 180px;
                margin-top: 10px;
            }

            .btn {
                font-size: 16px;
                padding: 10px;
            }

            .alert {
                font-size: 14px;
            }

            .appHeader {
                padding: 10px;
            }

            .pageTitle {
                font-size: 20px;
            }
        }

        /* Opsional: Perangkat yang lebih kecil */
        @media (max-width: 576px) {
            .btn {
                font-size: 14px;
                padding: 8px;
            }

            .col-10 {
                width: calc(100% - 20px);
            }

            .pageTitle {
                font-size: 18px;
            }
        }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('content')
    <div class="row justify-content-center" style="margin-top: 70px">
        <div class="col-md-5">
            <input type="hidden" id="lokasi">
            <div class="webcam-capture"></div>
        </div>
        <div class="col-md-5">
            <div id="map"></div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-10 mx-auto">
            @if ($cek > 0 && $alreadyCheckedOut)
                <p class="alert alert-success mb-2 mt-1">Anda sudah absen hari ini</p>
                <button id="takeabsen" class="btn btn-secondary btn-block" disabled>
                    <ion-icon name="camera-outline"></ion-icon>
                    Absen
                </button>
            @elseif ($cek > 0)
                <button id="takeabsen" class="btn btn-danger btn-block">
                    <ion-icon name="camera-outline"></ion-icon>
                    Absen Pulang
                </button>
            @else
                <button id="takeabsen" class="btn btn-primary btn-block">
                    <ion-icon name="camera-outline"></ion-icon>
                    Absen Masuk
                </button>
            @endif
        </div>
    </div>

    <audio id="notifikasi_in">
        <source src="{{ asset('assets/sound/notifikasi_in.mp3') }}" type="audio/mpeg">
    </audio>

    <audio id="notifikasi_out">
        <source src="{{ asset('assets/sound/notifikasi_out.mp3') }}" type="audio/mpeg">
    </audio>

    <audio id="radius_sound">
        <source src="{{ asset('assets/sound/radius.mp3') }}" type="audio/mpeg">
    </audio>
@endsection

@push('myscript')
    <script>
        var notifikasi_in = document.getElementById('notifikasi_in');
        var notifikasi_out = document.getElementById('notifikasi_out');
        var radius_sound = document.getElementById('radius_sound');

        // Setup Webcam
        Webcam.set({
            height: 480,
            width: 640,
            image_format: 'jpeg',
            jpeg_quality: 80
        });

        Webcam.attach('.webcam-capture');

        var lokasi = document.getElementById('lokasi');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
        }

        function successCallback(position) {
            lokasi.value = position.coords.latitude + "," + position.coords.longitude;

            // Setup Map
            var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 16);
            var lokasi_kantor = "{{ $lok_kantor->lokasi_kantor }}";
            var lok = lokasi_kantor.split(",");
            var lat_kantor = lok[0];
            var long_kantor = lok[1];
            var radius = "{{ $lok_kantor->radius }}";

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            var marker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);

            // Lokasi Kantor
            var circle = L.circle([lat_kantor, long_kantor], {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.5,
                radius: radius
            }).addTo(map);
        }

        function errorCallback() {
            console.error("Geolocation error");
        }

        // Absen Logic
        $('#takeabsen').click(function(e) {
            Webcam.snap(function(uri) {
                image = uri;
            });

            var lokasi = $('#lokasi').val();
            $.ajax({
                type: 'POST',
                url: '/presensi/store',
                data: {
                    _token: "{{ csrf_token() }}",
                    image: image,
                    lokasi: lokasi
                },
                cache: false,
                success: function(respond) {
                    var status = respond.split("|");
                    if (status[0] === "success") {
                        if (status[2] === "in") {
                            notifikasi_in.play();
                        } else {
                            notifikasi_out.play();
                        }
                        Swal.fire({
                            title: 'Berhasil!',
                            text: status[1],
                            icon: 'success'
                        });
                        setTimeout(() => {
                            location.href = '/dashboard';
                        }, 3000);
                    } else if (status[0] === "warning") {
                        Swal.fire({
                            title: 'Warning!',
                            text: status[1],
                            icon: 'warning'
                        });
                        if (status[2] === "radius") {
                            radius_sound.play();
                        }
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: status[1],
                            icon: 'error'
                        });
                    }
                }
            });
        });


        $(document).ready(function() {
            // Menghilangkan pesan gagal setelah 3 detik
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 3000);
        });
    </script>
@endpush
