<script>
    function initPhotobooth() {
        const video = document.getElementById('webcam');
        const canvas = document.getElementById('capture-canvas');
        const ctx = canvas ? canvas.getContext('2d') : null;
        const startBtn = document.getElementById('start-camera-btn');
        const snapBtn = document.getElementById('snap-btn');
        const countdownOverlay = document.getElementById('countdown-overlay');
        const countdownNum = document.getElementById('countdown-number');
        const flashOverlay = document.getElementById('flash-overlay');
        const manualInput = document.getElementById('manual-photos-input');

        let streamTrack = null;
        let capturedImages = [null, null, null];

        function readFile(file) {
            return new Promise(function(resolve) {
                if (!file) return resolve(null);
                const reader = new FileReader();
                reader.onload = function(e) { resolve(e.target.result); };
                reader.onerror = function() { resolve(null); };
                reader.readAsDataURL(file);
            });
        }

        function loadImage(src) {
            return new Promise(function(resolve) {
                if (!src) return resolve(null);
                const img = new Image();
                img.crossOrigin = "anonymous";
                img.onload = function() { resolve(img); };
                img.onerror = function() { resolve(null); };
                img.src = src;
            });
        }

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
            manualInput.onchange = async function(e) {
                const files = Array.from(e.target.files || []);
                if (files.length === 0) return;

                for (let i = 0; i < Math.min(files.length, 3); i++) {
                    const dataUrl = await readFile(files[i]);
                    capturedImages[i] = dataUrl;
                    const thumb = document.getElementById('thumb-' + i);
                    const label = document.getElementById('label-' + i);
                    if (thumb) { thumb.src = dataUrl; thumb.classList.remove('hidden'); }
                    if (label) label.classList.add('hidden');
                }

                if (capturedImages.filter(Boolean).length === 3) {
                    await renderStrip();
                }
            };
        }

        // 2. Upload per Slot (1, 2, 3)
        [0, 1, 2].forEach(function(idx) {
            const slot = document.getElementById('slot-btn-' + idx);
            const input = document.getElementById('photo-input-' + idx);

            if (slot && input) {
                slot.onclick = function(e) {
                    e.preventDefault();
                    input.click();
                };
                input.onchange = async function(e) {
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
                };
            }
        });

        // 3. Aktifkan Kamera
        if (startBtn) {
            startBtn.onclick = async function(e) {
                e.preventDefault();
                try {
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        alert('Akses kamera tidak didukung di browser ini. Gunakan fitur upload foto di bawah.');
                        return;
                    }

                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } },
                        audio: false
                    });

                    streamTrack = stream;
                    video.srcObject = stream;
                    await video.play();

                    document.getElementById('camera-placeholder').classList.add('hidden');
                    document.getElementById('webcam-container').classList.remove('hidden');
                    startBtn.classList.add('hidden');
                    snapBtn.classList.remove('hidden');
                } catch (err) {
                    console.error("Camera error:", err);
                    alert('Tidak dapat mengaktifkan kamera (' + err.name + '). Pastikan izin kamera telah diberikan di browser.');
                }
            };
        }

        // 4. Capture Otomatis 3 Foto
        if (snapBtn) {
            snapBtn.onclick = async function(e) {
                e.preventDefault();
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
                        }, 700);
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

                    await new Promise(function(r) { setTimeout(r, 800); });
                }

                snapBtn.disabled = false;
                snapBtn.textContent = '📸 MULAI CETAK 3 FOTO OTOMATIS';
                await renderStrip();
            };
        }

        // 5. Generator Jahit Desain Strip Foto
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

                // TEMA 1: CLASSIC VINTAGE
                if (styleTheme === 'classic_vintage') {
                    cCtx.fillStyle = '#2B1B10';
                    cCtx.fillRect(0, 0, stripW, stripH);

                    const cardX1 = 50, cardY1 = 120, cardW = stripW - 100, cardH = stripH - 240;
                    const cardX2 = cardX1 + cardW, cardY2 = cardY1 + cardH;
                    drawRoundedRect(cCtx, cardX1, cardY1, cardW, cardH, 25, '#FDFCFA');

                    const midY = Math.round((cardY1 + cardY2) / 2);
                    cCtx.fillStyle = '#2B1B10';
                    cCtx.beginPath();
                    cCtx.arc(cardX1, midY, 16, 0, Math.PI * 2);
                    cCtx.fill();
                    cCtx.beginPath();
                    cCtx.arc(cardX2, midY, 16, 0, Math.PI * 2);
                    cCtx.fill();

                    drawRoundedRect(cCtx, stripW / 2 - 30, cardY1 - 20, 60, 30, 8, '#C0C0C0', '#5C4033', 2);
                    cCtx.fillStyle = '#808080';
                    cCtx.beginPath();
                    cCtx.arc(stripW / 2, cardY1 - 5, 6, 0, Math.PI * 2);
                    cCtx.fill();

                    drawBarcode(cCtx, cardX1 + 60, cardY2 - 80, cardW - 120, 40, '#5C4033');
                    cCtx.fillStyle = '#5C4033';
                    cCtx.font = '20px Georgia, serif';
                    cCtx.textAlign = 'center';
                    cCtx.textBaseline = 'middle';
                    cCtx.fillText(footerText, stripW / 2, cardY2 - 25);

                // TEMA 2: DENIM Y2K
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
                    
                    drawRoundedRect(cCtx, cardX1, cardY1, cardW, cardH, 25, '#FCF8EE', '#8B2D2D', 3);
                    drawRoundedRect(cCtx, cardX1 + 6, cardY1 + 6, cardW - 12, cardH - 12, 20, null, '#8B2D2D', 1);

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

                    drawBarcode(cCtx, cardX2 - 100, stripH - 145, 80, 65, '#8B2D2D');

                    cCtx.textAlign = 'left';
                    cCtx.fillStyle = '#8B2D2D';
                    cCtx.font = 'italic 30px Georgia, serif';
                    cCtx.fillText('HEY,', 80, stripH - 125);
                    cCtx.font = 'bold 24px Georgia, serif';
                    cCtx.fillText('GORGEOUS', 80, stripH - 95);
                    cCtx.font = '14px Georgia, serif';
                    cCtx.fillText('📍 ' + footerText, 80, stripH - 68);

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

                // TEMA 3: PPG COLLAGE
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

                // TEMA 4: POLAROID PRINTER
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

                // Header Banner
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
                        } else {
                            cCtx.rect(pasteX, currentY, photoW, photoH);
                        }
                        cCtx.stroke();
                    } else {
                        cCtx.strokeRect(pasteX, currentY, photoW, photoH);
                    }

                    currentY += photoH + gap;
                }

                // Stiker PPG
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

        ['header_text', 'footer_text', 'style_theme', 'photo_shape'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) {
                el.oninput = function() {
                    if (capturedImages.filter(Boolean).length === 3) renderStrip();
                };
                el.onchange = function() {
                    if (capturedImages.filter(Boolean).length === 3) renderStrip();
                };
            }
        });
    }

    // Langsung jalankan tanpa menunggu DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPhotobooth);
    } else {
        initPhotobooth();
    }
    </script>