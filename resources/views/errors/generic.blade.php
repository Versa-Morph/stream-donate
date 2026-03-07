@extends('errors.layout')

@section('content')
<div class="err-glow"></div>
<div class="err-wrap">
    <div class="err-code">{{ $status ?? '?' }}</div>
    <div class="err-title">Terjadi Kesalahan</div>
    <div class="err-msg">
        {{ $message ?? 'Terjadi kesalahan yang tidak terduga. Mohon coba lagi.' }}
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
