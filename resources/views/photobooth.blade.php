<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DIY Photobooth - Scrapbook Kayla</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="scrapbook-paper linen-texture min-h-screen text-espresso font-sans p-2 sm:p-4 md:p-8 flex flex-col justify-between relative">
    
    <!-- User designed Mockup Corner Decorations (Pinggir web) -->
    <img src="{{ asset('images/mockup_top_left.png') }}" class="absolute top-0 left-0 w-20 sm:w-28 md:w-36 lg:w-52 opacity-95 pointer-events-none z-10 select-none" alt="PPG Group Left">
    <img src="{{ asset('images/mockup_top_right.png') }}" class="absolute top-0 right-0 w-24 sm:w-32 md:w-44 lg:w-64 opacity-95 pointer-events-none z-0 select-none" alt="Newspaper Collage Right">
    <img src="{{ asset('images/mockup_bottom_left.png') }}" class="absolute bottom-0 left-0 w-24 sm:w-36 md:w-48 lg:w-72 opacity-95 pointer-events-none z-0 select-none" alt="Newspaper Tulip Left">
    <img src="{{ asset('images/mockup_bottom_right.png') }}" class="absolute bottom-0 right-0 w-16 sm:w-20 md:w-28 lg:w-40 opacity-95 pointer-events-none z-10 select-none" alt="Blossom Guitar Right">
    
    
    <!-- Header -->
    <div class="max-w-4xl w-full mx-auto z-10 flex-1 flex flex-col gap-4 sm:gap-6">
        <header class="flex justify-between items-center border-b border-cocoa-light/20 pb-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="px-3 py-1 bg-espresso text-cream-light font-serif text-sm rounded shadow hover:bg-cocoa-medium transition">
                    &larr; KEMBALI
                </a>
                <h1 class="font-serif text-2xl md:text-3xl font-bold text-espresso-dark">📸 DIY PHOTOBOOTH</h1>
            </div>
            <span class="font-hand text-xl text-cocoa-medium">3 Strip Foto Ulang Tahun</span>
        </header>

        <!-- Main Area: Left (Webcam & Controls), Right (Preview Strip) -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 my-auto items-start">
            
            <!-- Left Side: Webcam and Snap Actions -->
            <div class="md:col-span-7 bg-[#F4EFE6] border-2 border-espresso p-3 sm:p-6 rounded-xl shadow-md relative flex flex-col items-center gap-4">
                
                <!-- Camera Feed Container -->
                <!-- Camera Active Placeholder -->
                <div id="camera-placeholder" class="w-full aspect-video rounded-lg border-2 border-dashed border-espresso bg-[#FAF7F2] flex flex-col items-center justify-center gap-2 p-4 text-center">
                    <span class="text-4xl sm:text-5xl">📷</span>
                    <h4 class="font-serif text-sm font-bold text-espresso-dark">Kamera Belum Aktif</h4>
                    <p class="font-hand text-xs text-cocoa-medium max-w-sm">Aktifkan kamera live laptop kamu dengan tombol di bawah, ATAU langsung ketuk slot foto di bawah untuk menjepret langsung/unggah file.</p>
                </div>

                <!-- Camera Feed Container -->
                <div id="webcam-container" class="hidden relative w-full aspect-video bg-black rounded-lg overflow-hidden border-2 border-espresso shadow-inner">
                    <video id="webcam" class="w-full h-full object-cover transform -scale-x-100" autoplay playsinline></video>
                    
                    <!-- Countdown Overlay -->
                    <div id="countdown-overlay" class="absolute inset-0 bg-black/60 hidden flex items-center justify-center text-8xl font-serif text-cream-light z-30">
                        <span id="countdown-number" class="countdown-pulse">3</span>
                    </div>

                    <!-- Screen Flash Overlay -->
                    <div id="flash-overlay" class="absolute inset-0 pointer-events-none z-40 opacity-0"></div>
                </div>

                <!-- Mobile Camera Guide Box (shown when secure webcam context is not available) -->
                <div id="mobile-camera-guide" class="hidden w-full p-4 sm:p-6 rounded-lg border-2 border-dashed border-espresso bg-[#FAF7F2] text-center flex flex-col items-center gap-2 sm:gap-3">
                    <span class="text-3xl sm:text-4xl">📸</span>
                    <h4 class="font-serif text-sm font-bold text-espresso-dark">Kamera HP / Perangkat Aktif</h4>
                    <p class="font-hand text-xs sm:text-sm text-cocoa-medium">Silakan ketuk tombol **AMBIL FOTO 1**, **2**, dan **3** di bawah untuk menjepret langsung menggunakan kamera HP kamu atau memilih foto dari galeri!</p>
                </div>

                <!-- Captured Thumbnails Bar (Interactive camera/upload slots for secure and non-secure devices) -->
                <div class="flex gap-4 justify-center w-full">
                    <label for="photo-input-0" class="w-20 aspect-[4/3] bg-espresso/10 border border-espresso rounded overflow-hidden relative cursor-pointer block hover:bg-espresso/20 transition shadow-sm">
                        <img id="thumb-0" class="w-full h-full object-cover hidden" alt="">
                        <span id="label-0" class="absolute inset-0 flex items-center justify-center font-hand text-[10px] text-cocoa-medium text-center p-1 font-bold">📸 AMBIL FOTO 1</span>
                        <input type="file" id="photo-input-0" accept="image/*" capture="user" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); border: 0;" onchange="handleSinglePhoto(0, event)">
                    </label>
                    <label for="photo-input-1" class="w-20 aspect-[4/3] bg-espresso/10 border border-espresso rounded overflow-hidden relative cursor-pointer block hover:bg-espresso/20 transition shadow-sm">
                        <img id="thumb-1" class="w-full h-full object-cover hidden" alt="">
                        <span id="label-1" class="absolute inset-0 flex items-center justify-center font-hand text-[10px] text-cocoa-medium text-center p-1 font-bold">📸 AMBIL FOTO 2</span>
                        <input type="file" id="photo-input-1" accept="image/*" capture="user" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); border: 0;" onchange="handleSinglePhoto(1, event)">
                    </label>
                    <label for="photo-input-2" class="w-20 aspect-[4/3] bg-espresso/10 border border-espresso rounded overflow-hidden relative cursor-pointer block hover:bg-espresso/20 transition shadow-sm">
                        <img id="thumb-2" class="w-full h-full object-cover hidden" alt="">
                        <span id="label-2" class="absolute inset-0 flex items-center justify-center font-hand text-[10px] text-cocoa-medium text-center p-1 font-bold">📸 AMBIL FOTO 3</span>
                        <input type="file" id="photo-input-2" accept="image/*" capture="user" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); border: 0;" onchange="handleSinglePhoto(2, event)">
                    </label>
                </div>
                <!-- Customization Form -->
                <div class="w-full bg-[#FAF7F2] p-4 rounded-lg border border-cocoa-light/20 flex flex-col gap-3 mt-2 text-left">
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
                            <label class="font-serif text-[10px] font-bold text-espresso">Bentuk Foto:</label>
                            <select id="photo_shape" class="text-xs bg-cream-light p-2 rounded border border-cocoa-light focus:outline-none">
                                <option value="square" selected>Retro Kotak (Standard)</option>
                                <option value="oval">Elips Kuno (Oval Referensi)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Controls -->
                <div class="flex gap-4 w-full justify-center">
                    <button id="start-btn" onclick="startCamera()" class="px-5 py-2.5 bg-tiramisu-dark text-espresso font-serif font-bold rounded-lg border-2 border-espresso shadow hover:bg-tiramisu-light transition">
                        AKTIFKAN KAMERA
                    </button>
                    <button id="snap-btn" onclick="captureSequence()" class="px-5 py-2.5 bg-espresso text-cream-light font-serif font-bold rounded-lg shadow hover:bg-cocoa-medium transition hidden">
                        MULAI CETAK 3 FOTO
                    </button>
                </div>

                <!-- Fallback Manual Upload -->
                <div class="mt-2 border-t border-dashed border-cocoa-light/30 pt-4 w-full flex flex-col items-center gap-2">
                    <span class="font-hand text-xs text-cocoa-medium">Kamera tidak berfungsi?</span>
                    <label for="manual-photos" class="cursor-pointer px-4 py-2 bg-tiramisu-light/50 border border-espresso rounded text-xs font-serif font-bold hover:bg-tiramisu-light transition">
                        📁 UNGGAH 3 FOTO SEKALIGUS
                    </label>
                    <input type="file" id="manual-photos" multiple accept="image/*" class="hidden" onchange="handleManualPhotos(event)">
                </div>
            </div>

            <!-- Right Side: Final Strip Output -->
            <div class="md:col-span-5 flex flex-col items-center gap-4">
                <h3 class="font-serif text-lg font-bold text-espresso-dark">CETAKAN STRIP KAMU</h3>
                
                <!-- Stitched Strip Wrapper -->
                <div id="output-wrapper" class="w-full max-w-[240px] bg-cream-light p-4 rounded paper-border relative flex flex-col items-center justify-center min-h-[360px] border border-gray-200">
                    <div id="strip-placeholder" class="text-center p-6 flex flex-col items-center">
                        <span class="text-4xl mb-2">🎞️</span>
                        <p class="font-hand text-lg text-cocoa-medium">Foto kamu yang sudah dijahit akan muncul di sini.</p>
                    </div>
                    
                    <img id="final-strip" class="w-full h-auto hidden object-contain" alt="Stitched Photo Strip">
                </div>

                <!-- Download Action -->
                <a id="download-btn" href="#" download="kayla_photobooth_strip.png" class="px-5 py-2.5 bg-espresso text-cream-light font-serif font-bold rounded shadow hover:bg-cocoa-medium transition hidden">
                    💾 UNDUH STRIP FOTO
                </a>
            </div>

        </div>

    <!-- Hidden Capture Canvas -->
    <canvas id="capture-canvas" class="hidden"></canvas>

    <!-- Sound Effects -->
    <audio id="sound-shutter" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-200.wav"></audio>

    <script>
        const video = document.getElementById('webcam');
        const canvas = document.getElementById('capture-canvas');
        const ctx = canvas.getContext('2d');
        const startBtn = document.getElementById('start-btn');
        const snapBtn = document.getElementById('snap-btn');
        const countdownOverlay = document.getElementById('countdown-overlay');
        const countdownNum = document.getElementById('countdown-number');
        const flashOverlay = document.getElementById('flash-overlay');
        const soundShutter = document.getElementById('sound-shutter');
        
        let streams = null;
        let capturedImages = [];

        async function startCamera() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Kamera tidak didukung karena koneksi tidak aman (HTTP). Silakan gunakan tombol "UNGGAH 3 FOTO SEKALIGUS" di bagian bawah, atau jalankan situs web ini menggunakan HTTPS.');
                return;
            }
            try {
                streams = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } });
                video.srcObject = streams;
                
                // Swap placeholder with live stream feed
                const placeholder = document.getElementById('camera-placeholder');
                const webcamContainer = document.getElementById('webcam-container');
                if (placeholder) placeholder.classList.add('hidden');
                if (webcamContainer) webcamContainer.classList.remove('hidden');
                
                startBtn.classList.add('hidden');
                snapBtn.classList.remove('hidden');
            } catch (err) {
                alert('Gagal mengakses kamera: ' + err.message + '\n\nSilakan gunakan tombol "UNGGAH 3 FOTO SEKALIGUS" di bawah.');
            }
        }

        function playShutter() {
            soundShutter.currentTime = 0;
            soundShutter.play().catch(() => {});
        }

        async function captureSequence() {
            capturedImages = [];
            snapBtn.disabled = true;
            snapBtn.textContent = 'MENGAMBIL FOTO...';
            
            // Clean thumbnails
            for(let i=0; i<3; i++) {
                document.getElementById(`thumb-${i}`).classList.add('hidden');
                document.getElementById(`label-${i}`).classList.remove('hidden');
            }

            for (let i = 0; i < 3; i++) {
                await runCountdown(3);
                triggerFlash();
                playShutter();
                capturePhoto(i);
                await delay(1500); // 1.5s delay before next photo
            }

            // After capturing 3, send to backend
            await sendToBackend();
        }

        function runCountdown(seconds) {
            return new Promise((resolve) => {
                countdownOverlay.classList.remove('hidden');
                let cur = seconds;
                countdownNum.textContent = cur;
                
                const interval = setInterval(() => {
                    cur--;
                    if (cur <= 0) {
                        clearInterval(interval);
                        countdownOverlay.classList.add('hidden');
                        resolve();
                    } else {
                        countdownNum.textContent = cur;
                    }
                }, 800);
            });
        }

        function triggerFlash() {
            flashOverlay.classList.add('flash-effect');
            setTimeout(() => {
                flashOverlay.classList.remove('flash-effect');
            }, 400);
        }

        function capturePhoto(index) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            // Draw frame to canvas mirrored
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            ctx.setTransform(1, 0, 0, 1, 0, 0); // reset transform

            const dataUrl = canvas.toDataURL('image/png');
            capturedImages.push(dataUrl);

            // Update thumb
            const thumb = document.getElementById(`thumb-${index}`);
            const label = document.getElementById(`label-${index}`);
            thumb.src = dataUrl;
            thumb.classList.remove('hidden');
            label.classList.add('hidden');
        }

        function loadImage(src) {
            return new Promise((resolve) => {
                if (!src) return resolve(null);
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = () => resolve(img);
                img.onerror = () => resolve(null);
                img.src = src;
            });
        }

        function drawRoundedRect(ctx, x, y, width, height, radius, fillStyle = null, strokeStyle = null, lineWidth = 1) {
            ctx.beginPath();
            if (typeof ctx.roundRect === 'function') {
                ctx.roundRect(x, y, width, height, radius);
            } else {
                ctx.moveTo(x + radius, y);
                ctx.lineTo(x + width - radius, y);
                ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
                ctx.lineTo(x + width, y + height - radius);
                ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
                ctx.lineTo(x + radius, y + height);
                ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
                ctx.lineTo(x, y + radius);
                ctx.quadraticCurveTo(x, y, x + radius, y);
                ctx.closePath();
            }
            if (fillStyle) {
                ctx.fillStyle = fillStyle;
                ctx.fill();
            }
            if (strokeStyle) {
                ctx.strokeStyle = strokeStyle;
                ctx.lineWidth = lineWidth;
                ctx.stroke();
            }
        }

        function drawBarcode(ctx, x, y, width, height, color) {
            ctx.fillStyle = color;
            let currX = x;
            let seed = 42;
            function nextRand(min, max) {
                seed = (seed * 9301 + 49297) % 233280;
                const rnd = seed / 233280;
                return Math.floor(min + rnd * (max - min + 1));
            }
            while (currX < x + width) {
                const barW = nextRand(2, 6);
                ctx.fillRect(currX, y, barW, height);
                currX += barW + nextRand(2, 5);
            }
        }

        async function generateStripClientSide() {
            const placeholder = document.getElementById('strip-placeholder');
            const finalStrip = document.getElementById('final-strip');
            const downloadBtn = document.getElementById('download-btn');
            
            placeholder.innerHTML = `
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-espresso mb-3"></div>
                <p class="font-hand text-lg text-cocoa-medium">Sedang mencetak foto strip kustom kamu...</p>
            `;
            placeholder.classList.remove('hidden');
            finalStrip.classList.add('hidden');
            downloadBtn.classList.add('hidden');

            const headerText = document.getElementById('header_text').value || 'Capturing Moments';
            const footerText = document.getElementById('footer_text').value || 'On the road, 20!';
            const styleTheme = document.getElementById('style_theme').value || 'classic_vintage';
            const photoShape = document.getElementById('photo_shape').value || 'square';

            try {
                // Dimensions matching original Python script
                const stripW = 600;
                const photoW = 440;
                const photoH = (photoShape === 'oval') ? 300 : 330;
                const topPad = 200;
                const gap = 25;
                const botPad = 220;
                const stripH = topPad + (3 * photoH) + (2 * gap) + botPad;

                const canvas = document.createElement('canvas');
                canvas.width = stripW;
                canvas.height = stripH;
                const ctx = canvas.getContext('2d');

                // 1. Background rendering
                if (styleTheme === 'classic_vintage') {
                    ctx.fillStyle = '#2B1B10';
                    ctx.fillRect(0, 0, stripW, stripH);

                    // Card wrapper
                    const cardX1 = 50, cardY1 = 120, cardW = stripW - 100, cardH = stripH - 240;
                    const cardX2 = cardX1 + cardW, cardY2 = cardY1 + cardH;
                    drawRoundedRect(ctx, cardX1, cardY1, cardW, cardH, 25, '#FDFCFA');

                    // Ticket notches
                    const midY = Math.round((cardY1 + cardY2) / 2);
                    ctx.fillStyle = '#2B1B10';
                    ctx.beginPath();
                    ctx.arc(cardX1, midY, 16, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.beginPath();
                    ctx.arc(cardX2, midY, 16, 0, Math.PI * 2);
                    ctx.fill();

                    // Clip at top
                    drawRoundedRect(ctx, stripW / 2 - 30, cardY1 - 20, 60, 30, 8, '#C0C0C0', '#5C4033', 2);
                    ctx.fillStyle = '#808080';
                    ctx.beginPath();
                    ctx.arc(stripW / 2, cardY1 - 5, 6, 0, Math.PI * 2);
                    ctx.fill();

                    // Barcode & footer
                    drawBarcode(ctx, cardX1 + 60, cardY2 - 80, cardW - 120, 40, '#5C4033');
                    ctx.fillStyle = '#5C4033';
                    ctx.font = '20px Georgia, serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(footerText, stripW / 2, cardY2 - 25);

                } else if (styleTheme === 'denim_y2k') {
                    const denimImg = await loadImage('/images/denim_bg_collage.png');
                    if (denimImg) {
                        ctx.drawImage(denimImg, 0, 0, stripW, stripH);
                    } else {
                        ctx.fillStyle = (photoShape === 'square') ? '#14233C' : '#4C6E8D';
                        ctx.fillRect(0, 0, stripW, stripH);
                    }

                    const cardX1 = 45, cardY1 = 50, cardW = stripW - 90, cardH = stripH - 100;
                    const cardX2 = cardX1 + cardW, cardY2 = cardY1 + cardH;
                    
                    // Main card with double borders
                    drawRoundedRect(ctx, cardX1, cardY1, cardW, cardH, 25, '#FCF8EE', '#8B2D2D', 3);
                    drawRoundedRect(ctx, cardX1 + 6, cardY1 + 6, cardW - 12, cardH - 12, 20, null, '#8B2D2D', 1);

                    // Dividers & notches
                    const notchR = 16;
                    const denimBlue = '#325078';
                    [175, stripH - 175].forEach(divY => {
                        ctx.fillStyle = denimBlue;
                        ctx.beginPath();
                        ctx.arc(cardX1, divY, notchR, 0, Math.PI * 2);
                        ctx.fill();
                        ctx.beginPath();
                        ctx.arc(cardX2, divY, notchR, 0, Math.PI * 2);
                        ctx.fill();

                        // Dashed line
                        ctx.save();
                        ctx.strokeStyle = '#8B2D2D';
                        ctx.lineWidth = 2;
                        ctx.setLineDash([6, 6]);
                        ctx.beginPath();
                        ctx.moveTo(cardX1 + 16, divY);
                        ctx.lineTo(cardX2 - 16, divY);
                        ctx.stroke();
                        ctx.restore();
                    });

                    // Ticket header typography
                    ctx.fillStyle = '#8B2D2D';
                    ctx.font = 'italic 32px Georgia, serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText('Movie Theatre', stripW / 2, 95);

                    ctx.font = 'bold 16px Georgia, serif';
                    ctx.fillText('15 c', 130, 145);
                    ctx.fillText('ADMIT ONE', stripW / 2, 145);
                    ctx.fillText('ONE DAY', stripW - 130, 145);

                    ctx.strokeStyle = '#8B2D2D';
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    ctx.moveTo(200, 125); ctx.lineTo(200, 165);
                    ctx.moveTo(400, 125); ctx.lineTo(400, 165);
                    ctx.stroke();

                    // Barcode bottom right
                    drawBarcode(ctx, cardX2 - 100, stripH - 145, 80, 65, '#8B2D2D');

                    // Ticket footer details
                    ctx.textAlign = 'left';
                    ctx.fillStyle = '#8B2D2D';
                    ctx.font = 'italic 30px Georgia, serif';
                    ctx.fillText('HEY,', 80, stripH - 125);
                    ctx.font = 'bold 24px Georgia, serif';
                    ctx.fillText('GORGEOUS', 80, stripH - 95);
                    ctx.font = '14px Georgia, serif';
                    ctx.fillText('📍 ' + footerText, 80, stripH - 68);

                    // Flower sticker
                    const flowerImg = await loadImage('/images/flower_sticker_1.png');
                    if (flowerImg) {
                        ctx.drawImage(flowerImg, 15, 600, 70, 70);
                    }

                    // Tilted "ADMIT ONE" ticket
                    ctx.save();
                    ctx.translate(stripW - 120, 480);
                    ctx.rotate(15 * Math.PI / 180);
                    drawRoundedRect(ctx, 0, 0, 120, 50, 10, '#FFB4BE', '#8B2D2D', 2);
                    ctx.fillStyle = '#8B2D2D';
                    ctx.font = 'bold 13px Georgia, serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText('ADMIT ONE', 60, 25);
                    ctx.restore();

                } else if (styleTheme === 'ppg_collage') {
                    const ppgBg = await loadImage('/images/ppg_bg_collage.jpg');
                    if (ppgBg) {
                        ctx.drawImage(ppgBg, 0, 0, stripW, stripH);
                    } else {
                        ctx.fillStyle = '#FFC0CB';
                        ctx.fillRect(0, 0, stripW, stripH);
                    }

                    // White overlay card
                    const cardX1 = 50, cardY1 = 130, cardW = stripW - 100, cardH = stripH - 260;
                    const cardY2 = cardY1 + cardH;
                    drawRoundedRect(ctx, cardX1, cardY1, cardW, cardH, 25, 'rgba(253, 252, 248, 0.90)');

                    ctx.fillStyle = '#5C4033';
                    ctx.font = 'bold 20px Georgia, serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(footerText, stripW / 2, cardY2 - 25);

                    // Powerpuff Girls Stickers
                    const [blossom, bubbles, buttercup, flower] = await Promise.all([
                        loadImage('/images/blossom_sticker.png'),
                        loadImage('/images/bubbles_sticker.png'),
                        loadImage('/images/buttercup_sticker.png'),
                        loadImage('/images/flower_sticker_1.png')
                    ]);

                    if (blossom) ctx.drawImage(blossom, 45, 95, 140, 140);
                    if (bubbles) ctx.drawImage(bubbles, stripW - 180, 95, 140, 140);
                    if (flower) ctx.drawImage(flower, 35, stripH - 240, 90, 90);
                    if (buttercup) ctx.drawImage(buttercup, stripW - 180, stripH - 255, 140, 140);

                } else if (styleTheme === 'polaroid_printer') {
                    const polBg = await loadImage('/images/polaroid_bg_collage.jpg');
                    if (polBg) {
                        ctx.drawImage(polBg, 0, 0, stripW, stripH);
                    } else {
                        ctx.fillStyle = '#8B5A2B';
                        ctx.fillRect(0, 0, stripW, stripH);
                    }

                    ctx.fillStyle = '#FFFFFF';
                    ctx.font = 'bold 20px Georgia, serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(footerText, stripW / 2, stripH - 60);
                }

                // 2. Render Header Banner (for non-denim themes)
                if (styleTheme !== 'denim_y2k') {
                    const bannerW = 440, bannerH = 70;
                    const bx1 = (stripW - bannerW) / 2, by1 = 35;
                    drawRoundedRect(ctx, bx1, by1, bannerW, bannerH, 15, 'rgba(253, 252, 248, 0.95)', '#5C4033', 3);

                    ctx.fillStyle = '#5C4033';
                    ctx.font = 'bold 30px Georgia, serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(headerText, stripW / 2, by1 + bannerH / 2);
                }

                // 3. Render Photos
                let currentY = topPad;
                const pasteX = (stripW - photoW) / 2;

                for (let i = 0; i < 3; i++) {
                    const img = await loadImage(capturedImages[i]);
                    if (!img) continue;

                    // Compute aspect ratio crop
                    const targetRatio = photoW / photoH;
                    const currentRatio = img.width / img.height;
                    let sx, sy, sw, sh;
                    if (currentRatio > targetRatio) {
                        sh = img.height;
                        sw = sh * targetRatio;
                        sx = (img.width - sw) / 2;
                        sy = 0;
                    } else {
                        sw = img.width;
                        sh = sw / targetRatio;
                        sx = 0;
                        sy = (img.height - sh) / 2;
                    }

                    // Polaroid white frame backing
                    if (styleTheme === 'polaroid_printer') {
                        const frameW = photoW + 30, frameH = photoH + 20;
                        const frameX = (stripW - frameW) / 2;
                        drawRoundedRect(ctx, frameX, currentY - 10, frameW, frameH, 4, '#FFFFFF', '#000000', 2);
                    }

                    // Clip and draw image
                    ctx.save();
                    ctx.beginPath();
                    if (photoShape === 'oval') {
                        ctx.ellipse(pasteX + photoW / 2, currentY + photoH / 2, photoW / 2, photoH / 2, 0, 0, Math.PI * 2);
                    } else if (styleTheme === 'denim_y2k') {
                        if (typeof ctx.roundRect === 'function') {
                            ctx.roundRect(pasteX, currentY, photoW, photoH, 20);
                        } else {
                            ctx.rect(pasteX, currentY, photoW, photoH);
                        }
                    } else {
                        ctx.rect(pasteX, currentY, photoW, photoH);
                    }
                    ctx.clip();
                    ctx.drawImage(img, sx, sy, sw, sh, pasteX, currentY, photoW, photoH);
                    ctx.restore();

                    // Outer stroke border
                    const borderColor = (styleTheme === 'denim_y2k') ? '#8B2D2D' : '#5C4033';
                    ctx.strokeStyle = borderColor;
                    ctx.lineWidth = (photoShape === 'oval' || styleTheme === 'denim_y2k') ? 4 : 3;

                    ctx.beginPath();
                    if (photoShape === 'oval') {
                        ctx.ellipse(pasteX + photoW / 2, currentY + photoH / 2, photoW / 2, photoH / 2, 0, 0, Math.PI * 2);
                        ctx.stroke();
                    } else if (styleTheme === 'denim_y2k') {
                        if (typeof ctx.roundRect === 'function') {
                            ctx.roundRect(pasteX, currentY, photoW, photoH, 20);
                            ctx.stroke();
                        } else {
                            ctx.strokeRect(pasteX, currentY, photoW, photoH);
                        }
                    } else {
                        ctx.strokeRect(pasteX, currentY, photoW, photoH);
                    }

                    currentY += photoH + gap;
                }

                // Convert to high-resolution PNG data URL
                const resultUrl = canvas.toDataURL('image/png');
                finalStrip.src = resultUrl;
                downloadBtn.href = resultUrl;

                placeholder.classList.add('hidden');
                finalStrip.classList.remove('hidden');
                downloadBtn.classList.remove('hidden');

            } catch (err) {
                console.error("Gagal menjahit strip foto:", err);
                alert("Terjadi kesalahan saat memproses foto: " + err.message);
                placeholder.classList.remove('hidden');
                placeholder.innerHTML = `
                    <span class="text-4xl mb-2">⚠️</span>
                    <p class="font-hand text-lg text-red-700">Gagal memproses foto. Silakan coba lagi.</p>
                `;
            } finally {
                snapBtn.disabled = false;
                snapBtn.textContent = 'MULAI CETAK 3 FOTO';
            }
        }

        async function sendToBackend() {
            await generateStripClientSide();
        }

        function delay(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        async function handleManualPhotos(event) {
            const files = event.target.files;
            if (files.length !== 3) {
                alert("Harap pilih tepat 3 foto sekaligus!");
                return;
            }

            capturedImages = [];
            
            // Read files to data urls
            for (let i = 0; i < 3; i++) {
                const file = files[i];
                const dataUrl = await new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (e) => resolve(e.target.result);
                    reader.readAsDataURL(file);
                });
                
                capturedImages.push(dataUrl);

                // Update thumbnails
                const thumb = document.getElementById(`thumb-${i}`);
                const label = document.getElementById(`label-${i}`);
                thumb.src = dataUrl;
                thumb.classList.remove('hidden');
                label.classList.add('hidden');
            }

            // Trigger generation
            await sendToBackend();
        }

        async function handleSinglePhoto(idx, event) {
            const file = event.target.files[0];
            if (!file) return;
            
            const dataUrl = await new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (e) => resolve(e.target.result);
                reader.readAsDataURL(file);
            });
            
            capturedImages[idx] = dataUrl;
            
            // Update thumbnails
            const thumb = document.getElementById(`thumb-${idx}`);
            const label = document.getElementById(`label-${idx}`);
            thumb.src = dataUrl;
            thumb.classList.remove('hidden');
            label.classList.add('hidden');
            
            // Check if all 3 slots are filled
            let filledCount = 0;
            for (let i = 0; i < 3; i++) {
                if (capturedImages[i]) filledCount++;
            }
            
            if (filledCount === 3) {
                // All 3 filled! Trigger generation automatically!
                await sendToBackend();
            }
        }

        // Check secure context on load to clean UI
        document.addEventListener('DOMContentLoaded', () => {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                const webcamContainer = document.getElementById('webcam-container');
                const cameraPlaceholder = document.getElementById('camera-placeholder');
                const startBtn = document.getElementById('start-btn');
                const mobileGuide = document.getElementById('mobile-camera-guide');
                
                if (webcamContainer) webcamContainer.classList.add('hidden');
                if (cameraPlaceholder) cameraPlaceholder.classList.add('hidden');
                if (startBtn) startBtn.classList.add('hidden');
                if (mobileGuide) mobileGuide.classList.remove('hidden');
            }

            // Live update strip when custom options are changed after capture
            ['header_text', 'footer_text', 'style_theme', 'photo_shape'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', () => {
                        if (capturedImages.filter(Boolean).length === 3) {
                            generateStripClientSide();
                        }
                    });
                    el.addEventListener('change', () => {
                        if (capturedImages.filter(Boolean).length === 3) {
                            generateStripClientSide();
                        }
                    });
                }
            });
        });
    </script>
    </div>
</body>
</html>
