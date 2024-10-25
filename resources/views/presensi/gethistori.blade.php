@if ($histori->isEmpty())
<div class="alert alert-outline-warning" style="display: flex; align-items: center; justify-content: center;">
    <p style="font-size: 1rem; margin: 0;">Data Belum Ada</p>
</div>

@endif
@foreach ($histori as $d)
<ul class="listview image-listview">
    <li>
        <div class="item">
            @php
                $path = Storage::url('uploads/absensi/'.$d->foto_in);
            @endphp
            <img src="{{ url($path)}}" alt="image" class="image">
            <div class="in d-flex justify-content-between align-items-center">
                <div>
                    <b>{{ date("d-m-Y", strtotime($d->tgl_presensi)) }}</b>
                </div>
                <div>
                    <span class="badge {{ $d->jam_in < '08:00' ? 'bg-success' : 'bg-danger' }}">{{ $d->jam_in }}</span>
                </div>
                <div>
                    @if($d->jam_out)
                        <span class="badge bg-primary">{{ $d->jam_out }}</span>
                    @else
                        <span class="badge bg-warning">Belum Absen</span>
                    @endif
                </div>
            </div>
            
            
        </div>
    </li>
</ul>
@endforeach