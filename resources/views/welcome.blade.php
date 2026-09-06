<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scrapbook Digital Kayla</title>
    
    <!-- CSS / Fonts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="scrapbook-paper linen-texture min-h-screen text-espresso font-sans flex flex-col justify-between p-2 sm:p-4 md:p-8 relative overflow-x-hidden">
    
    <!-- Corner Decorations -->
    <img src="/images/mockup_top_left.png" class="absolute top-0 left-0 w-20 sm:w-28 md:w-36 lg:w-52 opacity-95 pointer-events-none z-10 select-none" alt="Top Left">
    <img src="/images/mockup_top_right.png" class="absolute top-0 right-0 w-24 sm:w-32 md:w-44 lg:w-64 opacity-95 pointer-events-none z-0 select-none" alt="Top Right">
    <img src="/images/mockup_bottom_left.png" class="absolute bottom-0 left-0 w-24 sm:w-36 md:w-48 lg:w-72 opacity-95 pointer-events-none z-0 select-none" alt="Bottom Left">
    <img src="/images/mockup_bottom_right.png" class="absolute bottom-0 right-0 w-16 sm:w-20 md:w-28 lg:w-40 opacity-95 pointer-events-none z-10 select-none" alt="Bottom Right">
    
    <!-- Main Wrapper -->
    <div class="max-w-6xl w-full mx-auto z-10 flex-1 flex flex-col justify-between gap-4 sm:gap-6 lg:gap-8">
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative font-serif text-sm z-50">
                <strong class="font-bold">Gagal memperbarui foto:</strong>
                <ul class="list-disc pl-5 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative font-serif text-sm z-50">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-amber-100 border border-amber-400 text-amber-800 px-4 py-3 rounded relative font-serif text-sm z-50">
                {{ session('error') }}
            </div>
        @endif
        
        <!-- Header Row -->
        <header class="flex flex-col md:flex-row justify-between items-center border-b-2 border-dashed border-cocoa-light/30 pb-4">
            <div>
                <h1 class="font-serif text-3xl md:text-5xl font-bold tracking-tight text-espresso-dark">KAY'S DASHBOARD</h1>
                <p class="font-hand text-xl md:text-2xl text-cocoa-medium">Birthday Memory Book &amp; Scrapbook</p>
            </div>
        </header>

        <!-- Main Scrapbook Panels -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 my-auto">
            
            <!-- Left Side: Digital Camera & Wishes -->
            <div class="lg:col-span-7 bg-[#F4EFE6] border-2 border-espresso p-6 rounded-xl shadow-lg relative flex flex-col md:flex-row gap-6 items-center">
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-cocoa-medium text-cream-light text-[10px] uppercase font-bold tracking-wider px-4 py-1 rounded shadow">
                    📎 MEMORY ARCHIVE
                </div>
                
                <!-- Digital Camera Decoration -->
                <form action="{{ route('upload-camera-photo') }}" method="POST" enctype="multipart/form-data" id="form-camera-photo" class="w-full md:w-2/5 flex flex-col items-center">
                    @csrf
                    <label for="camera_photo_input" class="w-full max-w-[180px] aspect-[4/3] rounded-lg overflow-hidden border-2 border-espresso bg-[#E5DCCB] relative group block cursor-pointer shadow-md transform hover:rotate-3 transition duration-300">
                        <img id="custom-camera-img" src="/images/custom_camera.png" class="w-full h-full object-cover select-none" alt="Custom Camera Photo" onerror="this.src='/images/vintage_camera.png'">
                        <input type="file" name="camera_photo" id="camera_photo_input" class="hidden" onchange="document.getElementById('form-camera-photo').submit();">
                    </label>

                    <div class="mt-2 text-center">
                        <span class="font-hand text-lg text-cocoa-medium">Kayla 20's</span>
                    </div>
                </form>

                <!-- Text & Blossom Ornament -->
                <div class="flex-1 flex flex-col justify-between h-full gap-4">
                    <div>
                        <h2 class="font-serif text-3xl font-extrabold text-espresso-dark">HAPPY BDAY, Kayla!</h2>
                        <h3 class="font-hand text-3xl text-cocoa-medium rotate-[-2deg]">On the road, 20! 🎂</h3>
                    </div>
                    
                    <p class="font-serif text-sm italic leading-relaxed text-espresso/80">
                        "Selamat memasuki babak kepala dua Wib!"
                    </p>

                    <div class="flex items-center gap-3 bg-cream-light/60 p-2.5 rounded-lg border border-dashed border-cocoa-light">
                        <div class="relative flex items-center justify-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                            <img src="/images/blossom_sticker.png" class="object-contain select-none" style="width: 48px; height: 48px;" alt="Blossom Icon">
                            <span class="absolute -top-2 -left-2 text-lg transform -rotate-12">🎉</span>
                        </div>
                        <div>
                            <p class="font-serif text-xs font-bold text-espresso">Blossom :</p>
                            <p class="font-hand text-xs text-cocoa-medium">"Happy Birthday Kay! &amp; Enjoy Your 20's!"</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Polaroid Trio (Salju Paling Depan, Langit Paling Belakang) -->
            <div class="lg:col-span-5 h-[340px] bg-[#F4EFE6] border-2 border-espresso p-6 rounded-xl shadow-lg relative flex items-center justify-center overflow-hidden">
                <div class="relative w-full h-full flex items-center justify-center">
                    
                    <!-- 1. Polaroid Langit (Paling Belakang - z-10) -->
                    <form action="{{ route('upload-polaroid', 3) }}" method="POST" enctype="multipart/form-data" id="form-polaroid-3" class="absolute z-10 transition-all duration-300 transform -translate-y-4 rotate-[-3deg] hover:rotate-0 hover:scale-110 hover:z-50">
                        @csrf
                        <label class="polaroid-photo block w-40 sm:w-44 bg-white p-2.5 pb-4 border border-gray-200 cursor-pointer shadow-md rounded-sm select-none">
                            <div class="w-full h-28 overflow-hidden relative">
                                <img src="/images/polaroid_3.png" class="w-full h-full object-cover filter sepia-[0.2]" alt="Langit" onerror="this.src='/images/friends_polaroid.png'">
                            </div>
                            <input type="file" name="polaroid_photo" class="hidden" onchange="document.getElementById('form-polaroid-3').submit();">
                            <p class="font-hand text-center text-espresso text-base mt-2">Langit 🌤️</p>
                        </label>
                    </form>

                    <!-- 2. Polaroid Awan (Tengah - z-20) -->
                    <form action="{{ route('upload-polaroid', 2) }}" method="POST" enctype="multipart/form-data" id="form-polaroid-2" class="absolute z-20 transition-all duration-300 transform translate-x-10 translate-y-2 rotate-12 hover:rotate-0 hover:scale-110 hover:z-50">
                        @csrf
                        <label class="polaroid-photo block w-40 sm:w-44 bg-white p-2.5 pb-4 border border-gray-200 cursor-pointer shadow-lg rounded-sm select-none">
                            <div class="w-full h-28 overflow-hidden relative">
                                <img src="/images/polaroid_2.png" class="w-full h-full object-cover filter sepia-[0.1]" alt="Awan" onerror="this.src='/images/friends_polaroid.png'">
                            </div>
                            <input type="file" name="polaroid_photo" class="hidden" onchange="document.getElementById('form-polaroid-2').submit();">
                            <p class="font-hand text-center text-espresso text-base mt-2">Awan ☁️</p>
                        </label>
                    </form>

                    <!-- 3. Polaroid Salju (Paling Depan - z-30) -->
                    <form action="{{ route('upload-polaroid', 1) }}" method="POST" enctype="multipart/form-data" id="form-polaroid-1" class="absolute z-30 transition-all duration-300 transform -translate-x-10 translate-y-3 -rotate-12 hover:rotate-0 hover:scale-110 hover:z-50">
                        @csrf
                        <label class="polaroid-photo block w-40 sm:w-44 bg-white p-2.5 pb-4 border border-gray-200 cursor-pointer shadow-xl rounded-sm select-none">
                            <div class="w-full h-28 overflow-hidden relative">
                                <img src="/images/polaroid_1.png" class="w-full h-full object-cover filter sepia-[0.3]" alt="Salju" onerror="this.src='/images/friends_polaroid.png'">
                            </div>
                            <input type="file" name="polaroid_photo" class="hidden" onchange="document.getElementById('form-polaroid-1').submit();">
                            <p class="font-hand text-center text-espresso text-base mt-2">Salju ❄️</p>
                        </label>
                    </form>

                </div>

                <div class="absolute bottom-2 right-3 font-hand text-xs text-cocoa-medium">directed by: &bull; Langit</div>
            </div>

            <!-- Row 2: Playlist & Audio Player -->
            <div class="lg:col-span-12 bg-espresso text-cream-light p-6 rounded-xl shadow-lg border-2 border-espresso flex flex-col md:flex-row items-center gap-6 justify-between relative overflow-hidden">
                <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-tiramisu-dark/10 rounded-full blur-2xl"></div>
                
                <div class="flex items-center gap-4 z-10 w-full md:w-auto">
                    <!-- Album Cover -->
                    <form action="{{ route('upload-kayla') }}" method="POST" enctype="multipart/form-data" id="form-kayla-photo" class="shrink-0">
                        @csrf
                        <label class="w-20 h-20 rounded-lg overflow-hidden border border-tiramisu-light/30 shrink-0 relative group block cursor-pointer">
                            <img id="player-album-art" src="/images/kayla.jpg" class="w-full h-full object-cover" alt="Kayla" onerror="this.src='/images/custom_camera.png'">
                            <input type="file" name="kayla_photo" class="hidden" onchange="document.getElementById('form-kayla-photo').submit();">
                        </label>
                    </form>
                    
                    <!-- Track Details -->
                    <div class="min-w-[180px] sm:min-w-[240px] md:min-w-[280px] max-w-[320px] flex flex-col justify-center overflow-hidden">
                        <span class="bg-tiramisu-light/20 text-tiramisu-light text-[9px] font-bold tracking-widest px-2 py-0.5 rounded-full uppercase w-max">KAYLA'S PLAYLIST</span>
                        <h4 id="player-track-title" class="font-serif text-xl font-bold mt-1 text-tiramisu-light truncate">Happy Birthday, Kayla</h4>
                        <p id="player-artist" class="font-hand text-sm text-tiramisu-light/70 truncate">From Langit &hearts;</p>
                    </div>
                </div>

                <!-- Controls -->
                <div class="flex-1 flex flex-col gap-2 w-full max-w-lg z-10">
                    <div class="flex items-center justify-center gap-6">
                        <button onclick="prevTrack()" class="text-tiramisu-light hover:text-cream-light transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6 8.5 6V6z"/></svg>
                        </button>
                        <button id="play-btn" onclick="togglePlay()" class="w-10 h-10 rounded-full bg-tiramisu-light text-espresso flex items-center justify-center hover:scale-105 transition-transform">
                            <svg id="play-icon" class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            <svg id="pause-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                        </button>
                        <button onclick="nextTrack()" class="text-tiramisu-light hover:text-cream-light transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 18l8.5-6L6 6zm9-12v12h2V6z"/></svg>
                        </button>
                    </div>
                    
                    <div class="flex items-center gap-3 text-xs text-tiramisu-light/70">
                        <span id="current-time">0:00</span>
                        <input id="progress-bar" type="range" min="0" max="100" value="0" class="flex-1 accent-tiramisu-light h-1 bg-tiramisu-light/20 rounded-lg cursor-pointer" oninput="seekAudio(this.value)">
                        <span id="total-time">3:20</span>
                    </div>
                </div>

                <!-- Visualizer -->
                <div class="hidden md:flex items-center gap-1 h-12 w-24 z-10">
                    <div class="w-1.5 h-4 bg-tiramisu-light/60 rounded-full bar-anim"></div>
                    <div class="w-1.5 h-10 bg-tiramisu-light rounded-full bar-anim"></div>
                    <div class="w-1.5 h-6 bg-tiramisu-light/80 rounded-full bar-anim"></div>
                    <div class="w-1.5 h-8 bg-tiramisu-light rounded-full bar-anim"></div>
                    <div class="w-1.5 h-3 bg-tiramisu-light/40 rounded-full bar-anim"></div>
                </div>
            </div>

        </div>

        <!-- Bottom Navigation Area -->
        <footer class="flex flex-col items-center mt-4">
            <div class="flex items-center gap-2 mb-4 animate-bounce">
                <span class="font-serif font-bold text-espresso text-lg tracking-wider">JELAJAHI DI SINI</span>
                <svg class="w-8 h-8 text-cocoa-medium transform rotate-[15deg]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full">
                <a href="{{ route('photobooth') }}" class="paper-patch bg-[#FAF0E6] border-2 border-espresso p-4 text-center rounded flex flex-col items-center justify-center gap-1 hover:bg-tiramisu-light/30">
                    <span class="text-2xl">📸</span>
                    <span class="font-serif text-[11px] font-extrabold uppercase tracking-wide">DIY Photobooth</span>
                    <span class="font-hand text-xs text-cocoa-medium">3 Strip Foto Lucu</span>
                </a>
                
                <a href="{{ route('gallery') }}" class="paper-patch bg-[#FDF5E6] border-2 border-espresso p-4 text-center rounded flex flex-col items-center justify-center gap-1 hover:bg-tiramisu-light/30">
                    <span class="text-2xl">🎞️</span>
                    <span class="font-serif text-[11px] font-extrabold uppercase tracking-wide">Friendship Galery</span>
                    <span class="font-hand text-xs text-cocoa-medium">Open Our Galery</span>
                </a>

                <a href="{{ route('surat-dari-aku') }}" class="paper-patch bg-[#FAEBD7] border-2 border-espresso p-4 text-center rounded flex flex-col items-center justify-center gap-1 hover:bg-tiramisu-light/30">
                    <span class="text-2xl">💌</span>
                    <span class="font-serif text-[11px] font-extrabold uppercase tracking-wide">A Letter for Wibu</span>
                    <span class="font-hand text-xs text-cocoa-medium">Opening Letter</span>
                </a>

                <a href="{{ route('kapsul-waktu') }}" class="paper-patch bg-[#F5F5DC] border-2 border-espresso p-4 text-center rounded flex flex-col items-center justify-center gap-1 hover:bg-tiramisu-light/30">
                    <span class="text-2xl">🔒</span>
                    <span class="font-serif text-[11px] font-extrabold uppercase tracking-wide">Kapsul Waktu</span>
                    <span class="font-hand text-xs text-cocoa-medium">Pesan untuk Masa Depan</span>
                </a>
            </div>
        </footer>

    </div>

    <!-- Music Player JS -->
    <script>
    (function() {
        if (!window.bgAudio) {
            window.bgAudio = document.createElement('audio');
            window.bgAudio.id = 'bg-audio';
            document.body.appendChild(window.bgAudio);
        }
        const audio = window.bgAudio;
        const playBtn = document.getElementById('play-btn');
        const playIcon = document.getElementById('play-icon');
        const pauseIcon = document.getElementById('pause-icon');
        const progressBar = document.getElementById('progress-bar');
        const currentTimeEl = document.getElementById('current-time');
        const totalTimeEl = document.getElementById('total-time');
        const trackTitle = document.getElementById('player-track-title');
        const artistEl = document.getElementById('player-artist');
        
        let tracks = @json($customTracks ?? []);
        if (!tracks || tracks.length === 0) {
            tracks = [
                {
                    title: "Happy Birthday, Kayla 🎂",
                    artist: "From Langit &hearts;",
                    url: "https://assets.codepen.io/4358584/Anitek_-_01_-_Kisses.mp3"
                },
                {
                    title: "Nostalgic Afternoon 🍂",
                    artist: "Lofi Friendship Vibe",
                    url: "https://assets.codepen.io/4358584/Anitek_-_02_-_Kisses_Instrumental.mp3"
                },
                {
                    title: "Linen Pages & pressed Flowers 🌸",
                    artist: "Scrapbook Melancholy",
                    url: "https://assets.codepen.io/4358584/Anitek_-_03_-_Our_Lounge.mp3"
                }
            ];
        }
        
        localStorage.setItem('playlist_tracks', JSON.stringify(tracks));
        
        let currentTrackIdx = parseInt(localStorage.getItem('current_track_idx') || '0');
        if (currentTrackIdx >= tracks.length) {
            currentTrackIdx = 0;
        }
        
        function loadTrack(idx) {
            const track = tracks[idx];
            if (!track) return;

            let audioPath = '';
            try {
                if (audio.src) {
                    audioPath = decodeURIComponent(new URL(audio.src).pathname);
                }
            } catch(e) {}
            
            let trackPath = '';
            try {
                trackPath = decodeURIComponent(new URL(track.url, window.location.origin).pathname);
            } catch(e) {}

            if (!audio.src || audioPath !== trackPath) {
                audio.src = track.url;
                audio.load();
            }
            
            if (trackTitle) trackTitle.textContent = track.title;
            if (artistEl) artistEl.innerHTML = track.artist;
            localStorage.setItem('current_track_idx', idx);
        }
        
        loadTrack(currentTrackIdx);
        
        if (audio && !audio.paused) {
            if (playIcon) playIcon.classList.add('hidden');
            if (pauseIcon) pauseIcon.classList.remove('hidden');
            document.querySelectorAll('.bar-anim').forEach(bar => bar.classList.add('animate-pulse'));
        } else {
            if (playIcon) playIcon.classList.remove('hidden');
            if (pauseIcon) pauseIcon.classList.add('hidden');
            document.querySelectorAll('.bar-anim').forEach(bar => bar.classList.remove('animate-pulse'));
        }
        
        function togglePlay() {
            if (audio.paused) {
                audio.play().then(() => {
                    if (playIcon) playIcon.classList.add('hidden');
                    if (pauseIcon) pauseIcon.classList.remove('hidden');
                    document.querySelectorAll('.bar-anim').forEach(bar => bar.classList.add('animate-pulse'));
                }).catch(err => {
                    console.warn("Autoplay blocked: ", err);
                    alert("Ketuk layar web terlebih dahulu lalu tekan Play untuk memulai musik.");
                });
            } else {
                audio.pause();
                if (playIcon) playIcon.classList.remove('hidden');
                if (pauseIcon) pauseIcon.classList.add('hidden');
                document.querySelectorAll('.bar-anim').forEach(bar => bar.classList.remove('animate-pulse'));
            }
        }
        
        function nextTrack() {
            currentTrackIdx = (currentTrackIdx + 1) % tracks.length;
            loadTrack(currentTrackIdx);
            audio.play().then(() => {
                if (playIcon) playIcon.classList.add('hidden');
                if (pauseIcon) pauseIcon.classList.remove('hidden');
                document.querySelectorAll('.bar-anim').forEach(bar => bar.classList.add('animate-pulse'));
            }).catch(err => console.warn(err));
        }
        
        function prevTrack() {
            currentTrackIdx = (currentTrackIdx - 1 + tracks.length) % tracks.length;
            loadTrack(currentTrackIdx);
            audio.play().then(() => {
                if (playIcon) playIcon.classList.add('hidden');
                if (pauseIcon) pauseIcon.classList.remove('hidden');
                document.querySelectorAll('.bar-anim').forEach(bar => bar.classList.add('animate-pulse'));
            }).catch(err => console.warn(err));
        }
        
        audio.ontimeupdate = () => {
            if (audio.duration) {
                const percentage = (audio.currentTime / audio.duration) * 100;
                if (progressBar) progressBar.value = percentage;
                
                const curMins = Math.floor(audio.currentTime / 60);
                const curSecs = Math.floor(audio.currentTime % 60).toString().padStart(2, '0');
                const durMins = Math.floor(audio.duration / 60);
                const durSecs = Math.floor(audio.duration % 60).toString().padStart(2, '0');
                
                if (currentTimeEl) currentTimeEl.textContent = `${curMins}:${curSecs}`;
                if (totalTimeEl) totalTimeEl.textContent = `${durMins}:${durSecs}`;
            }
        };
        
        audio.onended = () => {
            nextTrack();
        };
        
        function seekAudio(value) {
            if (audio.duration) {
                audio.currentTime = (value / 100) * audio.duration;
            }
        }

        window.togglePlay = togglePlay;
        window.nextTrack = nextTrack;
        window.prevTrack = prevTrack;
        window.seekAudio = seekAudio;
    })();
    </script>
</body>
</html>