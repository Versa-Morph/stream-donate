@extends('errors.layout')

@section('content')
<div class="err-glow warn"></div>
<div class="err-wrap">
    <div class="err-code warn">429</div>
    <div class="err-title">Terlalu Banyak Permintaan</div>
    <div class="err-msg">
        Anda melakukan terlalu banyak permintaan dalam waktu singkat.
        Mohon tunggu sebentar kemudian coba lagi.
    </div>
    <div class="err-actions">
        <a href="javascript:history.back()" class="btn btn-primary">
            Kembali
        </a>
    </div>
</div>
@endsection
