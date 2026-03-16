{{--
    Partial: OBS Setup Guide
    Di-include di setiap tab widget di Widget Studio.
    Tidak menerima parameter — hanya menampilkan langkah-langkah statis.
--}}
<ol style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:12px;">

    <li style="display:flex;align-items:flex-start;gap:12px;">
        <span style="flex-shrink:0;width:24px;height:24px;border-radius:8px;background:rgba(124,108,252,.12);border:1px solid rgba(124,108,252,.22);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:var(--brand-light);margin-top:1px">1</span>
        <span style="font-size:13px;color:var(--text-2);line-height:1.6">
            Buka <strong style="color:var(--text)">OBS Studio</strong>, klik <strong style="color:var(--text)">+</strong> di panel <em>Sources</em> dan pilih
            <strong style="color:var(--text)">Browser</strong>.
        </span>
    </li>

    <li style="display:flex;align-items:flex-start;gap:12px;">
        <span style="flex-shrink:0;width:24px;height:24px;border-radius:8px;background:rgba(124,108,252,.12);border:1px solid rgba(124,108,252,.22);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:var(--brand-light);margin-top:1px">2</span>
        <span style="font-size:13px;color:var(--text-2);line-height:1.6">
            Paste <strong style="color:var(--text)">URL</strong> di atas ke kolom <em>URL</em> pada dialog Browser Source.
        </span>
    </li>

    <li style="display:flex;align-items:flex-start;gap:12px;">
        <span style="flex-shrink:0;width:24px;height:24px;border-radius:8px;background:rgba(124,108,252,.12);border:1px solid rgba(124,108,252,.22);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:var(--brand-light);margin-top:1px">3</span>
        <span style="font-size:13px;color:var(--text-2);line-height:1.6">
            Atur <strong style="color:var(--text)">Width = 1920</strong> dan <strong style="color:var(--text)">Height = 1080</strong>
            agar widget bisa memposisikan diri di canvas dengan benar.
        </span>
    </li>

    <li style="display:flex;align-items:flex-start;gap:12px;">
        <span style="flex-shrink:0;width:24px;height:24px;border-radius:8px;background:rgba(124,108,252,.12);border:1px solid rgba(124,108,252,.22);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:var(--brand-light);margin-top:1px">4</span>
        <span style="font-size:13px;color:var(--text-2);line-height:1.6">
            Centang <strong style="color:var(--text)">Shutdown source when not visible</strong> &amp;
            <strong style="color:var(--text)">Refresh browser when scene becomes active</strong> (opsional tapi direkomendasikan).
        </span>
    </li>

    <li style="display:flex;align-items:flex-start;gap:12px;">
        <span style="flex-shrink:0;width:24px;height:24px;border-radius:8px;background:rgba(34,211,160,.1);border:1px solid rgba(34,211,160,.2);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:var(--green);margin-top:1px">✓</span>
        <span style="font-size:13px;color:var(--text-2);line-height:1.6">
            Klik <strong style="color:var(--text)">OK</strong>. Widget sekarang aktif di scene OBS kamu —
            tidak perlu restart OBS saat ada donasi masuk.
        </span>
    </li>

</ol>

<div style="margin-top:16px;padding:10px 14px;background:rgba(251,191,36,.05);border:1px solid rgba(251,191,36,.15);border-radius:8px;display:flex;align-items:flex-start;gap:8px;">
    <span class="iconify" data-icon="solar:lightbulb-bold-duotone" style="width:15px;height:15px;color:var(--yellow);flex-shrink:0;margin-top:1px"></span>
    <span style="font-size:12px;color:var(--text-2);line-height:1.6">
        <strong style="color:var(--text)">Tip:</strong> Jika widget tidak muncul, pastikan URL sudah benar dan OBS punya akses internet.
        Gunakan tombol <em>Buka</em> di atas untuk preview widget di browser terlebih dahulu.
    </span>
</div>
