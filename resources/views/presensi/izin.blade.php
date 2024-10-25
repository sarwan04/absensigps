@extends('layouts.presensi')

@section('header')
{{-- App Header --}}
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Data Izin / Sakit</div>
    <div class="right"></div>
</div>
{{-- end App Header --}}
@endsection

@section('content')
<div class="row" style="margin-top: 70px">
    <div class="col">
        @php
        $messagesuccess = Session::get('success');
        $messageerror = Session::get('error');
        @endphp
        @if(Session::get('success'))
            <div class="alert alert-success">
                {{$messagesuccess}}
            </div>
        @endif

        @if(Session::get('error'))
            <div class="alert alert-danger">
                {{$messageerror}}
            </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col">
        @foreach ($dataizin as $d)
            <ul class="listview image-listview">
                <li>
                    <div class="item">
                        <div class="in">
                            <div>
                                <b>{{ date("d-m-Y", strtotime($d->tgl_izin)) }} ({{ $d->status == "s" ? "Sakit" : "Izin" }})</b>
                                <br>
                                <small class="text-muted">{{ $d->keterangan }}</small>
                            </div>

                            <div class="btn-group">
                                <div class="mr-2">
                                    @if($d->status_approved == 0)
                                        <span class="badge bg-warning">Menunggu</span>
                                    @elseif($d->status_approved == 1)
                                        <span class="badge bg-success">Di Setujui</span>
                                    @elseif($d->status_approved == 2)
                                        <span class="badge bg-danger">Di Tolak</span>
                                    @endif
                                </div>
                                    <div>
                                        <a href="#" class="edit btn btn-info btn-sm mr-2" id="{{ $d->id }}">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="18"  height="18"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                        </a>
                                    </div>
                                    <form action="{{ url('/presensi/deleteizin/'.$d->id) }}" method="POST" class="delete-form" style="display: inline;">
                                        @csrf
                                        <button type="button" class="btn btn-danger btn-sm mr-1 delete-confirm">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="18"  height="18"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 6a1 1 0 0 1 .117 1.993l-.117 .007h-.081l-.919 11a3 3 0 0 1 -2.824 2.995l-.176 .005h-8c-1.598 0 -2.904 -1.249 -2.992 -2.75l-.005 -.167l-.923 -11.083h-.08a1 1 0 0 1 -.117 -1.993l.117 -.007h16z" /><path d="M14 2a2 2 0 0 1 2 2a1 1 0 0 1 -1.993 .117l-.007 -.117h-4l-.007 .117a1 1 0 0 1 -1.993 -.117a2 2 0 0 1 1.85 -1.995l.15 -.005h4z" /></svg>
                                        </button>
                                    </form>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        @endforeach
    </div>
</div>


<div class="fab-button bottom-right" style="margin-bottom: 70px">
    <a href="/presensi/buatizin" class="fab">
        <ion-icon name="add-outline"></ion-icon>
    </a>
</div>
@endsection

@push('myscript')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Confirmation before deletion
    document.querySelectorAll('.delete-confirm').forEach(button => {
      button.addEventListener('click', function (e) {
        e.preventDefault();
        const form = this.closest('.delete-form');
        if (confirm("Are you sure you want to delete this record?")) {
          form.submit();
        }
      });
    });
  });

    $(function(){
        // Menghilangkan pesan sukses setelah 3 detik
        setTimeout(function() {
            $('.alert-success').fadeOut('slow');
        }, 3000);

        // Menghilangkan pesan gagal setelah 3 detik
        setTimeout(function() {
            $('.alert-danger').fadeOut('slow');
        }, 3000);
    });
</script>
@endpush
