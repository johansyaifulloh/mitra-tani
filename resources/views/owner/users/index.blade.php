@extends('layouts.admin.app', ['navActive' => 'owner.users.index'])
@section('page-title', 'Manajemen Akun')
@section('page-sub', 'Kelola akun Admin, Owner, dan Pengguna Sistem')
@section('content')

{{-- Stat cards ringkasan pengguna --}}
<div class="panel-stats">
    <div class="panel-stat panel-card-hover">
        <div class="panel-stat__top">
            <span class="panel-stat__icon">👥</span>
        </div>
        <p class="panel-stat__label">Total Akun</p>
        <p class="panel-stat__value">{{ $stats['total'] }}</p>
        <p class="panel-stat__hint">Semua peran terdaftar</p>
    </div>
    <div class="panel-stat panel-card-hover">
        <div class="panel-stat__top">
            <span class="panel-stat__icon">🛡️</span>
        </div>
        <p class="panel-stat__label">Akun Admin</p>
        <p class="panel-stat__value panel-stat__value--ok">{{ $stats['admin'] }}</p>
        <p class="panel-stat__hint">Pengelola produk & approval</p>
    </div>
    <div class="panel-stat panel-card-hover">
        <div class="panel-stat__top">
            <span class="panel-stat__icon">👑</span>
        </div>
        <p class="panel-stat__label">Akun Owner</p>
        <p class="panel-stat__value">{{ $stats['owner'] }}</p>
        <p class="panel-stat__hint">Pemilik toko / full access</p>
    </div>
    <div class="panel-stat panel-card-hover">
        <div class="panel-stat__top">
            <span class="panel-stat__icon">🛒</span>
        </div>
        <p class="panel-stat__label">Akun Konsumen</p>
        <p class="panel-stat__value">{{ $stats['customer'] }}</p>
        <p class="panel-stat__hint">Pembeli toko online</p>
    </div>
</div>

<div class="panel-card panel-card-hover">
    <div class="panel-card__head" style="flex-wrap: wrap; gap: 12px;">
        <div>
            <h3 class="panel-card__title">Daftar Akun Pengguna</h3>
            <span class="panel-card__meta">{{ $users->total() }} akun terdaftar</span>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('owner.users.create') }}" class="panel-btn panel-btn--sm" style="background:#059669; color:#fff; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                + Buat Akun Baru
            </a>
        </div>
    </div>

    {{-- Filter toolbar --}}
    <form method="GET" action="{{ route('owner.users.index') }}" class="panel-toolbar" style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; flex-wrap: wrap; gap: 12px; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('owner.users.index', ['search' => $search]) }}" class="panel-btn panel-btn--sm {{ empty($role) ? 'panel-btn--active' : 'panel-btn--outline' }}" style="border-radius: 20px; text-decoration: none;">Semua ({{ $stats['total'] }})</a>
            <a href="{{ route('owner.users.index', ['role' => 'admin', 'search' => $search]) }}" class="panel-btn panel-btn--sm {{ $role === 'admin' ? 'panel-btn--active' : 'panel-btn--outline' }}" style="border-radius: 20px; text-decoration: none;">🛡️ Admin ({{ $stats['admin'] }})</a>
            <a href="{{ route('owner.users.index', ['role' => 'owner', 'search' => $search]) }}" class="panel-btn panel-btn--sm {{ $role === 'owner' ? 'panel-btn--active' : 'panel-btn--outline' }}" style="border-radius: 20px; text-decoration: none;">👑 Owner ({{ $stats['owner'] }})</a>
            <a href="{{ route('owner.users.index', ['role' => 'customer', 'search' => $search]) }}" class="panel-btn panel-btn--sm {{ $role === 'customer' ? 'panel-btn--active' : 'panel-btn--outline' }}" style="border-radius: 20px; text-decoration: none;">🛒 Konsumen ({{ $stats['customer'] }})</a>
        </div>
        <div style="display: flex; gap: 8px;">
            @if($role)
                <input type="hidden" name="role" value="{{ $role }}">
            @endif
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, hp..." class="panel-form__input panel-form__input--sm" style="min-width: 220px;">
            <button type="submit" class="panel-btn panel-btn--sm panel-btn--outline">Cari</button>
            @if($search || $role)
                <a href="{{ route('owner.users.index') }}" class="panel-btn panel-btn--sm panel-btn--outline" style="text-decoration:none;">Reset</a>
            @endif
        </div>
    </form>

    <div class="panel-table-wrap">
        <table class="panel-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama & Info</th>
                    <th>Email</th>
                    <th>No. Handphone</th>
                    <th>Peran (Role)</th>
                    <th>Tgl Bergabung</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $u)
                <tr>
                    <td class="mono" style="color: #64748b;">{{ $users->firstItem() + $index }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 34px; height: 34px; border-radius: 10px; background: {{ $u->role === 'owner' ? '#e0e7ff' : ($u->role === 'admin' ? '#dcfce7' : '#f1f5f9') }}; color: {{ $u->role === 'owner' ? '#4338ca' : ($u->role === 'admin' ? '#15803d' : '#475569') }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div>
                                <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $u->name }}</p>
                                @if(auth('api')->id() === $u->id)
                                    <span style="font-size: 11px; background: #fef08a; color: #854d0e; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Akun Anda</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="mono" style="font-size: 13px;">{{ $u->email ?: '-' }}</td>
                    <td style="font-size: 13px;">{{ $u->phone ?: '-' }}</td>
                    <td>
                        @if($u->role === 'owner')
                            <span class="panel-badge" style="background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; font-weight: 600;">👑 Owner</span>
                        @elseif($u->role === 'admin')
                            <span class="panel-badge panel-badge--ok" style="font-weight: 600;">🛡️ Admin</span>
                        @else
                            <span class="panel-badge panel-badge--neutral" style="font-weight: 500;">🛒 Konsumen</span>
                        @endif
                    </td>
                    <td style="font-size: 13px; color: #64748b;">{{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}</td>
                    <td style="text-align: right;">
                        <div style="display: inline-flex; gap: 6px; justify-content: flex-end;">
                            <a href="{{ route('owner.users.edit', $u->id) }}" class="panel-btn panel-btn--sm panel-btn--outline" style="text-decoration: none; padding: 4px 10px; font-size: 12px;">Edit</a>
                            
                            @if(auth('api')->id() !== $u->id)
                            <form action="{{ route('owner.users.destroy', $u->id) }}" method="POST" onsubmit="return confirmDeleteUser(event, '{{ $u->name }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="panel-btn panel-btn--sm" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 4px 10px; font-size: 12px; cursor: pointer;">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px 20px; color: #94a3b8;">
                        <div style="font-size: 32px; margin-bottom: 8px;">👤</div>
                        <p style="font-weight: 600; color: #64748b;">Tidak ada data akun ditemukan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="panel-card__foot">
        {{ $users->links() }}
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDeleteUser(e, name) {
    e.preventDefault();
    var form = e.target.closest('form');
    MtNotify.confirm({
        title: 'Hapus Akun Pengguna?',
        message: 'Apakah Anda yakin ingin menghapus akun "' + name + '"? Tindakan ini tidak dapat dibatalkan.',
        confirmText: 'Ya, Hapus',
        variant: 'danger'
    }).then(function (ok) {
        if (ok) form.submit();
    });
    return false;
}
</script>
@endpush
