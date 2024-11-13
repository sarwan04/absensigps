<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, viewport-fit=cover" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="theme-color" content="#000000" />
    <title>Smart Absen</title>
    <meta name="description" content="Mobilekit HTML Mobile UI Kit" />
    <meta name="keywords" content="bootstrap 4, mobile template, cordova, phonegap, mobile, html" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}" sizes="32x32" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/icon/192x192.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />

    <style>
        /* Hover effect for admin login link */
        .admin-login-link {
            transition: color 0.3s ease, text-decoration 0.3s ease;
        }

        .admin-login-link:hover {
            text-decoration: underline;
        }
    </style>

</head>

<body class="bg-white">
    <!-- loader -->
    <div id="loader">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
    <!-- * loader -->

    <!-- App Capsule -->
    <div id="appCapsule" class="pt-0">
        <div class="login-form mt-5">
            <div class="section">
                <img src="{{ asset('assets/img/login/login.avif') }}" alt="Login Image" class="form-image" />
            </div>
            <div class="section mt-1">
                <h1>Smart Absen</h1>
            </div>
            <div class="section mt-1 mb-5">
                @if (Session::has('warning'))
                    <div class="alert alert-outline-warning" id="error-message">
                        {{ Session::get('warning') }}
                    </div>
                @endif

                <!-- Button Login Admin di atas NIP -->
                <div class="d-flex justify-content-end">
                    <a href="/admin" class="text-muted admin-login-link">
                        Login Sebagai Admin
                    </a>
                </div>

                <form action="/proseslogin" method="POST">
                    @csrf
                    <div class="form-group boxed">
                        <div class="input-wrapper">
                            <input type="text" name="nik" class="form-control" id="nik" placeholder="NIP"
                                required oninvalid="this.setCustomValidity('Nip tidak boleh kosong!')"
                                oninput="this.setCustomValidity('')" />
                            <i class="clear-input">
                                <ion-icon name="close-circle"></ion-icon>
                            </i>
                        </div>
                    </div>

                    <div class="form-group boxed">
                        <div class="input-wrapper">
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Password" required
                                oninvalid="this.setCustomValidity('Password tidak boleh kosong!')"
                                oninput="this.setCustomValidity('')" />
                            <i class="clear-input">
                                <ion-icon name="close-circle"></ion-icon>
                            </i>
                        </div>
                    </div>

                    <div class="form-group boxed">
                        <button type="submit" class="btn btn-success btn-block btn-lg mt-2">
                            Login
                        </button>

                        <div class="form-links mt-2">
                            <div>
                                <a href="#" class="text-muted">Lupa Password?</a>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- * App Capsule -->

    <!-- ///////////// Js Files ////////////////////  -->
    <!-- Jquery -->
    <script src="{{ asset('assets/js/lib/jquery-3.4.1.min.js') }}"></script>
    <!-- Bootstrap-->
    <script src="{{ asset('assets/js/lib/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/bootstrap.min.js') }}"></script>
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.0.0/dist/ionicons/ionicons.js"></script>
    <!-- Owl Carousel -->
    <script src="{{ asset('assets/js/plugins/owl-carousel/owl.carousel.min.js') }}"></script>
    <!-- jQuery Circle Progress -->
    <script src="{{ asset('assets/js/plugins/jquery-circle-progress/circle-progress.min.js') }}"></script>
    <!-- Base Js File -->
    <script src="{{ asset('assets/js/base.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Menghilangkan pesan gagal setelah 3 detik
            setTimeout(function() {
                $('#error-message').fadeOut('slow');
            }, 3000);
        });
    </script>
</body>

</html>
