@extends('layouts.admin.app', ['navActive' => 'owner.users.index'])
@section('page-title', 'Edit Akun Pengguna')
@section('page-sub', 'Perbarui informasi profil dan hak akses akun')
@section('content')

<div class="panel-card" style="max-width: 780px; margin: 0 auto;">
    <div class="panel-card__head" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 class="panel-card__title">Edit Akun: {{ $user->name }}</h3>
            <span class="panel-card__meta">ID Akun #{{ $user->id }}</span>
        </div>
        <a href="{{ route('owner.users.index') }}" class="panel-btn panel-btn--sm panel-btn--outline" style="text-decoration:none;">← Kembali</a>
    </div>

    <form action="{{ route('owner.users.update', $user->id) }}" method="POST" style="padding: 24px;">
        @csrf
        @method('PUT')

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
            <label class="panel-form__label" style="display: block; margin-bottom: 10px; font-weight: 700; color: #1e293b;">Peran Akun (Role) <span style="color: #ef4444;">*</span></label>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                <label style="border: 2px solid {{ old('role', $user->role) === 'admin' ? '#059669' : '#e2e8f0' }}; border-radius: 14px; padding: 14px; display: flex; gap: 10px; cursor: pointer; background: {{ old('role', $user->role) === 'admin' ? '#f0fdf4' : '#fff' }}; transition: all .2s;" id="role-admin-card" onclick="selectRole('admin')">
                    <input type="radio" name="role" value="admin" {{ old('role', $user->role) === 'admin' ? 'checked' : '' }} style="margin-top: 3px;">
                    <div>
                        <strong style="color: #065f46; font-size: 14px;">🛡️ Admin</strong>
                        <p style="font-size: 11px; color: #475569; margin-top: 2px; line-height: 1.3;">Produk, Kategori & Approval</p>
                    </div>
                </label>

                <label style="border: 2px solid {{ old('role', $user->role) === 'owner' ? '#4f46e5' : '#e2e8f0' }}; border-radius: 14px; padding: 14px; display: flex; gap: 10px; cursor: pointer; background: {{ old('role', $user->role) === 'owner' ? '#eef2ff' : '#fff' }}; transition: all .2s;" id="role-owner-card" onclick="selectRole('owner')">
                    <input type="radio" name="role" value="owner" {{ old('role', $user->role) === 'owner' ? 'checked' : '' }} style="margin-top: 3px;">
                    <div>
                        <strong style="color: #3730a3; font-size: 14px;">👑 Owner</strong>
                        <p style="font-size: 11px; color: #475569; margin-top: 2px; line-height: 1.3;">Full Akses Toko & Akun</p>
                    </div>
                </label>

                <label style="border: 2px solid {{ old('role', $user->role) === 'customer' ? '#0284c7' : '#e2e8f0' }}; border-radius: 14px; padding: 14px; display: flex; gap: 10px; cursor: pointer; background: {{ old('role', $user->role) === 'customer' ? '#f0f9ff' : '#fff' }}; transition: all .2s;" id="role-customer-card" onclick="selectRole('customer')">
                    <input type="radio" name="role" value="customer" {{ old('role', $user->role) === 'customer' ? 'checked' : '' }} style="margin-top: 3px;">
                    <div>
                        <strong style="color: #0369a1; font-size: 14px;">🛒 Konsumen</strong>
                        <p style="font-size: 11px; color: #475569; margin-top: 2px; line-height: 1.3;">Pelanggan Toko Online</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Data Pengguna --}}
        <div style="display: grid; grid-template-columns: 1fr; gap: 18px; margin-bottom: 24px;">
            <div class="panel-form__field">
                <label class="panel-form__label">Nama Lengkap <span style="color: #ef4444;">*</span></label>
                <input type="text" name="name" class="panel-form__input" value="{{ old('name', $user->name) }}" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="panel-form__field">
                    <label class="panel-form__label">Alamat Email <span style="color: #ef4444;">*</span></label>
                    <input type="email" name="email" class="panel-form__input" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="panel-form__field">
                    <label class="panel-form__label">Nomor Handphone / WhatsApp</label>
                    <input type="tel" name="phone" class="panel-form__input" value="{{ old('phone', $user->phone) }}">
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-top: 8px;">
                <p style="font-weight: 600; color: #334155; margin-bottom: 8px; font-size: 13px;">🔒 Ubah Password (Opsional)</p>
                <p style="font-size: 12px; color: #64748b; margin-bottom: 14px;">Biarkan kosong jika tidak ingin mengganti password akun ini.</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="panel-form__field">
                        <label class="panel-form__label">Password Baru</label>
                        <input type="password" name="password" class="panel-form__input" placeholder="Isi password baru jika diubah" minlength="6">
                    </div>

                    <div class="panel-form__field">
                        <label class="panel-form__label">Ulangi Password Baru</label>
                        <input type="password" name="password_confirmation" class="panel-form__input" placeholder="Ulangi password baru" minlength="6">
                    </div>
                </div>
            </div>
        </div>

        <div style="padding-top: 16px; border-top: 1px solid #f1f5f9; display: flex; gap: 12px; justify-content: flex-end;">
            <a href="{{ route('owner.users.index') }}" class="panel-btn panel-btn--outline" style="text-decoration:none;">Batal</a>
            <button type="submit" class="panel-btn" style="background:#059669; color:#fff;">Simpan Perubahan</button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function selectRole(role) {
    var adminCard = document.getElementById('role-admin-card');
    var ownerCard = document.getElementById('role-owner-card');
    var customerCard = document.getElementById('role-customer-card');
    
    adminCard.style.borderColor = '#e2e8f0';
    adminCard.style.background = '#fff';
    ownerCard.style.borderColor = '#e2e8f0';
    ownerCard.style.background = '#fff';
    customerCard.style.borderColor = '#e2e8f0';
    customerCard.style.background = '#fff';

    if (role === 'admin') {
        adminCard.style.borderColor = '#059669';
        adminCard.style.background = '#f0fdf4';
    } else if (role === 'owner') {
        ownerCard.style.borderColor = '#4f46e5';
        ownerCard.style.background = '#eef2ff';
    } else {
        customerCard.style.borderColor = '#0284c7';
        customerCard.style.background = '#f0f9ff';
    }
}
</script>
@endpush
