<x-app-layout>
@push('styles')
<style>
.profile-wrap{padding:28px;max-width:680px;margin:0 auto;display:flex;flex-direction:column;gap:20px}

/* section cards */
.profile-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;animation:slideUp .4s ease both}
.profile-card:nth-child(1){animation-delay:.05s;opacity:0;animation-fill-mode:forwards}
.profile-card:nth-child(2){animation-delay:.1s;opacity:0;animation-fill-mode:forwards}
.profile-card:nth-child(3){animation-delay:.15s;opacity:0;animation-fill-mode:forwards}

.profile-card-header{display:flex;align-items:center;gap:12px;padding:20px 24px 16px;border-bottom:1px solid var(--border)}
.profile-card-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.profile-card-icon .iconify{font-size:20px}
.profile-card-icon.brand{background:rgba(124,108,252,.12)}
.profile-card-icon.brand .iconify{color:var(--brand-light)}
.profile-card-icon.green{background:rgba(34,211,160,.10)}
.profile-card-icon.green .iconify{color:var(--green)}
.profile-card-icon.red{background:rgba(244,63,94,.10)}
.profile-card-icon.red .iconify{color:#f43f5e}
.profile-card-title{font-family:'Space Grotesk',sans-serif;font-size:16px;font-weight:700}
.profile-card-sub{font-size:12px;color:var(--text-3);margin-top:2px}

.profile-card-body{padding:24px}
</style>
@endpush

<div class="profile-wrap">

    {{-- Card 1: Profile Info --}}
    <div class="profile-card">
        <div class="profile-card-header">
            <div class="profile-card-icon brand">
                <span class="iconify" data-icon="solar:user-bold-duotone"></span>
            </div>
            <div>
                <div class="profile-card-title">Informasi Profil</div>
                <div class="profile-card-sub">Perbarui nama dan alamat email akun kamu</div>
            </div>
        </div>
        <div class="profile-card-body">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    {{-- Card 2: Password --}}
    <div class="profile-card">
        <div class="profile-card-header">
            <div class="profile-card-icon green">
                <span class="iconify" data-icon="solar:lock-password-bold-duotone"></span>
            </div>
            <div>
                <div class="profile-card-title">Ubah Password</div>
                <div class="profile-card-sub">Gunakan password panjang dan unik untuk keamanan akun</div>
            </div>
        </div>
        <div class="profile-card-body">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    {{-- Card 3: Delete Account --}}
    <div class="profile-card">
        <div class="profile-card-header">
            <div class="profile-card-icon red">
                <span class="iconify" data-icon="solar:shield-warning-bold-duotone"></span>
            </div>
            <div>
                <div class="profile-card-title">Hapus Akun</div>
                <div class="profile-card-sub">Tindakan ini tidak dapat dibatalkan</div>
            </div>
        </div>
        <div class="profile-card-body">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>
</x-app-layout>
