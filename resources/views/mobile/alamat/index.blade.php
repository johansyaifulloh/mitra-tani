@extends('layouts.mobile.app', ['navActive' => 'profil'])
@section('title', 'Alamat Saya | Mantri Tani')
@section('main-class', 'px-5 py-4 pb-24 animate-fade-up')
@section('header')
<header class="page-header">
<a href="{{ route('toko.profil.index') }}" class="page-header__back"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 19l-7-7 7-7"/></svg></a>
<h1 class="page-header__title">Alamat Saya</h1>
<div class="page-header__action"></div>
</header>
@endsection
@section('content')

<p class="text-sm text-gray-500 mb-4">Kelola alamat untuk pengambilan barang di toko. Pilih satu alamat sebagai <span class="font-semibold text-emerald-700">alamat utama</span>.</p>

<div class="address-list mb-4" id="address-list">
<p class="text-sm text-gray-400 text-center py-4" id="address-empty">Belum ada alamat tersimpan.</p>
</div>

<button type="button" class="address-add-btn" id="toggle-address-form">
<span class="address-add-btn__icon">+</span>
<span>Tambah Alamat Baru</span>
</button>

<form class="panel address-form-panel mt-4" id="address-form" hidden>
<p class="panel__label">Alamat Baru</p>
<div class="space-y-3">
<input class="input-field" name="label" id="addr-label" placeholder="Label (Rumah / Kebun / Kantor)" required>
<input class="input-field" name="recipient_name" id="addr-name" placeholder="Nama penerima" required>
<input class="input-field" name="phone" id="addr-phone" placeholder="No. Handphone" required>
<textarea class="input-field address-form-panel__textarea" name="street" id="addr-street" rows="3" placeholder="Alamat lengkap (Jl, RT/RW, Dusun)" required></textarea>
<input class="input-field" name="district" id="addr-district" placeholder="Kecamatan, Kota" required>
<label class="address-form-panel__default">
<input type="checkbox" name="is_default" id="addr-default" value="1" class="accent-emerald-600"> Jadikan alamat utama
</label>
<button type="submit" class="btn-primary py-3.5 text-sm w-full" id="address-save-btn">Simpan Alamat</button>
</div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var storeUrl = @json(route('toko.alamat.store'));
    var baseUrl = @json(url('/toko/alamat'));
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var form = document.getElementById('address-form');
    var list = document.getElementById('address-list');
    var toggleBtn = document.getElementById('toggle-address-form');
    var saveBtn = document.getElementById('address-save-btn');
    var addresses = @json($addresses ?? []);
    var busy = false;

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function renderAddressCard(address) {
        var actions = '<div class="address-card__actions">';
        if (address.default) {
            actions += '<span class="address-card__badge-default">Alamat Utama</span>';
        } else {
            actions += '<button type="button" class="address-card__action address-card__action--default" data-set-default="' + address.id + '">Jadikan Utama</button>';
        }
        actions += '<button type="button" class="address-card__action address-card__action--delete" data-delete="' + address.id + '">Hapus</button>';
        actions += '</div>';

        return '<div class="address-card address-card--static' + (address.default ? ' address-card--active' : '') + '">' +
            '<div class="address-card__head">' +
            '<div class="address-card__labels">' +
            '<span class="address-card__tag">' + escapeHtml(address.label) + '</span>' +
            (address.default ? '<span class="address-card__default">Utama</span>' : '') +
            '</div></div>' +
            '<p class="address-card__name">' + escapeHtml(address.name) + '</p>' +
            '<p class="address-card__phone">' + escapeHtml(address.phone) + '</p>' +
            '<p class="address-card__street">' + escapeHtml(address.street) + '</p>' +
            '<p class="address-card__district">' + escapeHtml(address.district) + '</p>' +
            actions +
            '</div>';
    }

    function renderList() {
        if (!addresses.length) {
            list.innerHTML = '<p class="text-sm text-gray-400 text-center py-4" id="address-empty">Belum ada alamat tersimpan.</p>';
            return;
        }
        list.innerHTML = addresses.map(renderAddressCard).join('');
    }

    function request(url, method, body) {
        return fetch(url, {
            method: method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
            body: body ? JSON.stringify(body) : undefined,
        })
        .then(function (res) {
            return res.json().then(function (json) {
                if (!res.ok) throw new Error(json.message || 'Permintaan gagal.');
                return json;
            });
        });
    }

    toggleBtn.addEventListener('click', function () {
        form.hidden = !form.hidden;
        if (!form.hidden) form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (busy) return;

        var payload = {
            label: form.label.value.trim(),
            recipient_name: form.recipient_name.value.trim(),
            phone: form.phone.value.trim(),
            street: form.street.value.trim(),
            district: form.district.value.trim(),
            is_default: form.is_default.checked,
        };

        if (!payload.label || !payload.recipient_name || !payload.phone || !payload.street || !payload.district) {
            MtNotify.toast('Lengkapi semua field alamat.', 'warning', 'Perhatian');
            return;
        }

        busy = true;
        saveBtn.disabled = true;
        saveBtn.textContent = 'Menyimpan...';

        request(storeUrl, 'POST', payload)
            .then(function (result) {
                MtNotify.toast(result.message, 'success', 'Berhasil');
                addresses = result.data || [];
                renderList();
                form.reset();
                form.hidden = true;
            })
            .catch(function (err) {
                MtNotify.toast(err.message, 'error', 'Gagal');
            })
            .finally(function () {
                busy = false;
                saveBtn.disabled = false;
                saveBtn.textContent = 'Simpan Alamat';
            });
    });

    list.addEventListener('click', function (e) {
        var setBtn = e.target.closest('[data-set-default]');
        var delBtn = e.target.closest('[data-delete]');

        if (setBtn && !busy) {
            busy = true;
            request(baseUrl + '/' + setBtn.dataset.setDefault, 'PUT', { is_default: true })
                .then(function (result) {
                    MtNotify.toast('Alamat utama diperbarui.', 'success', 'Berhasil');
                    addresses = result.data || [];
                    renderList();
                })
                .catch(function (err) {
                    MtNotify.toast(err.message, 'error', 'Gagal');
                })
                .finally(function () { busy = false; });
            return;
        }

        if (delBtn && !busy) {
            MtNotify.confirm({
                title: 'Hapus Alamat',
                message: 'Yakin ingin menghapus alamat ini?',
                confirmText: 'Hapus',
                cancelText: 'Batal',
                variant: 'danger',
            }).then(function (ok) {
                if (!ok) return;
                busy = true;
                request(baseUrl + '/' + delBtn.dataset.delete, 'DELETE')
                    .then(function (result) {
                        MtNotify.toast('Alamat dihapus.', 'success', 'Berhasil');
                        addresses = result.data || [];
                        renderList();
                    })
                    .catch(function (err) {
                        MtNotify.toast(err.message, 'error', 'Gagal');
                    })
                    .finally(function () { busy = false; });
            });
        }
    });

    renderList();
});
</script>
@endpush
