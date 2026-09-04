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

        async function sendToBackend() {
            const placeholder = document.getElementById('strip-placeholder');
            const finalStrip = document.getElementById('final-strip');
            const downloadBtn = document.getElementById('download-btn');
            
            placeholder.innerHTML = `
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-espresso mb-3"></div>
                <p class="font-hand text-lg text-cocoa-medium">Sedang menjahit foto strip kustom kamu...</p>
            `;

            const headerText = document.getElementById('header_text').value;
            const footerText = document.getElementById('footer_text').value;
            const styleTheme = document.getElementById('style_theme').value;
            const photoShape = document.getElementById('photo_shape').value;

            try {
                const response = await fetch("{{ route('photobooth.generate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        image1: capturedImages[0],
                        image2: capturedImages[1],
                        image3: capturedImages[2],
                        header_text: headerText,
                        footer_text: footerText,
                        template_color: styleTheme,
                        photo_shape: photoShape
                    })
                });

                const result = await response.json();
                if (result.success) {
                    placeholder.classList.add('hidden');
                    finalStrip.src = result.url;
                    finalStrip.classList.remove('hidden');
                    downloadBtn.href = result.url;
                    downloadBtn.classList.remove('hidden');
                } else {
                    alert('Gagal membuat strip: ' + result.message);
                }
            } catch (err) {
                alert('Error server: ' + err.message);
            } finally {
                snapBtn.disabled = false;
                snapBtn.textContent = 'MULAI CETAK 3 FOTO';
            }
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
        });
    </script>
    </div>
</body>
</html>
