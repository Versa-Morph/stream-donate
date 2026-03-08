@extends('errors.layout')

@section('content')
<div class="err-glow"></div>
<div class="err-wrap">
    <div class="err-code">404</div>
    <div class="err-title">Halaman Tidak Ditemukan</div>
    <div class="err-msg">
        Halaman yang Anda cari tidak ada, mungkin sudah dipindahkan,
        atau URL yang dimasukkan tidak tepat.
    </div>
    <div class="err-actions">
        <a href="{{ url('/') }}" class="btn btn-primary">
            Ke Beranda
        </a>
        <a href="javascript:history.back()" class="btn btn-ghost">
            Kembali
        </a>
    </div>
</div>
@endsection
