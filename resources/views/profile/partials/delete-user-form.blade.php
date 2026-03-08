<div>
    <p style="font-size:13px;color:var(--text-2);line-height:1.7;margin-bottom:20px">
        Setelah akun dihapus, semua data termasuk profil streamer, donasi, dan konfigurasi alert akan
        <strong style="color:#f43f5e">dihapus secara permanen</strong> dan tidak dapat dipulihkan.
    </p>

    <button type="button" onclick="openDeleteModal()"
        style="display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:var(--radius);border:1px solid rgba(244,63,94,.4);background:rgba(244,63,94,.08);color:#f43f5e;font-size:13px;font-weight:600;cursor:pointer;transition:.2s"
        onmouseover="this.style.background='rgba(244,63,94,.15)'"
        onmouseout="this.style.background='rgba(244,63,94,.08)'">
        <span class="iconify" data-icon="solar:trash-bin-trash-bold-duotone"></span>
        Hapus Akun Saya
    </button>
</div>

{{-- Delete confirmation modal --}}
<div class="delete-modal-overlay" id="delete-modal-overlay" onclick="if(event.target===this)closeDeleteModal()">
    <div class="delete-modal">
        <h3>
            <span class="iconify" data-icon="solar:shield-warning-bold-duotone" style="vertical-align:middle;margin-right:6px"></span>
            Hapus Akun?
        </h3>
        <p>
            Tindakan ini <strong>tidak dapat dibatalkan</strong>. Semua data kamu — profil, donasi, dan pengaturan alert —
            akan dihapus selamanya. Masukkan password untuk konfirmasi.
        </p>

        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <div class="form-group" style="margin-bottom:16px">
                <label for="delete_password">Password</label>
                <div class="input-icon-wrap">
                    <span class="iconify" data-icon="solar:lock-password-bold-duotone"></span>
                    <input id="delete_password" name="password" type="password"
                        placeholder="Masukkan password kamu…"
                        autofocus />
                </div>
                @error('password', 'userDeletion')
                    <div style="font-size:12px;color:#f43f5e;margin-top:5px">{{ $message }}</div>
                @enderror
            </div>

            <div class="delete-modal-actions">
                <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Batal</button>
                <button type="submit" class="btn-danger-confirm">
                    <span class="iconify" data-icon="solar:trash-bin-trash-bold-duotone"></span>
                    Ya, Hapus Akun
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openDeleteModal() {
    document.getElementById('delete-modal-overlay').classList.add('open');
    setTimeout(() => { const p = document.getElementById('delete_password'); if(p) p.focus(); }, 100);
}
function closeDeleteModal() {
    document.getElementById('delete-modal-overlay').classList.remove('open');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
@if($errors->userDeletion->isNotEmpty())
openDeleteModal();
@endif
</script>
@endpush
