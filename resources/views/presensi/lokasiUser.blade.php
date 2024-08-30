@extends('layouts.presensi')

@section('header')
<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Lokasi</div>
    <div class="right"></div>
</div>
<!-- * App Header -->  
<style>
    #map {
        margin-top: 50px;
        height: 100vh;
        width: 100%;
    }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('content')

<div class="row mt-2">
    <div class="col">
        <div id="map"></div>
    </div>
</div>

@endsection

@push('myscript')
<script>
// Inisialisasi peta
var map = L.map('map').setView([0, 0], 13); 

// Tambahkan tile layer (peta dasar)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
}).addTo(map);

// Meminta lokasi pengguna
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        var lokasi_kantor = "{{ $lok_kantor->lokasi_kantor}}";
        var lok = lokasi_kantor.split(",");
        var lat_kantor = lok[0];
        var long_kantor = lok[1];
        var radius = "{{ $lok_kantor->radius }}";
        var userLat = position.coords.latitude;
        var userLng = position.coords.longitude;

        map.setView([userLat, userLng], 16);

        L.marker([userLat, userLng]).addTo(map)
            .bindPopup('Lokasi Anda Saat Ini')
            .openPopup();
        
         // Lokasi Kantor
         var circle = L.circle([lat_kantor, long_kantor], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: radius
        }).addTo(map);

    }, function(error) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal Mengakses Lokasi',
            text: 'Lokasi tidak bisa diakses: ' + error.message,
        });
    });
} else {
    Swal.fire({
        icon: 'warning',
        title: 'Geolocation Tidak Didukung',
        text: 'Geolocation tidak didukung oleh browser Anda.',
    });
}
</script>
@endpush

