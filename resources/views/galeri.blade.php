<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Our Galery - Scrapbook Kayla</title>
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
                <h1 class="font-serif text-2xl md:text-3xl font-bold text-espresso-dark">🎞️ GALERI MASA MUDA</h1>
            </div>
            <span class="font-hand text-xl text-cocoa-medium">Memori SMA &amp; Kebersamaan</span>
        </header>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative font-serif text-sm z-50">
                <strong class="font-bold">Gagal menyimpan foto memori:</strong>
                <ul class="list-disc pl-5 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <p class="text-xs mt-2 text-red-600">Catatan: Jika ukuran file terlalu besar (lebih dari 2MB), pastikan untuk menyesuaikan pengaturan 'upload_max_filesize' di PHP.ini XAMPP Anda.</p>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative font-serif text-sm z-50">
                {{ session('success') }}
            </div>
        @endif

        <!-- Main Workspace -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 my-auto items-start">
            
            <!-- Left Side: Upload Memory Form (4 Cols) -->
            <div class="lg:col-span-4 bg-[#F4EFE6] border-2 border-espresso p-6 rounded-xl shadow-md flex flex-col gap-4 relative">
                <!-- Tape design element -->
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-tiramisu-dark/60 w-24 h-6 rotate-[-1deg] border border-espresso opacity-70"></div>
                
                <h2 class="font-serif text-lg font-bold text-espresso-dark mt-2">[ 📝 UNGGAH MEMORI BARU ]</h2>
                <p class="font-serif text-xs text-espresso/70 leading-relaxed">Tambahkan kenangan terbaru</p>
                
                <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4 mt-2">
                    @csrf
                    <!-- Image Input -->
                    <div class="flex flex-col gap-1">
                        <label class="font-serif text-xs font-bold text-espresso">Pilih Foto:</label>
                        <input type="file" name="image" required class="text-xs bg-cream-light p-2 rounded border border-cocoa-light focus:outline-none">
                    </div>
                    
                    <!-- Caption Input -->
                    <div class="flex flex-col gap-1">
                        <label class="font-serif text-xs font-bold text-espresso">Catatan Tulisan Tangan:</label>
                        <input type="text" name="caption" placeholder="Contoh: 1st meet after collage 🌧️" class="text-sm bg-cream-light p-2.5 rounded border border-cocoa-light font-hand focus:outline-none" required>
                    </div>

                    <!-- Size Input -->
                    <div class="flex flex-col gap-1">
                        <label class="font-serif text-xs font-bold text-espresso">Ukuran Foto:</label>
                        <select name="size" class="text-sm bg-cream-light p-2.5 rounded border border-cocoa-light focus:outline-none">
                            <option value="small">Kecil (170px)</option>
                            <option value="medium" selected>Sedang (240px)</option>
                            <option value="large">Besar (290px)</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2 bg-espresso text-cream-light font-serif font-bold text-sm rounded shadow hover:bg-cocoa-medium transition mt-2">
                        SIMPAN DI SCRAPBOOK
                    </button>
                </form>
            </div>

            <!-- Right Side: Polaroid Flex Collage (8 Cols) -->
            <div class="lg:col-span-8">
                
                <!-- Polaroid Collage Container -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 justify-center justify-items-center">
                    
                    <!-- If database is empty, show default nostalgic polaroids -->
                    @if($photos->isEmpty())
                        <!-- Default Polaroid 1 -->
                        <div class="polaroid-card bg-white p-3 pb-8 border border-gray-200 rounded-sm transform rotate-[-3deg] w-44 sm:w-52">
                            <img src="{{ asset('images/friends_polaroid.png') }}" class="w-full h-40 object-cover filter sepia-[0.3]" alt="">
                            <p class="font-hand text-center text-espresso text-lg mt-3 rotate-[1deg]">Candid Kantin SMA 🍜</p>
                        </div>
                        
                        <!-- Default Polaroid 2 -->
                        <div class="polaroid-card bg-white p-3 pb-8 border border-gray-200 rounded-sm transform rotate-[4deg] w-44 sm:w-52">
                            <img src="{{ asset('images/friends_polaroid.png') }}" class="w-full h-40 object-cover filter sepia-[0.1]" alt="">
                            <p class="font-hand text-center text-espresso text-lg mt-3">Upacara Senin Pagi 🏫</p>
                        </div>
                        
                        <!-- Default Polaroid 3 -->
                        <div class="polaroid-card bg-white p-3 pb-8 border border-gray-200 rounded-sm transform rotate-[-2deg] w-44 sm:w-52">
                            <img src="{{ asset('images/friends_polaroid.png') }}" class="w-full h-40 object-cover filter sepia-[0.5]" alt="">
                            <p class="font-hand text-center text-espresso text-lg mt-3">Kelulusan SMA tercinta 🎉</p>
                        </div>
                    @else
                        <!-- Database Photos -->
                        @foreach($photos as $index => $photo)
                            @php
                                // Assign alternating rotations for a realistic scrapbook layout
                                $rotations = ['rotate-[-3deg]', 'rotate-[4deg]', 'rotate-[-2deg]', 'rotate-[3deg]', 'rotate-[-4deg]'];
                                $rotation = $rotations[$index % count($rotations)];
                                
                                // Size classes optimized for 3-column grid
                                $sizeClasses = [
                                    'small' => 'w-36 sm:w-40',
                                    'medium' => 'w-40 sm:w-48',
                                    'large' => 'w-44 sm:w-56'
                                ];
                                $sizeClass = $sizeClasses[$photo->size] ?? 'w-40 sm:w-48';
                            @endphp
                            <div class="polaroid-card relative bg-white p-3 pb-6 border border-gray-200 rounded-sm {{ $rotation }} {{ $sizeClass }} group">
                                <!-- Delete Button (Visible on Hover) -->
                                <form action="{{ route('gallery.destroy', $photo->id) }}" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-30">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shadow" onclick="return confirm('Hapus foto memori ini?')">
                                        &times;
                                    </button>
                                </form>

                                <img src="{{ asset($photo->image_path) }}" class="w-full h-40 object-cover" alt="Memory">
                                <p class="font-hand text-center text-espresso text-lg mt-3">{{ $photo->caption }}</p>
                            </div>
                        @endforeach
                    @endif

                </div>
            </div>

        </div>
    </div>
</body>
</html>
