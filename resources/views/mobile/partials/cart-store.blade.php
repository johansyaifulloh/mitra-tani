<script>
window.MtCart = (function () {
    var storeUrl = @json(route('toko.keranjang.store'));
    var loginUrl = @json($tokoLoginUrl ?? route('toko.login', ['redirect' => 'toko.keranjang.index']));
    var keranjangUrl = @json($tokoKeranjangUrl ?? route('toko.keranjang.index'));
    var loggedIn = @json($tokoLoggedIn ?? false);
    var countUrl = @json(route('toko.keranjang.count'));
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var pending = false;

    function goLogin() {
        window.location.href = loginUrl;
    }

    function updateBadge(count) {
        var badge = document.getElementById('cart-badge');
        if (!badge) {
            return;
        }
        count = parseInt(count, 10) || 0;
        if (count > 0) {
            badge.textContent = count;
            badge.hidden = false;
        } else {
            badge.hidden = true;
        }
    }

    function refreshBadge() {
        if (!loggedIn) {
            return;
        }
        fetch(countUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        })
        .then(function (res) { return res.ok ? res.json() : null; })
        .then(function (json) {
            if (json && json.meta) {
                updateBadge(json.meta.cart_count);
            }
        })
        .catch(function () {});
    }

    function add(productId, quantity, options) {
        if (!loggedIn) {
            goLogin();
            return Promise.resolve();
        }

        options = options || {};
        quantity = Math.max(1, parseInt(quantity, 10) || 1);
        productId = parseInt(productId, 10);

        if (!productId) {
            return Promise.reject(new Error('Produk tidak valid.'));
        }

        if (pending) {
            return Promise.resolve();
        }

        pending = true;
        var btn = options.button || null;
        if (btn) {
            btn.disabled = true;
        }

        return fetch(storeUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity,
            }),
        })
        .then(function (res) {
            return res.json().then(function (json) {
                if (res.status === 401) {
                    goLogin();
                    throw new Error(json.message || 'Unauthenticated.');
                }
                if (!res.ok) {
                    throw new Error(json.message || 'Gagal menambahkan ke keranjang.');
                }
                return json;
            });
        })
        .then(function (result) {
            MtNotify.toast(result.message || 'Produk ditambahkan ke keranjang.', 'success', 'Berhasil');
            if (result.meta && typeof result.meta.cart_count !== 'undefined') {
                updateBadge(result.meta.cart_count);
            } else {
                refreshBadge();
            }
            if (options.redirectToCart) {
                setTimeout(function () { window.location.href = keranjangUrl; }, 800);
            }
            return result;
        })
        .catch(function (err) {
            if (err.message && err.message !== 'Unauthenticated.') {
                MtNotify.toast(err.message, 'error', 'Gagal');
            }
            throw err;
        })
        .finally(function () {
            pending = false;
            if (btn) {
                btn.disabled = false;
            }
        });
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-cart-add]');
        if (!btn || btn.disabled) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        if (!loggedIn) {
            goLogin();
            return;
        }

        var productId = btn.dataset.productId;
        var quantity = btn.dataset.quantity || 1;
        var redirectToCart = btn.dataset.cartRedirect === '1';

        add(productId, quantity, {
            button: btn,
            redirectToCart: redirectToCart,
        });
    });

    // Sinkronkan badge saat halaman dimuat (tanpa perlu refresh manual)
    refreshBadge();

    return {
        add: add,
        isLoggedIn: function () { return loggedIn; },
        goLogin: goLogin,
        refreshBadge: refreshBadge,
        updateBadge: updateBadge,
    };
})();
</script>
