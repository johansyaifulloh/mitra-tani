{{-- Toast container + confirm modal — Tailwind unified notify --}}
@props(['notifyErrorTitle' => 'Gagal'])

<div id="mt-notify-root" class="mt-notify-root fixed top-4 right-4 z-[110] flex w-full max-w-sm flex-col gap-3 px-4 pointer-events-none sm:px-0"></div>

<div id="mt-notify-confirm" class="fixed inset-0 z-[100] hidden items-center justify-center p-4" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" data-mt-notify-dismiss></div>
    <div class="relative w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="mt-notify-confirm-title">
        <div class="flex items-start gap-4">
            <div id="mt-notify-confirm-icon" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600"></div>
            <div class="min-w-0 flex-1">
                <h3 id="mt-notify-confirm-title" class="text-lg font-bold text-slate-900"></h3>
                <p id="mt-notify-confirm-message" class="mt-1 text-sm leading-relaxed text-slate-600"></p>
            </div>
        </div>
        <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <button type="button" id="mt-notify-confirm-cancel" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</button>
            <button type="button" id="mt-notify-confirm-ok" class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition">Lanjutkan</button>
        </div>
    </div>
</div>

<style>
@keyframes mt-notify-in {
    from { opacity: 0; transform: translateY(-10px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes mt-notify-out {
    from { opacity: 1; transform: translateY(0) scale(1); }
    to { opacity: 0; transform: translateY(-8px) scale(0.98); }
}
.mt-notify-enter { animation: mt-notify-in 0.28s cubic-bezier(0.22, 1, 0.36, 1); }
.mt-notify-leave { animation: mt-notify-out 0.22s ease-in forwards; }
</style>

<script>
(function () {
    var root = document.getElementById('mt-notify-root');
    var confirmEl = document.getElementById('mt-notify-confirm');
    var confirmTitle = document.getElementById('mt-notify-confirm-title');
    var confirmMessage = document.getElementById('mt-notify-confirm-message');
    var confirmIcon = document.getElementById('mt-notify-confirm-icon');
    var confirmOk = document.getElementById('mt-notify-confirm-ok');
    var confirmCancel = document.getElementById('mt-notify-confirm-cancel');
    var confirmBackdrop = confirmEl.querySelector('[data-mt-notify-dismiss]');
    var confirmResolver = null;

    var icons = {
        success: '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>',
        error: '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        warning: '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>',
        info: '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 16v-4m0-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    };

    var toastStyles = {
        success: {
            wrap: 'border-emerald-600 bg-emerald-600 shadow-lg shadow-emerald-900/15',
            icon: 'bg-emerald-500 text-white',
            title: 'text-white',
            message: 'text-emerald-50',
            close: 'text-emerald-100 hover:bg-emerald-500 hover:text-white',
        },
        error: {
            wrap: 'border-red-600 bg-red-600 shadow-lg shadow-red-900/15',
            icon: 'bg-red-500 text-white',
            title: 'text-white',
            message: 'text-red-50',
            close: 'text-red-100 hover:bg-red-500 hover:text-white',
        },
        warning: {
            wrap: 'border-amber-500 bg-amber-500 shadow-lg shadow-amber-900/15',
            icon: 'bg-amber-400 text-white',
            title: 'text-white',
            message: 'text-amber-50',
            close: 'text-amber-100 hover:bg-amber-400 hover:text-white',
        },
        info: {
            wrap: 'border-sky-600 bg-sky-600 shadow-lg shadow-sky-900/15',
            icon: 'bg-sky-500 text-white',
            title: 'text-white',
            message: 'text-sky-50',
            close: 'text-sky-100 hover:bg-sky-500 hover:text-white',
        },
    };

    var defaultTitles = {
        success: 'Berhasil',
        error: 'Gagal',
        warning: 'Perhatian',
        info: 'Informasi',
    };

    var toastDuration = 5000;

    function dismissToast(el) {
        if (!el || el.dataset.dismissed === '1') return;
        el.dataset.dismissed = '1';
        clearTimeout(Number(el.dataset.timer || 0));
        el.classList.remove('mt-notify-enter');
        el.classList.add('mt-notify-leave');
        setTimeout(function () { el.remove(); }, 220);
    }

    function resolveRoot(anchor) {
        if (anchor) {
            var custom = document.querySelector(anchor);
            if (custom) return custom;
        }
        return root;
    }

    function toast(message, type, title, opts) {
        type = type || 'error';
        opts = opts || {};
        var duration = opts.duration || toastDuration;
        var targetRoot = resolveRoot(opts.anchor);
        var inline = opts.anchor && targetRoot !== root;
        var style = toastStyles[type] || toastStyles.error;
        var label = title || defaultTitles[type] || defaultTitles.error;

        var el = document.createElement('div');
        el.className = 'mt-notify-enter pointer-events-auto flex items-start gap-2.5 rounded-xl border px-3.5 py-3 '
            + (inline ? 'w-full ' : 'max-w-sm ')
            + style.wrap;
        el.setAttribute('role', 'alert');
        el.innerHTML = ''
            + '<span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full ' + style.icon + '">' + (icons[type] || icons.error) + '</span>'
            + '<div class="min-w-0 flex-1">'
            + '<p class="text-[13px] font-bold leading-tight ' + style.title + '">' + label + '</p>'
            + '<p class="mt-0.5 text-[13px] leading-snug ' + style.message + '" data-mt-notify-message></p>'
            + '</div>'
            + '<button type="button" class="shrink-0 rounded-md p-1 transition ' + (style.close || 'text-white/80 hover:bg-white/15 hover:text-white') + '" aria-label="Tutup">'
            + '<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>'
            + '</button>';

        el.querySelector('[data-mt-notify-message]').textContent = message;
        el.querySelector('button').addEventListener('click', function () { dismissToast(el); });
        targetRoot.appendChild(el);
        el.dataset.timer = String(setTimeout(function () { dismissToast(el); }, duration));
        return el;
    }

    function queueToast(message, type, title, anchor) {
        try {
            sessionStorage.setItem('mt_notify_pending', JSON.stringify({
                message: message,
                type: type || 'success',
                title: title || null,
                anchor: anchor || null,
            }));
        } catch (e) {}
    }

    function flushPendingToast() {
        try {
            var raw = sessionStorage.getItem('mt_notify_pending');
            if (!raw) return;
            sessionStorage.removeItem('mt_notify_pending');
            var data = JSON.parse(raw);
            if (data && data.message) {
                toast(data.message, data.type || 'success', data.title || null, { anchor: data.anchor || null });
            }
        } catch (e) {}
    }

    function redirectWithToast(url, message, type, title, anchor) {
        queueToast(message, type, title, anchor);
        window.location.href = url;
    }

    function closeConfirm(result) {
        confirmEl.classList.add('hidden');
        confirmEl.classList.remove('flex');
        confirmEl.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (confirmResolver) {
            confirmResolver(result);
            confirmResolver = null;
        }
    }

    function confirm(options) {
        options = options || {};
        var variant = options.variant || 'warning';
        var iconClass = {
            warning: 'bg-amber-100 text-amber-600',
            danger: 'bg-red-100 text-red-600',
            info: 'bg-sky-100 text-sky-600',
        };
        var btnClass = {
            warning: 'bg-amber-600 hover:bg-amber-700',
            danger: 'bg-red-600 hover:bg-red-700',
            info: 'bg-brand-600 hover:bg-brand-700',
        };

        confirmTitle.textContent = options.title || 'Konfirmasi';
        confirmMessage.textContent = options.message || '';
        confirmOk.textContent = options.confirmText || 'Ya, lanjutkan';
        confirmCancel.textContent = options.cancelText || 'Batal';
        confirmIcon.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-full ' + (iconClass[variant] || iconClass.warning);
        confirmIcon.innerHTML = icons[variant === 'danger' ? 'warning' : variant] || icons.warning;
        confirmOk.className = 'inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition ' + (btnClass[variant] || btnClass.warning);

        confirmEl.classList.remove('hidden');
        confirmEl.classList.add('flex');
        confirmEl.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        confirmOk.focus();

        return new Promise(function (resolve) {
            confirmResolver = resolve;
        });
    }

    confirmOk.addEventListener('click', function () { closeConfirm(true); });
    confirmCancel.addEventListener('click', function () { closeConfirm(false); });
    confirmBackdrop.addEventListener('click', function () { closeConfirm(false); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && confirmResolver) closeConfirm(false);
    });

    window.MtNotify = {
        toast: toast,
        confirm: confirm,
        redirectWithToast: redirectWithToast,
        duration: toastDuration,
    };

    window.showPanelToast = function (message, type) { toast(message, type); };
    window.dismissPanelToast = function () {
        var first = root.firstElementChild;
        if (first) dismissToast(first);
    };
    window.dismissAuthToast = window.dismissPanelToast;

    document.addEventListener('DOMContentLoaded', function () {
        flushPendingToast();
        @if(session('success'))
        toast(@json(session('success')), 'success', 'Berhasil');
        @endif
        @if(session('error'))
        toast(@json(session('error')), 'error', 'Gagal');
        @endif
        @if(isset($errors) && $errors->any())
        toast(@json($errors->first()), 'error', @json($notifyErrorTitle ?? 'Gagal'));
        @endif
    });
})();
</script>
