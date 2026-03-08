@extends('errors.layout')

@section('content')
<div class="err-glow danger"></div>
<div class="err-wrap">
    <div class="err-code danger">500</div>
    <div class="err-title">Terjadi Kesalahan Server</div>
    <div class="err-msg">
        Kami mengalami kendala teknis. Tim kami sudah mendapat notifikasi
        dan sedang menangani masalah ini. Mohon coba lagi dalam beberapa saat.
    </div>
    <div class="err-actions">
        <a href="javascript:location.reload()" class="btn btn-primary">
            Coba Lagi
        </a>
        <a href="{{ url('/') }}" class="btn btn-ghost">
            Ke Beranda
        </a>
    </div>
</div>
@endsection
