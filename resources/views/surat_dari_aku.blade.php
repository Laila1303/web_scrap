<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Surat Untukmu - Scrapbook Kayla</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="scrapbook-paper linen-texture min-h-screen text-espresso font-sans p-2 sm:p-4 md:p-8 flex flex-col justify-between relative overflow-x-hidden">
    
    <!-- User designed Mockup Corner Decorations (Pinggir web) -->
    <img src="{{ asset('images/mockup_top_left.png') }}" class="absolute top-0 left-0 w-20 sm:w-28 md:w-36 lg:w-52 opacity-95 pointer-events-none z-10 select-none" alt="PPG Group Left">
    <img src="{{ asset('images/mockup_top_right.png') }}" class="absolute top-0 right-0 w-24 sm:w-32 md:w-44 lg:w-64 opacity-95 pointer-events-none z-0 select-none" alt="Newspaper Collage Right">
    <img src="{{ asset('images/mockup_bottom_left.png') }}" class="absolute bottom-0 left-0 w-24 sm:w-36 md:w-48 lg:w-72 opacity-95 pointer-events-none z-0 select-none" alt="Newspaper Tulip Left">
    <img src="{{ asset('images/mockup_bottom_right.png') }}" class="absolute bottom-0 right-0 w-16 sm:w-20 md:w-28 lg:w-40 opacity-95 pointer-events-none z-10 select-none" alt="Blossom Guitar Right">
    
    <div class="max-w-4xl w-full mx-auto z-10 flex-1 flex flex-col gap-4 sm:gap-6">
        
        <!-- Header -->
        <header class="flex justify-between items-center border-b border-cocoa-light/20 pb-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="px-3 py-1 bg-espresso text-cream-light font-serif text-sm rounded shadow hover:bg-cocoa-medium transition">
                    &larr; KEMBALI
                </a>
                <h1 class="font-serif text-2xl md:text-3xl font-bold text-espresso-dark">💌 Love Letter</h1>
            </div>
            <span class="font-hand text-xl text-cocoa-medium">Pesan Spesial Hari Ini</span>
        </header>

        <!-- Main Envelope Area -->
        <div class="flex-1 flex flex-col items-center justify-center py-12 envelope-container">
            
            <div id="envelope-wrapper" class="envelope rounded-lg cursor-pointer flex flex-col justify-end p-6">
                
                <!-- Flap -->
                <div class="envelope-flap" id="flap"></div>

                <!-- Red-brown wax seal -->
                <div id="wax-seal" class="absolute left-1/2 top-[140px] transform -translate-x-1/2 w-14 h-14 rounded-full wax-seal flex items-center justify-center z-25 transition-transform duration-300 hover:scale-110">
                    <span class="text-cream-light text-xs font-serif font-bold">K</span>
                </div>

                <!-- Letter Content Inside -->
                <div class="letter-content rounded p-6 md:p-8 flex flex-col gap-4 relative" id="letter" onclick="event.stopPropagation()">
                    <!-- Top-Right Close Button inside fixed letter overlay -->
                    <button onclick="closeEnvelope()" class="absolute top-4 right-4 text-espresso-dark font-bold hover:text-cocoa-medium text-xl cursor-pointer pointer-events-auto" title="Tutup Surat">&times;</button>

                    <div class="border-b border-dashed border-cocoa-light pb-3">
                        <span class="font-hand text-lg text-cocoa-medium">Lhokseumawe, 6 Juli 2026</span>
                        <h2 class="font-serif text-2xl font-bold text-espresso-dark mt-1">Untuk Wibuu,</h2>
                    </div>

                    <div class="font-serif text-sm leading-relaxed text-espresso/90 flex flex-col gap-4">
                        <p>
                            Hai Wib, Happy Late Birthday... Udah Kepala 2 aja yaaa</p>
                        <p>
                            Hadiah tahun ni agak beda ya, aku buatin website khusus ultahmu, sekalian untuk nostalgia jugaa
                        </p>
                        <p>
                           Walaupun kita udah jarang ketemu sama komunikasi.. semoga mu selalu di lancarkan urusannya, jalani hobi dengan hati senang, i've nothing to say lagi sih.., intinya bahagia dan sehat selalu wibuuu yang terbaik pokoknya dan jangan lupain aku yaaa
                        </p>
                        <p class="font-hand text-right text-lg text-cocoa-medium mt-4">
                            Miss u Always,<br>
                            Langit &hearts;
                        </p>
                    </div>

                    <!-- Bottom Close Button inside fixed letter overlay -->
                    <div class="flex justify-center mt-4">
                        <button onclick="closeEnvelope()" class="px-4 py-2 bg-espresso text-cream-light font-serif text-xs rounded shadow hover:bg-cocoa-medium transition pointer-events-auto">
                            SEGEL KEMBALI SURAT
                        </button>
                    </div>
                </div>

                <!-- Envelope Address label -->
                <div class="absolute bottom-4 left-6 text-espresso/50 font-serif text-[10px] tracking-widest uppercase">
                    For : Wibu Late B'Day
                </div>

                <!-- Guide instructions -->
                <div id="guide-text" class="absolute bottom-4 right-6 text-espresso font-hand text-sm bg-tiramisu-light/35 px-2 py-0.5 rounded">
                    [ Klik cop surat untuk membuka ]
                </div>
            </div>

            <!-- Close button shown after open -->
            <button id="close-letter-btn" class="mt-8 px-4 py-2 bg-espresso text-cream-light font-serif text-sm rounded shadow hover:bg-cocoa-medium transition hidden">
                SEGEL KEMBALI SURAT
            </button>
        </div>

        <script>
            function openEnvelope() {
                const envelope = document.getElementById('envelope-wrapper');
                const waxSeal = document.getElementById('wax-seal');
                const guideText = document.getElementById('guide-text');
                const closeBtn = document.getElementById('close-letter-btn');

                if (envelope) envelope.classList.add('open');
                if (waxSeal) waxSeal.classList.add('hidden');
                if (guideText) guideText.classList.add('hidden');
                
                setTimeout(() => {
                    if (closeBtn) closeBtn.classList.remove('hidden');
                }, 600);
            }

            function closeEnvelope() {
                const envelope = document.getElementById('envelope-wrapper');
                const waxSeal = document.getElementById('wax-seal');
                const guideText = document.getElementById('guide-text');
                const closeBtn = document.getElementById('close-letter-btn');

                if (envelope) envelope.classList.remove('open');
                if (closeBtn) closeBtn.classList.add('hidden');
                
                setTimeout(() => {
                    if (waxSeal) waxSeal.classList.remove('hidden');
                    if (guideText) guideText.classList.remove('hidden');
                }, 500);
            }

            // PJAX-friendly direct event bindings
            function initEnvelopeEvents() {
                const envelope = document.getElementById('envelope-wrapper');
                const closeBtn = document.getElementById('close-letter-btn');
                
                if (envelope) {
                    envelope.removeEventListener('click', openEnvelope);
                    envelope.addEventListener('click', openEnvelope);
                }
                
                if (closeBtn) {
                    closeBtn.removeEventListener('click', handleCloseClick);
                    closeBtn.addEventListener('click', handleCloseClick);
                }
            }

            function handleCloseClick(e) {
                e.stopPropagation();
                closeEnvelope();
            }

            // Expose to window for global access
            window.openEnvelope = openEnvelope;
            window.closeEnvelope = closeEnvelope;

            // Initialize immediately and on DOM loads
            initEnvelopeEvents();
            document.addEventListener('DOMContentLoaded', initEnvelopeEvents);
        </script>
    </div>
</body>
</html>
