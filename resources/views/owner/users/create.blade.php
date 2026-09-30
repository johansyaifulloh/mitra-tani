@extends('layouts.admin.app', ['navActive' => 'owner.users.index'])
@section('page-title', 'Buat Akun Baru')
@section('page-sub', 'Tambah akun pengelola (Admin / Owner) ke sistem')
@section('content')

<div class="panel-card" style="max-width: 780px; margin: 0 auto;">
    <div class="panel-card__head" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 class="panel-card__title">Form Pembuatan Akun</h3>
            <span class="panel-card__meta">Isi data lengkap akun yang ingin dibuat</span>
        </div>
        <a href="{{ route('owner.users.index') }}" class="panel-btn panel-btn--sm panel-btn--outline" style="text-decoration:none;">← Kembali</a>
    </div>

    <form action="{{ route('owner.users.store') }}" method="POST" style="padding: 24px;">
        @csrf

        @if ($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
                <p style="font-weight: 700; color: #991b1b; margin-bottom: 6px; font-size: 14px;">Terjadi Kesalahan Pengisian:</p>
                <ul style="margin: 0; padding-left: 20px; color: #b91c1c; font-size: 13px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Pilihan Peran / Role --}}
        <div style="margin-bottom: 24px;">
            <label class="panel-form__label" style="display: block; margin-bottom: 10px; font-weight: 700; color: #1e293b;">Pilih Peran Akun (Role) <span style="color: #ef4444;">*</span></label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <label style="border: 2px solid {{ old('role', 'admin') === 'admin' ? '#059669' : '#e2e8f0' }}; border-radius: 14px; padding: 16px; display: flex; gap: 12px; cursor: pointer; background: {{ old('role', 'admin') === 'admin' ? '#f0fdf4' : '#fff' }}; transition: all .2s;" id="role-admin-card" onclick="selectRole('admin')">
                    <input type="radio" name="role" value="admin" {{ old('role', 'admin') === 'admin' ? 'checked' : '' }} style="margin-top: 4px;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span style="font-size: 18px;">🛡️</span>
                            <strong style="color: #065f46; font-size: 15px;">Admin Toko</strong>
                        </div>
                        <p style="font-size: 12px; color: #475569; margin-top: 4px; line-height: 1.4;">
                            Dapat mengelola data produk, kategori, approval pengambilan, dan laporan operasional.
                        </p>
                    </div>
                </label>

                <label style="border: 2px solid {{ old('role') === 'owner' ? '#4f46e5' : '#e2e8f0' }}; border-radius: 14px; padding: 16px; display: flex; gap: 12px; cursor: pointer; background: {{ old('role') === 'owner' ? '#eef2ff' : '#fff' }}; transition: all .2s;" id="role-owner-card" onclick="selectRole('owner')">
                    <input type="radio" name="role" value="owner" {{ old('role') === 'owner' ? 'checked' : '' }} style="margin-top: 4px;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span style="font-size: 18px;">👑</span>
                            <strong style="color: #3730a3; font-size: 15px;">Owner (Full Akses)</strong>
                        </div>
                        <p style="font-size: 12px; color: #475569; margin-top: 4px; line-height: 1.4;">
                            Akses penuh bisnis, manajemen akun admin, laporan keuangan, dan konfigurasi Midtrans.
                        </p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Data Pengguna --}}
        <div style="display: grid; grid-template-columns: 1fr; gap: 18px; margin-bottom: 24px;">
            <div class="panel-form__field">
                <label class="panel-form__label">Nama Lengkap <span style="color: #ef4444;">*</span></label>
                <input type="text" name="name" class="panel-form__input" placeholder="Misal: Budi Santoso" value="{{ old('name') }}" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="panel-form__field">
                    <label class="panel-form__label">Alamat Email <span style="color: #ef4444;">*</span></label>
                    <input type="email" name="email" class="panel-form__input" placeholder="admin.baru@mantri-tani.test" value="{{ old('email') }}" required>
                    <p class="panel-field-hint">Digunakan sebagai username saat login.</p>
                </div>

                <div class="panel-form__field">
                    <label class="panel-form__label">Nomor Handphone / WhatsApp</label>
                    <input type="tel" name="phone" class="panel-form__input" placeholder="0812-3456-7890" value="{{ old('phone') }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="panel-form__field">
                    <label class="panel-form__label">Password <span style="color: #ef4444;">*</span></label>
                    <input type="password" name="password" class="panel-form__input" placeholder="Minimal 6 karakter" required minlength="6">
                </div>

                <div class="panel-form__field">
                    <label class="panel-form__label">Konfirmasi Password <span style="color: #ef4444;">*</span></label>
                    <input type="password" name="password_confirmation" class="panel-form__input" placeholder="Ulangi password di atas" required minlength="6">
                </div>
            </div>
        </div>

        <div style="padding-top: 16px; border-top: 1px solid #f1f5f9; display: flex; gap: 12px; justify-content: flex-end;">
            <a href="{{ route('owner.users.index') }}" class="panel-btn panel-btn--outline" style="text-decoration:none;">Batal</a>
            <button type="submit" class="panel-btn" style="background:#059669; color:#fff;">Simpan & Buat Akun</button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function selectRole(role) {
    var adminCard = document.getElementById('role-admin-card');
    var ownerCard = document.getElementById('role-owner-card');
    
    if (role === 'admin') {
        adminCard.style.borderColor = '#059669';
        adminCard.style.background = '#f0fdf4';
        ownerCard.style.borderColor = '#e2e8f0';
        ownerCard.style.background = '#fff';
    } else {
        ownerCard.style.borderColor = '#4f46e5';
        ownerCard.style.background = '#eef2ff';
        adminCard.style.borderColor = '#e2e8f0';
        adminCard.style.background = '#fff';
    }
}
</script>
@endpush
