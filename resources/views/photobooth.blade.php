<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DIY Photobooth - Scrapbook Kayla</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="scrapbook-paper linen-texture min-h-screen text-espresso font-sans p-2 sm:p-4 md:p-8 flex flex-col justify-between relative overflow-x-hidden">
    
    <!-- Dekorasi Sudut -->
    <img src="/images/mockup_top_left.png" class="absolute top-0 left-0 w-20 sm:w-28 md:w-36 lg:w-52 opacity-95 pointer-events-none z-0 select-none" alt="Top Left">
    <img src="/images/mockup_top_right.png" class="absolute top-0 right-0 w-24 sm:w-32 md:w-44 lg:w-64 opacity-95 pointer-events-none z-0 select-none" alt="Top Right">
    <img src="/images/mockup_bottom_left.png" class="absolute bottom-0 left-0 w-24 sm:w-36 md:w-48 lg:w-72 opacity-95 pointer-events-none z-0 select-none" alt="Bottom Left">
    <img src="/images/mockup_bottom_right.png" class="absolute bottom-0 right-0 w-16 sm:w-20 md:w-28 lg:w-40 opacity-95 pointer-events-none z-0 select-none" alt="Bottom Right">
    
    <!-- Main Content Wrapper -->
    <div class="max-w-4xl w-full mx-auto relative z-10 flex-1 flex flex-col gap-4 sm:gap-6">
        
        <!-- Header -->
        <header class="flex justify-between items-center border-b border-cocoa-light/20 pb-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="px-3 py-1 bg-espresso text-cream-light font-serif text-sm rounded shadow hover:bg-cocoa-medium transition">
                    &larr; KEMBALI
                </a>
                <h1 class="font-serif text-2xl md:text-3xl font-bold text-espresso-dark">📸 DIY PHOTOBOOTH</h1>
            </div>
            <span class="font-hand text-xl text-cocoa-medium">3 Strip Foto Ulang Tahun</span>
        </header>

        <!-- Main Area -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 my-auto items-start">
            
            <!-- Left Side: Kamera & Input Foto -->
            <div class="md:col-span-7 bg-[#F4EFE6] border-2 border-espresso p-4 sm:p-6 rounded-xl shadow-md relative flex flex-col items-center gap-4">
                
                <!-- Placeholder Kamera -->
                <div id="camera-placeholder" class="w-full aspect-video rounded-lg border-2 border-dashed border-espresso bg-[#FAF7F2] flex flex-col items-center justify-center gap-2 p-4 text-center">
                    <span class="text-4xl sm:text-5xl">📷</span>
                    <h4 class="font-serif text-sm font-bold text-espresso-dark">Kamera Live / Unggah Foto</h4>
                    <p class="font-hand text-xs text-cocoa-medium max-w-sm">Aktifkan kamera live di bawah atau pilih foto langsung dari perangkat kamu.</p>
                </div>

                <!-- Live Webcam Box -->
                <div id="webcam-container" class="hidden relative w-full aspect-video bg-black rounded-lg overflow-hidden border-2 border-espresso shadow-inner">
                    <video id="webcam" class="w-full h-full object-cover transform -scale-x-100" autoplay playsinline muted></video>
                    
                    <div id="countdown-overlay" class="absolute inset-0 bg-black/60 hidden flex items-center justify-center text-8xl font-serif text-cream-light z-30">
                        <span id="countdown-number">3</span>
                    </div>

                    <div id="flash-overlay" class="absolute inset-0 pointer-events-none z-40 opacity-0 bg-white transition-opacity duration-200"></div>
                </div>

                <!-- 3 Thumbnail Slots -->
                <div class="flex gap-4 justify-center w-full">
                    <div id="slot-btn-0" class="w-24 aspect-[4/3] bg-espresso/10 border-2 border-dashed border-espresso rounded overflow-hidden relative cursor-pointer flex items-center justify-center hover:bg-espresso/20 transition">
                        <img id="thumb-0" class="w-full h-full object-cover hidden" alt="Foto 1">
                        <span id="label-0" class="font-hand text-xs text-cocoa-medium text-center font-bold">📸 FOTO 1</span>
                        <input type="file" id="photo-input-0" accept="image/*" class="hidden">
                    </div>
                    <div id="slot-btn-1" class="w-24 aspect-[4/3] bg-espresso/10 border-2 border-dashed border-espresso rounded overflow-hidden relative cursor-pointer flex items-center justify-center hover:bg-espresso/20 transition">
                        <img id="thumb-1" class="w-full h-full object-cover hidden" alt="Foto 2">
                        <span id="label-1" class="font-hand text-xs text-cocoa-medium text-center font-bold">📸 FOTO 2</span>
                        <input type="file" id="photo-input-1" accept="image/*" class="hidden">
                    </div>
                    <div id="slot-btn-2" class="w-24 aspect-[4/3] bg-espresso/10 border-2 border-dashed border-espresso rounded overflow-hidden relative cursor-pointer flex items-center justify-center hover:bg-espresso/20 transition">
                        <img id="thumb-2" class="w-full h-full object-cover hidden" alt="Foto 3">
                        <span id="label-2" class="font-hand text-xs text-cocoa-medium text-center font-bold">📸 FOTO 3</span>
                        <input type="file" id="photo-input-2" accept="image/*" class="hidden">
                    </div>
                </div>

                <!-- Form Kustomisasi Strip -->
                <div class="w-full bg-[#FAF7F2] p-4 rounded-lg border border-cocoa-light/20 flex flex-col gap-3 text-left">
                    <span class="font-serif text-xs font-bold text-espresso-dark uppercase tracking-wider">[ 🎨 KUSTOMISASI STRIP FOTO ]</span>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1">
                            <label class="font-serif text-[10px] font-bold text-espresso">Tulisan Atas (Header):</label>
                            <input type="text" id="header_text" value="Capturing Moments" class="text-xs bg-cream-light p-2 rounded border border-cocoa-light focus:outline-none">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="font-serif text-[10px] font-bold text-espresso">Tulisan Bawah (Footer):</label>
                            <input type="text" id="footer_text" value="On the road, 20!" class="text-xs bg-cream-light p-2 rounded border border-cocoa-light focus:outline-none">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="font-serif text-[10px] font-bold text-espresso">Gaya/Style Strip Foto:</label>
                            <select id="style_theme" class="text-xs bg-cream-light p-2 rounded border border-cocoa-light focus:outline-none">
                                <option value="classic_vintage" selected>Classic Vintage (Scrapbook) 📎</option>
                                <option value="denim_y2k">Denim Stars (Y2K Denim) 👖</option>
                                <option value="ppg_collage">Powerpuff Girls (PPG Collage) 🎀</option>
                                <option value="polaroid_printer">Retro Polaroid (Printer) 📸</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="font-serif text-[10px] font-bold text-espresso">Bentuk Frame Foto:</label>
                            <select id="photo_shape" class="text-xs bg-cream-light p-2 rounded border border-cocoa-light focus:outline-none">
                                <option value="square" selected>Retro Kotak (Standard)</option>
                                <option value="oval">Elips Kuno (Oval Referensi)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi Kamera -->
                <div class="flex flex-wrap gap-3 w-full justify-center mt-1">
                    <button id="start-camera-btn" type="button" class="px-5 py-2.5 bg-tiramisu-dark text-espresso font-serif font-bold rounded-lg border-2 border-espresso shadow hover:bg-tiramisu-light transition cursor-pointer">
                        📹 AKTIFKAN KAMERA
                    </button>
                    <button id="snap-btn" type="button" class="px-5 py-2.5 bg-espresso text-cream-light font-serif font-bold rounded-lg shadow hover:bg-cocoa-medium transition hidden cursor-pointer">
                        📸 MULAI CETAK 3 FOTO OTOMATIS
                    </button>
                </div>

                <!-- Input File Native -->
                <div class="border-t border-dashed border-cocoa-light/30 pt-3 w-full flex flex-col items-center gap-2">
                    <span class="font-hand text-xs text-cocoa-medium">Pilih 3 foto sekaligus dari penyimpanan laptop:</span>
                    <input type="file" id="manual-photos-input" multiple accept="image/*" class="text-xs text-espresso file:mr-3 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-bold file:bg-espresso file:text-cream-light hover:file:bg-cocoa-medium cursor-pointer">
                </div>
            </div>

            <!-- Right Side: Preview Output Strip -->
            <div class="md:col-span-5 flex flex-col items-center gap-4">
                <h3 class="font-serif text-lg font-bold text-espresso-dark">CETAKAN STRIP KAMU</h3>
                
                <div id="output-wrapper" class="w-full max-w-[260px] bg-cream-light p-4 rounded paper-border relative flex flex-col items-center justify-center min-h-[380px] border border-gray-200 shadow-md">
                    <div id="strip-placeholder" class="text-center p-6 flex flex-col items-center">
                        <span class="text-4xl mb-2">🎞️</span>
                        <p class="font-hand text-lg text-cocoa-medium">Strip fotomu yang sudah berdesain rapi akan muncul di sini.</p>
                    </div>
                    
                    <img id="final-strip" class="w-full h-auto hidden object-contain rounded shadow" alt="Stitched Photo Strip">
                </div>

                <a id="download-btn" href="#" download="kayla_photobooth_strip.png" class="px-5 py-2.5 bg-espresso text-cream-light font-serif font-bold rounded shadow hover:bg-cocoa-medium transition hidden">
                    💾 UNDUH STRIP FOTO
                </a>
            </div>

        </div>
    </div>

    <!-- Hidden Capture Canvas -->
    <canvas id="capture-canvas" class="hidden"></canvas>

    <script>
    (function() {
        window.addEventListener('load', function() {
            const video = document.getElementById('webcam');
            const canvas = document.getElementById('capture-canvas');
            const ctx = canvas ? canvas.getContext('2d') : null;
            const startBtn = document.getElementById('start-camera-btn');
            const snapBtn = document.getElementById('snap-btn');
            const countdownOverlay = document.getElementById('countdown-overlay');
            const countdownNum = document.getElementById('countdown-number');
            const flashOverlay = document.getElementById('flash-overlay');
            const manualInput = document.getElementById('manual-photos-input');

            let streams = null;
            let capturedImages = [null, null, null];

            // Helper Gambar Image async
            function loadImage(src) {
                return new Promise(function(resolve) {
                    if (!src) return resolve(null);
                    const img = new Image();
                    img.onload = function() { resolve(img); };
                    img.onerror = function() { resolve(null); };
                    img.src = src;
                });
            }

            // Helper Rounded Rect
            function drawRoundedRect(cCtx, x, y, width, height, radius, fillStyle = null, strokeStyle = null, lineWidth = 1) {
                cCtx.beginPath();
                if (typeof cCtx.roundRect === 'function') {
                    cCtx.roundRect(x, y, width, height, radius);
                } else {
                    cCtx.moveTo(x + radius, y);
                    cCtx.lineTo(x + width - radius, y);
                    cCtx.quadraticCurveTo(x + width, y, x + width, y + radius);
                    cCtx.lineTo(x + width, y + height - radius);
                    cCtx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
                    cCtx.lineTo(x + radius, y + height);
                    cCtx.quadraticCurveTo(x, y + height, x, y + height - radius);
                    cCtx.lineTo(x, y + radius);
                    cCtx.quadraticCurveTo(x, y, x + radius, y);
                    cCtx.closePath();
                }
                if (fillStyle) {
                    cCtx.fillStyle = fillStyle;
                    cCtx.fill();
                }
                if (strokeStyle) {
                    cCtx.strokeStyle = strokeStyle;
                    cCtx.lineWidth = lineWidth;
                    cCtx.stroke();
                }
            }

            // Helper Barcode
            function drawBarcode(cCtx, x, y, width, height, color) {
                cCtx.fillStyle = color;
                let currX = x;
                let seed = 42;
                function nextRand(min, max) {
                    seed = (seed * 9301 + 49297) % 233280;
                    return Math.floor(min + (seed / 233280) * (max - min + 1));
                }
                while (currX < x + width) {
                    const barW = nextRand(2, 6);
                    cCtx.fillRect(currX, y, barW, height);
                    currX += barW + nextRand(2, 5);
                }
            }

            // Helper render stiker proporsional (anti-penyet)
            function drawSticker(cCtx, img, x, y, maxW, maxH, rot = 0) {
                if (!img || !img.width || !img.height) return;
                const ratio = Math.min(maxW / img.width, maxH / img.height);
                const w = img.width * ratio;
                const h = img.height * ratio;
                cCtx.save();
                cCtx.translate(x, y);
                if (rot !== 0) cCtx.rotate(rot * Math.PI / 180);
                cCtx.drawImage(img, -w / 2, -h / 2, w, h);
                cCtx.restore();
            }

            // 1. Upload 3 Foto Sekaligus
            if (manualInput) {
                manualInput.addEventListener('change', async function(e) {
                    const files = e.target.files;
                    if (!files || files.length !== 3) {
                        alert("Harap pilih tepat 3 file foto sekaligus.");
                        return;
                    }

                    for (let i = 0; i < 3; i++) {
                        const dataUrl = await readFile(files[i]);
                        capturedImages[i] = dataUrl;
                        const thumb = document.getElementById('thumb-' + i);
                        const label = document.getElementById('label-' + i);
                        if (thumb) { thumb.src = dataUrl; thumb.classList.remove('hidden'); }
                        if (label) label.classList.add('hidden');
                    }

                    await renderStrip();
                });
            }

            // 2. Upload per Slot (1, 2, 3)
            [0, 1, 2].forEach(function(idx) {
                const slot = document.getElementById('slot-btn-' + idx);
                const input = document.getElementById('photo-input-' + idx);

                if (slot && input) {
                    slot.addEventListener('click', function() { input.click(); });
                    input.addEventListener('change', async function(e) {
                        const file = e.target.files[0];
                        if (!file) return;

                        const dataUrl = await readFile(file);
                        capturedImages[idx] = dataUrl;
                        const thumb = document.getElementById('thumb-' + idx);
                        const label = document.getElementById('label-' + idx);
                        if (thumb) { thumb.src = dataUrl; thumb.classList.remove('hidden'); }
                        if (label) label.classList.add('hidden');

                        if (capturedImages.filter(Boolean).length === 3) {
                            await renderStrip();
                        }
                    });
                }
            });

            // 3. Aktifkan Kamera (Fallback Constraint)
            if (startBtn) {
                startBtn.addEventListener('click', async function() {
                    try {
                        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                            alert('Kamera dibatasi pada koneksi ini. Gunakan tombol pilih foto di bawah.');
                            return;
                        }

                        streams = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                        video.srcObject = streams;
                        await video.play();

                        document.getElementById('camera-placeholder').classList.add('hidden');
                        document.getElementById('webcam-container').classList.remove('hidden');
                        startBtn.classList.add('hidden');
                        snapBtn.classList.remove('hidden');
                    } catch (err) {
                        alert('Kamera tidak dapat diakses (' + err.name + '). Silakan pilih foto langsung dari penyimpanan laptop.');
                    }
                });
            }

            // 4. Capture Otomatis 3 Foto
            if (snapBtn) {
                snapBtn.addEventListener('click', async function() {
                    capturedImages = [null, null, null];
                    snapBtn.disabled = true;
                    snapBtn.textContent = 'MENGAMBIL FOTO...';

                    for (let i = 0; i < 3; i++) {
                        await new Promise(function(resolve) {
                            countdownOverlay.classList.remove('hidden');
                            let cur = 3;
                            countdownNum.textContent = cur;
                            const timer = setInterval(function() {
                                cur--;
                                if (cur <= 0) {
                                    clearInterval(timer);
                                    countdownOverlay.classList.add('hidden');
                                    resolve();
                                } else {
                                    countdownNum.textContent = cur;
                                }
                            }, 800);
                        });

                        if (flashOverlay) {
                            flashOverlay.style.opacity = '1';
                            setTimeout(function() { flashOverlay.style.opacity = '0'; }, 200);
                        }

                        canvas.width = video.videoWidth || 640;
                        canvas.height = video.videoHeight || 480;
                        ctx.translate(canvas.width, 0);
                        ctx.scale(-1, 1);
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        ctx.setTransform(1, 0, 0, 1, 0, 0);

                        const dataUrl = canvas.toDataURL('image/png');
                        capturedImages[i] = dataUrl;
                        const thumb = document.getElementById('thumb-' + i);
                        const label = document.getElementById('label-' + i);
                        if (thumb) { thumb.src = dataUrl; thumb.classList.remove('hidden'); }
                        if (label) label.classList.add('hidden');

                        await new Promise(function(r) { setTimeout(r, 1000); });
                    }

                    snapBtn.disabled = false;
                    snapBtn.textContent = '📸 MULAI CETAK 3 FOTO OTOMATIS';
                    await renderStrip();
                });
            }

            // 5. Generator Jahit Desain Strip Foto (Full Design & Anti-Penyet)
            async function renderStrip() {
                const placeholder = document.getElementById('strip-placeholder');
                const finalStrip = document.getElementById('final-strip');
                const downloadBtn = document.getElementById('download-btn');

                placeholder.innerHTML = `
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-espresso mb-3 mx-auto"></div>
                    <p class="font-hand text-lg text-cocoa-medium">Sedang merajut strip foto kamu...</p>
                `;
                placeholder.classList.remove('hidden');
                finalStrip.classList.add('hidden');
                downloadBtn.classList.add('hidden');

                const headerText = document.getElementById('header_text').value || 'Capturing Moments';
                const footerText = document.getElementById('footer_text').value || 'On the road, 20!';
                const styleTheme = document.getElementById('style_theme').value || 'classic_vintage';
                const photoShape = document.getElementById('photo_shape').value || 'square';

                try {
                    const stripW = 600;
                    const photoW = 440;
                    const photoH = (photoShape === 'oval') ? 300 : 330;
                    const topPad = 200;
                    const gap = 25;
                    const botPad = 220;
                    const stripH = topPad + (3 * photoH) + (2 * gap) + botPad;

                    const c = document.createElement('canvas');
                    c.width = stripW;
                    c.height = stripH;
                    const cCtx = c.getContext('2d');

                    // ================= TEMA 1: CLASSIC VINTAGE =================
                    if (styleTheme === 'classic_vintage') {
                        cCtx.fillStyle = '#2B1B10';
                        cCtx.fillRect(0, 0, stripW, stripH);

                        const cardX1 = 50, cardY1 = 120, cardW = stripW - 100, cardH = stripH - 240;
                        const cardX2 = cardX1 + cardW, cardY2 = cardY1 + cardH;
                        drawRoundedRect(cCtx, cardX1, cardY1, cardW, cardH, 25, '#FDFCFA');

                        // Ticket notches di kiri & kanan tengah
                        const midY = Math.round((cardY1 + cardY2) / 2);
                        cCtx.fillStyle = '#2B1B10';
                        cCtx.beginPath();
                        cCtx.arc(cardX1, midY, 16, 0, Math.PI * 2);
                        cCtx.fill();
                        cCtx.beginPath();
                        cCtx.arc(cardX2, midY, 16, 0, Math.PI * 2);
                        cCtx.fill();

                        // Paper clip di atas kartu
                        drawRoundedRect(cCtx, stripW / 2 - 30, cardY1 - 20, 60, 30, 8, '#C0C0C0', '#5C4033', 2);
                        cCtx.fillStyle = '#808080';
                        cCtx.beginPath();
                        cCtx.arc(stripW / 2, cardY1 - 5, 6, 0, Math.PI * 2);
                        cCtx.fill();

                        // Barcode & Footer
                        drawBarcode(cCtx, cardX1 + 60, cardY2 - 80, cardW - 120, 40, '#5C4033');
                        cCtx.fillStyle = '#5C4033';
                        cCtx.font = '20px Georgia, serif';
                        cCtx.textAlign = 'center';
                        cCtx.textBaseline = 'middle';
                        cCtx.fillText(footerText, stripW / 2, cardY2 - 25);

                    // ================= TEMA 2: DENIM Y2K =================
                    } else if (styleTheme === 'denim_y2k') {
                        const denimImg = await loadImage('/images/denim_bg_collage.png');
                        if (denimImg) {
                            cCtx.drawImage(denimImg, 0, 0, stripW, stripH);
                        } else {
                            cCtx.fillStyle = '#14233C';
                            cCtx.fillRect(0, 0, stripW, stripH);
                        }

                        const cardX1 = 45, cardY1 = 50, cardW = stripW - 90, cardH = stripH - 100;
                        const cardX2 = cardX1 + cardW;
                        
                        // Double Border Vintage
                        drawRoundedRect(cCtx, cardX1, cardY1, cardW, cardH, 25, '#FCF8EE', '#8B2D2D', 3);
                        drawRoundedRect(cCtx, cardX1 + 6, cardY1 + 6, cardW - 12, cardH - 12, 20, null, '#8B2D2D', 1);

                        // Notches & Dashed line
                        const notchR = 16;
                        const denimBlue = '#325078';
                        [175, stripH - 175].forEach(divY => {
                            cCtx.fillStyle = denimBlue;
                            cCtx.beginPath();
                            cCtx.arc(cardX1, divY, notchR, 0, Math.PI * 2);
                            cCtx.fill();
                            cCtx.beginPath();
                            cCtx.arc(cardX2, divY, notchR, 0, Math.PI * 2);
                            cCtx.fill();

                            cCtx.save();
                            cCtx.strokeStyle = '#8B2D2D';
                            cCtx.lineWidth = 2;
                            cCtx.setLineDash([6, 6]);
                            cCtx.beginPath();
                            cCtx.moveTo(cardX1 + 16, divY);
                            cCtx.lineTo(cardX2 - 16, divY);
                            cCtx.stroke();
                            cCtx.restore();
                        });

                        // Movie Theatre Header
                        cCtx.fillStyle = '#8B2D2D';
                        cCtx.font = 'italic 32px Georgia, serif';
                        cCtx.textAlign = 'center';
                        cCtx.textBaseline = 'middle';
                        cCtx.fillText('Movie Theatre', stripW / 2, 95);

                        cCtx.font = 'bold 16px Georgia, serif';
                        cCtx.fillText('15 c', 130, 145);
                        cCtx.fillText('ADMIT ONE', stripW / 2, 145);
                        cCtx.fillText('ONE DAY', stripW - 130, 145);

                        cCtx.strokeStyle = '#8B2D2D';
                        cCtx.lineWidth = 2;
                        cCtx.beginPath();
                        cCtx.moveTo(200, 125); cCtx.lineTo(200, 165);
                        cCtx.moveTo(400, 125); cCtx.lineTo(400, 165);
                        cCtx.stroke();

                        // Barcode & Footer
                        drawBarcode(cCtx, cardX2 - 100, stripH - 145, 80, 65, '#8B2D2D');

                        cCtx.textAlign = 'left';
                        cCtx.fillStyle = '#8B2D2D';
                        cCtx.font = 'italic 30px Georgia, serif';
                        cCtx.fillText('HEY,', 80, stripH - 125);
                        cCtx.font = 'bold 24px Georgia, serif';
                        cCtx.fillText('GORGEOUS', 80, stripH - 95);
                        cCtx.font = '14px Georgia, serif';
                        cCtx.fillText('📍 ' + footerText, 80, stripH - 68);

                        // Flower sticker & admit one patch
                        const flowerImg = await loadImage('/images/flower_sticker_1.png');
                        if (flowerImg) drawSticker(cCtx, flowerImg, 45, 630, 75, 75, -5);

                        cCtx.save();
                        cCtx.translate(stripW - 120, 480);
                        cCtx.rotate(15 * Math.PI / 180);
                        drawRoundedRect(cCtx, 0, 0, 120, 50, 10, '#FFB4BE', '#8B2D2D', 2);
                        cCtx.fillStyle = '#8B2D2D';
                        cCtx.font = 'bold 13px Georgia, serif';
                        cCtx.textAlign = 'center';
                        cCtx.textBaseline = 'middle';
                        cCtx.fillText('ADMIT ONE', 60, 25);
                        cCtx.restore();

                    // ================= TEMA 3: PPG COLLAGE =================
                    } else if (styleTheme === 'ppg_collage') {
                        const ppgBg = await loadImage('/images/ppg_bg_collage.jpg');
                        if (ppgBg) {
                            cCtx.drawImage(ppgBg, 0, 0, stripW, stripH);
                        } else {
                            cCtx.fillStyle = '#FFB6C1';
                            cCtx.fillRect(0, 0, stripW, stripH);
                        }

                        const cardX1 = 50, cardY1 = 120, cardW = stripW - 100, cardH = stripH - 240;
                        const cardY2 = cardY1 + cardH;
                        drawRoundedRect(cCtx, cardX1, cardY1, cardW, cardH, 25, 'rgba(253, 252, 248, 0.94)', '#5C4033', 2);

                        cCtx.fillStyle = '#5C4033';
                        cCtx.font = 'bold 20px Georgia, serif';
                        cCtx.textAlign = 'center';
                        cCtx.textBaseline = 'middle';
                        cCtx.fillText(footerText, stripW / 2, cardY2 - 25);

                    // ================= TEMA 4: POLAROID PRINTER =================
                    } else if (styleTheme === 'polaroid_printer') {
                        const polBg = await loadImage('/images/polaroid_bg_collage.jpg');
                        if (polBg) {
                            cCtx.drawImage(polBg, 0, 0, stripW, stripH);
                        } else {
                            cCtx.fillStyle = '#8B5A2B';
                            cCtx.fillRect(0, 0, stripW, stripH);
                        }

                        cCtx.fillStyle = '#FFFFFF';
                        cCtx.font = 'bold 20px Georgia, serif';
                        cCtx.textAlign = 'center';
                        cCtx.textBaseline = 'middle';
                        cCtx.fillText(footerText, stripW / 2, stripH - 60);
                    }

                    // Header Banner (non-denim)
                    if (styleTheme !== 'denim_y2k') {
                        const bannerW = 440, bannerH = 70;
                        const bx1 = (stripW - bannerW) / 2, by1 = 35;
                        drawRoundedRect(cCtx, bx1, by1, bannerW, bannerH, 15, 'rgba(253, 252, 248, 0.95)', '#5C4033', 3);

                        cCtx.fillStyle = '#5C4033';
                        cCtx.font = 'bold 30px Georgia, serif';
                        cCtx.textAlign = 'center';
                        cCtx.textBaseline = 'middle';
                        cCtx.fillText(headerText, stripW / 2, by1 + bannerH / 2);
                    }

                    // Render 3 Foto
                    let currentY = topPad;
                    const pasteX = (stripW - photoW) / 2;

                    for (let i = 0; i < 3; i++) {
                        const imgData = capturedImages[i];
                        if (!imgData) continue;
                        const img = await loadImage(imgData);
                        if (!img) continue;

                        const targetRatio = photoW / photoH;
                        const currentRatio = img.width / img.height;
                        let sx = 0, sy = 0, sw = img.width, sh = img.height;
                        if (currentRatio > targetRatio) {
                            sh = img.height;
                            sw = sh * targetRatio;
                            sx = (img.width - sw) / 2;
                        } else {
                            sw = img.width;
                            sh = sw / targetRatio;
                            sy = (img.height - sh) / 2;
                        }

                        // Polaroid white frame backing
                        if (styleTheme === 'polaroid_printer') {
                            const frameW = photoW + 30, frameH = photoH + 20;
                            const frameX = (stripW - frameW) / 2;
                            drawRoundedRect(cCtx, frameX, currentY - 10, frameW, frameH, 4, '#FFFFFF', '#000000', 2);
                        }

                        cCtx.save();
                        cCtx.beginPath();
                        if (photoShape === 'oval') {
                            cCtx.ellipse(pasteX + photoW / 2, currentY + photoH / 2, photoW / 2, photoH / 2, 0, 0, Math.PI * 2);
                        } else if (styleTheme === 'denim_y2k') {
                            if (typeof cCtx.roundRect === 'function') {
                                cCtx.roundRect(pasteX, currentY, photoW, photoH, 20);
                            } else {
                                cCtx.rect(pasteX, currentY, photoW, photoH);
                            }
                        } else {
                            cCtx.rect(pasteX, currentY, photoW, photoH);
                        }
                        cCtx.clip();
                        cCtx.drawImage(img, sx, sy, sw, sh, pasteX, currentY, photoW, photoH);
                        cCtx.restore();

                        // Border garis luar foto
                        const borderColor = (styleTheme === 'denim_y2k') ? '#8B2D2D' : '#5C4033';
                        cCtx.strokeStyle = borderColor;
                        cCtx.lineWidth = (photoShape === 'oval' || styleTheme === 'denim_y2k') ? 4 : 3;

                        cCtx.beginPath();
                        if (photoShape === 'oval') {
                            cCtx.ellipse(pasteX + photoW / 2, currentY + photoH / 2, photoW / 2, photoH / 2, 0, 0, Math.PI * 2);
                            cCtx.stroke();
                        } else if (styleTheme === 'denim_y2k') {
                            if (typeof cCtx.roundRect === 'function') {
                                cCtx.roundRect(pasteX, currentY, photoW, photoH, 20);
                                cCtx.stroke();
                            } else {
                                cCtx.strokeRect(pasteX, currentY, photoW, photoH);
                            }
                        } else {
                            cCtx.strokeRect(pasteX, currentY, photoW, photoH);
                        }

                        currentY += photoH + gap;
                    }

                    // Render Stiker PPG di Atas Layer Foto (Proporsional & Tajam)
                    if (styleTheme === 'ppg_collage') {
                        const [blossom, bubbles, buttercup, flower] = await Promise.all([
                            loadImage('/images/blossom_sticker.png'),
                            loadImage('/images/bubbles_sticker.png'),
                            loadImage('/images/buttercup_sticker.png'),
                            loadImage('/images/flower_sticker_1.png')
                        ]);

                        drawSticker(cCtx, blossom, 110, 150, 130, 130, -8);
                        drawSticker(cCtx, bubbles, stripW - 110, 150, 130, 130, 8);
                        drawSticker(cCtx, flower, 85, stripH - 210, 80, 80, -12);
                        drawSticker(cCtx, buttercup, stripW - 105, stripH - 200, 130, 130, 6);
                    }

                    const resultUrl = c.toDataURL('image/png');
                    finalStrip.src = resultUrl;
                    downloadBtn.href = resultUrl;
                    placeholder.classList.add('hidden');
                    finalStrip.classList.remove('hidden');
                    downloadBtn.classList.remove('hidden');
                } catch (err) {
                    console.error(err);
                    alert("Gagal merajut strip: " + err.message);
                    placeholder.classList.remove('hidden');
                }
            }

            function readFile(file) {
                return new Promise(function(resolve) {
                    const reader = new FileReader();
                    reader.onload = function(e) { resolve(e.target.result); };
                    reader.readAsDataURL(file);
                });
            }

            // Live Update jika opsi style / tulisan / bentuk diubah
            ['header_text', 'footer_text', 'style_theme', 'photo_shape'].forEach(function(id) {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', function() {
                        if (capturedImages.filter(Boolean).length === 3) renderStrip();
                    });
                    el.addEventListener('change', function() {
                        if (capturedImages.filter(Boolean).length === 3) renderStrip();
                    });
                }
            });
        });
    })();
    </script>
</body>
</html>