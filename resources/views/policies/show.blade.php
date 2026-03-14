@props(['title' => 'Policy', 'slug' => ''])

<x-app-layout :title="$title">
    <div style="max-width:900px;margin:0 auto;padding:32px 28px">
        <a href="{{ route('policies.index') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--text-3);font-size:13px;margin-bottom:20px;text-decoration:none">
            <span class="iconify" data-icon="solar:alt-arrow-left-bold"></span>
            Kembali ke Kebijakan
        </a>

        <h1 style="font-family:'Space Grotesk',sans-serif;font-size:26px;font-weight:700;margin-bottom:24px;color:var(--text)">{{ $title }}</h1>

        @if($slug)
            @include('policies.content.' . $slug)
        @else
            <p style="color:var(--text-2)">Kebijakan tidak ditemukan.</p>
        @endif

        <div style="margin-top:40px;padding-top:24px;border-top:1px solid var(--border)">
            <p style="font-size:12px;color:var(--text-3)">Terakhir diperbarui: Maret 2026</p>
        </div>
    </div>
</x-app-layout>
