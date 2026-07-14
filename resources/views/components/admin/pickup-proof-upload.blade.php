@props(['transactionId' => ''])

<div class="panel-proof-upload" id="proof-upload">
<div class="panel-proof-upload__head">
<div>
<h4 class="panel-proof-upload__title">📸 Bukti Pengambilan Barang</h4>
<p class="panel-proof-upload__desc">Wajib ambil foto langsung dari kamera — bukan dari galeri. Foto otomatis dikompres maksimal 100 KB. Mobile memakai kamera belakang, laptop memakai webcam depan.</p>
</div>
<span class="panel-badge panel-badge--warn">Wajib</span>
</div>

<div class="panel-proof-upload__empty" id="proof-empty">
<div class="panel-proof-upload__zone">
<div class="panel-proof-upload__zone-icon">
<svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
</div>
<p class="panel-proof-upload__zone-title">Belum ada foto bukti</p>
<p class="panel-proof-upload__zone-hint" id="proof-camera-hint">Tekan tombol di bawah untuk membuka kamera</p>
<span class="panel-proof-upload__device-tag" id="proof-device-tag">📱 Kamera belakang</span>
</div>
<button type="button" class="panel-proof-upload__btn panel-proof-upload__btn--full" id="proof-open-camera">
<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
Buka Kamera & Ambil Foto
</button>
<p class="panel-proof-upload__note">Galeri tidak tersedia — hanya foto langsung dari kamera perangkat.</p>
</div>

<div class="panel-proof-upload__camera" id="proof-camera" hidden>
<div class="panel-proof-upload__camera-frame">
<video id="proof-video" class="panel-proof-upload__video" autoplay playsinline muted></video>
<div class="panel-proof-upload__camera-overlay">
<span class="panel-proof-upload__camera-live">● LIVE</span>
<span id="proof-camera-label">Kamera aktif</span>
</div>
</div>
<canvas id="proof-canvas" hidden></canvas>
<div class="panel-proof-upload__camera-actions">
<button type="button" class="panel-proof-upload__btn panel-proof-upload__btn--capture" id="proof-capture">
<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
Ambil Foto
</button>
<button type="button" class="panel-proof-upload__btn panel-proof-upload__btn--outline" id="proof-cancel-camera">Batal</button>
</div>
<p class="panel-proof-upload__camera-error" id="proof-camera-error" hidden></p>
</div>

<div class="panel-proof-upload__preview" id="proof-preview" hidden>
<div class="panel-proof-upload__preview-frame">
<img id="proof-img" src="" alt="Bukti pengambilan {{ $transactionId }}" class="panel-proof-upload__preview-img">
<div class="panel-proof-upload__preview-badge">✓ Foto dari kamera</div>
</div>
<div class="panel-proof-upload__preview-info">
<div>
<p class="panel-proof-upload__preview-name" id="proof-filename">—</p>
<p class="panel-proof-upload__preview-size" id="proof-filesize">—</p>
</div>
<button type="button" class="panel-proof-upload__remove" id="proof-retake">Ambil Ulang</button>
</div>
</div>
</div>

@push('scripts')
<script>
(function () {
    var openBtn = document.getElementById('proof-open-camera');
    if (! openBtn) return;

    var video = document.getElementById('proof-video');
    var canvas = document.getElementById('proof-canvas');
    var empty = document.getElementById('proof-empty');
    var camera = document.getElementById('proof-camera');
    var preview = document.getElementById('proof-preview');
    var img = document.getElementById('proof-img');
    var filename = document.getElementById('proof-filename');
    var filesize = document.getElementById('proof-filesize');
    var btnVerify = document.getElementById('btn-verify');
    var deviceTag = document.getElementById('proof-device-tag');
    var cameraLabel = document.getElementById('proof-camera-label');
    var cameraError = document.getElementById('proof-camera-error');
    var stream = null;
    var currentUrl = null;
    var useFrontCamera = false;

    function isMobileDevice() {
        return /Android|iPhone|iPad|iPod|Mobile|webOS|BlackBerry/i.test(navigator.userAgent)
            || (navigator.maxTouchPoints > 1 && window.innerWidth < 1024);
    }

    function setupDeviceLabel() {
        useFrontCamera = !isMobileDevice();
        if (useFrontCamera) {
            deviceTag.textContent = '💻 Webcam depan';
            if (cameraLabel) cameraLabel.textContent = 'Webcam depan laptop';
        } else {
            deviceTag.textContent = '📱 Kamera belakang';
            if (cameraLabel) cameraLabel.textContent = 'Kamera belakang HP';
        }
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(function (t) { t.stop(); });
            stream = null;
        }
        video.srcObject = null;
    }

    function showError(msg) {
        cameraError.textContent = msg;
        cameraError.hidden = false;
    }

    function hideError() {
        cameraError.hidden = true;
        cameraError.textContent = '';
    }

    async function startCamera() {
        hideError();
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showError('Browser tidak mendukung kamera. Gunakan Chrome/Safari terbaru.');
            return;
        }
        stopCamera();
        var facing = useFrontCamera ? 'user' : 'environment';
        video.classList.toggle('panel-proof-upload__video--mirror', useFrontCamera);
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: facing }, width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false
            });
            video.srcObject = stream;
            empty.hidden = true;
            preview.hidden = true;
            camera.hidden = false;
        } catch (err) {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                video.srcObject = stream;
                empty.hidden = true;
                preview.hidden = true;
                camera.hidden = false;
            } catch (err2) {
                showError('Akses kamera ditolak atau tidak tersedia. Izinkan kamera di browser.');
                camera.hidden = false;
                empty.hidden = true;
            }
        }
    }

    function setProof(blob) {
        if (currentUrl) URL.revokeObjectURL(currentUrl);
        currentUrl = URL.createObjectURL(blob);
        img.src = currentUrl;
        filename.textContent = 'bukti-pengambilan-{{ $transactionId }}.jpg';
        filesize.textContent = formatSize(blob.size);
        empty.hidden = true;
        camera.hidden = true;
        preview.hidden = false;
        if (btnVerify) {
            btnVerify.disabled = false;
            btnVerify.title = '';
        }
    }

    var MAX_BYTES = 100 * 1024; // batas 100 KB untuk bukti

    function compressToLimit(sourceCanvas, done) {
        // Kombinasi skala & kualitas dari besar ke kecil sampai <= 100 KB
        var combos = [
            [1, 0.7], [1, 0.5], [0.85, 0.5], [0.75, 0.45],
            [0.6, 0.45], [0.5, 0.4], [0.4, 0.4], [0.35, 0.35],
        ];
        var i = 0;
        var last = null;

        function next() {
            if (i >= combos.length) { done(last); return; }
            var scale = combos[i][0];
            var quality = combos[i][1];
            i++;

            var c = document.createElement('canvas');
            c.width = Math.max(1, Math.round(sourceCanvas.width * scale));
            c.height = Math.max(1, Math.round(sourceCanvas.height * scale));
            c.getContext('2d').drawImage(sourceCanvas, 0, 0, c.width, c.height);

            c.toBlob(function (blob) {
                if (!blob) { next(); return; }
                last = blob;
                if (blob.size <= MAX_BYTES) { done(blob); return; }
                next();
            }, 'image/jpeg', quality);
        }

        next();
    }

    function capturePhoto() {
        if (!stream || !video.videoWidth) return;
        var ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        if (useFrontCamera) {
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
        }
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        stopCamera();

        compressToLimit(canvas, function (blob) {
            if (!blob) return;

            if (blob.size > MAX_BYTES) {
                showError('Foto masih di atas 100 KB. Coba ambil ulang dengan latar lebih sederhana.');
            } else {
                hideError();
            }

            setProof(blob);
            var fileInput = document.getElementById('approval-photo-input');
            if (fileInput) {
                var file = new File([blob], 'bukti-pengambilan.jpg', { type: 'image/jpeg' });
                var dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;
            }
        });
    }

    function resetToEmpty() {
        stopCamera();
        if (currentUrl) URL.revokeObjectURL(currentUrl);
        currentUrl = null;
        img.src = '';
        empty.hidden = false;
        camera.hidden = true;
        preview.hidden = true;
        hideError();
        if (btnVerify) {
            btnVerify.disabled = true;
            btnVerify.title = 'Ambil foto bukti dari kamera terlebih dahulu';
        }
    }

    setupDeviceLabel();
    openBtn.addEventListener('click', startCamera);
    document.getElementById('proof-capture').addEventListener('click', capturePhoto);
    document.getElementById('proof-cancel-camera').addEventListener('click', resetToEmpty);
    document.getElementById('proof-retake').addEventListener('click', function () {
        resetToEmpty();
        startCamera();
    });

    if (btnVerify) {
        btnVerify.addEventListener('click', function (e) {
            if (btnVerify.disabled) {
                e.preventDefault();
                return;
            }
            stopCamera();
        });
    }

    window.addEventListener('beforeunload', stopCamera);
})();
</script>
@endpush
