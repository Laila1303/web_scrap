<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kapsul Waktu - Scrapbook Kayla</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="scrapbook-paper linen-texture min-h-screen text-espresso font-sans p-2 sm:p-4 md:p-8 flex flex-col justify-between relative">
    
    <!-- User designed Mockup Corner Decorations (Pinggir web) -->
    <img src="{{ asset('images/mockup_top_left.png') }}" class="absolute top-0 left-0 w-20 sm:w-28 md:w-36 lg:w-52 opacity-95 pointer-events-none z-10 select-none" alt="PPG Group Left">
    <img src="{{ asset('images/mockup_top_right.png') }}" class="absolute top-0 right-0 w-24 sm:w-32 md:w-44 lg:w-64 opacity-95 pointer-events-none z-0 select-none" alt="Newspaper Collage Right">
    <img src="{{ asset('images/mockup_bottom_left.png') }}" class="absolute bottom-0 left-0 w-24 sm:w-36 md:w-48 lg:w-72 opacity-95 pointer-events-none z-0 select-none" alt="Newspaper Tulip Left">
    <img src="{{ asset('images/mockup_bottom_right.png') }}" class="absolute bottom-0 right-0 w-16 sm:w-20 md:w-28 lg:w-40 opacity-95 pointer-events-none z-10 select-none" alt="Blossom Guitar Right">
    
    <div class="max-w-6xl w-full mx-auto z-10 flex-1 flex flex-col gap-4 sm:gap-6">
        
        <!-- Header -->
        <header class="flex justify-between items-center border-b border-cocoa-light/20 pb-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="px-3 py-1 bg-espresso text-cream-light font-serif text-sm rounded shadow hover:bg-cocoa-medium transition">
                    &larr; KEMBALI
                </a>
                <h1 class="font-serif text-2xl md:text-3xl font-bold text-espresso-dark">🔒 KAPSUL WAKTU &amp; HARAPAN</h1>
            </div>
            <span class="font-hand text-xl text-cocoa-medium">Pesan Masa Depan Kayla</span>
        </header>

        <!-- Notification -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative font-serif text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 my-auto items-start">
            
            <!-- Left Column: Input Form (5 Cols) -->
            <div class="lg:col-span-5 bg-[#F4EFE6] border-2 border-espresso p-6 rounded-xl shadow-md flex flex-col gap-4 relative">
                <!-- Decorative Tape -->
                <div class="absolute -top-3 left-1/3 w-28 h-6 paper-tape transform rotate-[-2deg] opacity-75"></div>
                
                <h2 class="font-serif text-xl font-bold text-espresso-dark mt-2">[ ✍️ TULIS SURAT MASA DEPAN ]</h2>
                
                <form action="{{ route('kapsul-waktu.store') }}" method="POST" class="flex flex-col gap-4">
                    @csrf
                    
                    <!-- Sender -->
                    <div class="flex flex-col gap-1">
                        <label class="font-serif text-xs font-bold text-espresso">Nama Pengirim:</label>
                        <input type="text" name="sender" placeholder="" class="text-sm bg-cream-light p-2.5 rounded border border-cocoa-light focus:outline-none">
                    </div>

                    <!-- Type Selection -->
                    <div class="flex flex-col gap-1">
                        <label class="font-serif text-xs font-bold text-espresso">Jenis Kiriman:</label>
                        <select name="type" id="type-select" onchange="toggleUnlockOptions(this.value)" class="text-sm bg-cream-light p-2.5 rounded border border-cocoa-light focus:outline-none">
                            <option value="time_capsule" @selected(old('type') === 'time_capsule')>🔒 Kapsul Waktu (Terkunci sampai tanggal tertentu)</option>
                            <option value="letter" @selected(old('type') === 'letter')>🔓 Surat Biasa (Langsung bisa dibuka Kayla hari ini)</option>
                        </select>
                    </div>

                    <!-- Unlock Date Options (Relative/Custom) -->
                    <div id="unlock-options-container" class="flex flex-col gap-3">
                        <div class="flex flex-col gap-1">
                            <label class="font-serif text-xs font-bold text-espresso">Jadwal Pembukaan Kapsul:</label>
                            <select name="unlock_relative" id="relative-select" onchange="toggleCustomDate(this.value)" class="text-sm bg-cream-light p-2.5 rounded border border-cocoa-light focus:outline-none">
                                <option value="1_year" @selected(old('unlock_relative') === '1_year' || !old('unlock_relative'))>1 Tahun Lagi (2027)</option>
                                <option value="2_years" @selected(old('unlock_relative') === '2_years')>2 Tahun Lagi (2028)</option>
                                <option value="5_years" @selected(old('unlock_relative') === '5_years')>5 Tahun Lagi (2031)</option>
                                <option value="custom" @selected(old('unlock_relative') === 'custom')>Pilih Tanggal Kustom...</option>
                            </select>
                        </div>
                        
                        <!-- Custom Date picker -->
                        <div id="custom-date-container" class="hidden flex flex-col gap-1">
                            <label class="font-serif text-xs font-bold text-espresso">Pilih Tanggal:</label>
                            <input type="date" name="unlock_custom" min="{{ date('Y-m-d') }}" value="{{ old('unlock_custom') }}" class="text-sm bg-cream-light p-2.5 rounded border border-cocoa-light focus:outline-none">
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex flex-col gap-1">
                        <label class="font-serif text-xs font-bold text-espresso">Isi Surat / Harapan:</label>
                        <textarea name="content" rows="6" placeholder="Tuliskan harapanmu, kenangan, atau pesan khusus untuk dibaca Kayla di masa depan..." class="text-sm bg-cream-light p-2.5 rounded border border-cocoa-light font-serif focus:outline-none" required></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-espresso text-cream-light font-serif font-bold text-sm rounded shadow hover:bg-cocoa-medium transition mt-2">
                        SIMPAN DALAM KAPSUL WAKTU
                    </button>
                </form>
            </div>

            <!-- Right Column: Time Capsule Display List (7 Cols) -->
            <div class="lg:col-span-7 flex flex-col gap-4">
                <h3 class="font-serif text-lg font-bold text-espresso-dark">Koleksi Kapsul Waktu Kayla</h3>

                <div class="flex flex-col gap-4 max-h-[560px] overflow-y-auto pr-2">
                    @forelse($capsules as $capsule)
                        @php
                            $isUnlocked = $capsule->isUnlocked();
                        @endphp
                        
                        <div class="capsule-card bg-[#FDFDFC] p-5 rounded-lg shadow-sm flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
                            
                            <!-- Icon and Status -->
                            <div class="flex items-center gap-4">
                                <div class="text-3xl shrink-0">
                                    {{ $isUnlocked ? '🔓' : '🔒' }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-serif text-xs font-bold text-cocoa-medium uppercase">{{ $capsule->type === 'letter' ? 'Surat Biasa' : 'Kapsul Waktu' }}</span>
                                        <span class="text-[10px] text-espresso/40">&bull; {{ $capsule->created_at->format('d M Y') }}</span>
                                    </div>
                                    <h4 class="font-serif text-base font-bold text-espresso-dark mt-0.5">Dari: {{ $capsule->sender }}</h4>
                                    
                                    @if(!$isUnlocked)
                                        <!-- Countdown Text Container -->
                                        <p class="font-hand text-xs text-red-700/80 mt-1 countdown-timer" data-target="{{ $capsule->unlock_at->toISOString() }}">
                                            Terkunci. Membuka pada {{ $capsule->unlock_at->format('d M Y H:i') }}
                                        </p>
                                    @else
                                        <p class="font-hand text-xs text-green-700/80 mt-1">Sudah bisa dibuka!</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Action -->
                            <div>
                                @if($isUnlocked)
                                    <button onclick="readCapsule('{{ $capsule->sender }}', '{{ addslashes(str_replace("\n", "<br>", $capsule->content)) }}', '{{ $capsule->created_at->format('d M Y') }}')" class="px-4 py-1.5 bg-espresso text-cream-light font-serif text-xs rounded hover:bg-cocoa-medium transition">
                                        BACA SEKARANG &rarr;
                                    </button>
                                @else
                                    <button class="px-4 py-1.5 bg-cocoa-light/30 text-espresso/50 font-serif text-xs rounded cursor-not-allowed" disabled>
                                        TERKUNCI 🔒
                                    </button>
                                @endif
                            </div>

                        </div>
                    @empty
                        <div class="bg-[#F4EFE6] p-8 text-center rounded-lg border-2 border-dashed border-cocoa-light">
                            <span class="text-3xl">📭</span>
                            <p class="font-hand text-lg text-cocoa-medium mt-2">Belum ada kapsul waktu atau surat yang disimpan.</p>
                        </div>
                    @endforelse
                </div>

            </div>

        </div>
    </div>

    <!-- Read Modal -->
    <div id="read-modal" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-cream-light border-2 border-espresso max-w-lg w-full rounded-xl shadow-2xl p-6 relative flex flex-col gap-4">
            <!-- Close Button -->
            <button onclick="closeModal()" class="absolute top-4 right-4 text-espresso-dark font-bold hover:text-cocoa-medium text-lg">&times;</button>
            
            <div class="border-b border-dashed border-cocoa-light pb-2">
                <span id="modal-date" class="font-hand text-sm text-cocoa-medium">6 Juli 2026</span>
                <h3 id="modal-sender" class="font-serif text-xl font-bold text-espresso-dark">Dari: Sahabat</h3>
            </div>
            
            <p id="modal-content" class="font-serif text-sm leading-relaxed text-espresso/90 max-h-[300px] overflow-y-auto py-2">
                Isi surat...
            </p>
            
            <button onclick="closeModal()" class="w-full py-2 bg-espresso text-cream-light font-serif font-bold text-xs rounded shadow hover:bg-cocoa-medium transition mt-2">
                TUTUP SURAT
            </button>
        </div>
    </div>

    <script>
        function toggleUnlockOptions(type) {
            const container = document.getElementById('unlock-options-container');
            if (type === 'letter') {
                container.classList.add('hidden');
            } else {
                container.classList.remove('hidden');
            }
        }

        function toggleCustomDate(value) {
            const container = document.getElementById('custom-date-container');
            const input = container.querySelector('input[type="date"]');
            if (value === 'custom') {
                container.classList.remove('hidden');
                if (input) input.required = true;
            } else {
                container.classList.add('hidden');
                if (input) {
                    input.required = false;
                    input.value = '';
                }
            }
        }

        // Initialize state on page load/PJAX transition
        function syncFormFields() {
            const typeSelect = document.getElementById('type-select');
            if (typeSelect) toggleUnlockOptions(typeSelect.value);
            
            const relativeSelect = document.getElementById('relative-select');
            if (relativeSelect) toggleCustomDate(relativeSelect.value);
        }

        document.addEventListener('DOMContentLoaded', syncFormFields);
        syncFormFields(); // Trigger for dynamic/PJAX transitions

        function readCapsule(sender, content, date) {
            document.getElementById('modal-sender').textContent = 'Dari: ' + sender;
            document.getElementById('modal-content').innerHTML = content;
            document.getElementById('modal-date').textContent = 'Dikirim pada: ' + date;
            document.getElementById('read-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('read-modal').classList.add('hidden');
        }

        // Live Countdown Script
        function updateCountdowns() {
            const now = new Date().getTime();
            const timers = document.querySelectorAll('.countdown-timer');
            
            timers.forEach(timer => {
                const targetDate = new Date(timer.getAttribute('data-target')).getTime();
                const diff = targetDate - now;
                
                if (diff <= 0) {
                    timer.innerHTML = "<span class='text-green-700 font-bold'>Sudah bisa dibuka! Muat ulang halaman.</span>";
                } else {
                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                    
                    timer.innerHTML = `Terkunci. Buka dalam: <span class="font-mono font-bold">${days}h ${hours}h ${minutes}m ${seconds}s</span>`;
                }
            });
        }
        
        setInterval(updateCountdowns, 1000);
        updateCountdowns();
    </script>
</body>
</html>
