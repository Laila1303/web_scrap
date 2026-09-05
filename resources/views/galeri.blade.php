<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Our Gallery - Scrapbook Kayla</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="scrapbook-paper linen-texture min-h-screen text-espresso font-sans p-2 sm:p-4 md:p-8 flex flex-col justify-between relative">
    
    <!-- Corner Decorations -->
    <img src="{{ asset('images/mockup_top_left.png') }}" class="absolute top-0 left-0 w-20 sm:w-28 md:w-36 lg:w-52 opacity-95 pointer-events-none z-10 select-none" alt="Top Left">
    <img src="{{ asset('images/mockup_top_right.png') }}" class="absolute top-0 right-0 w-24 sm:w-32 md:w-44 lg:w-64 opacity-95 pointer-events-none z-0 select-none" alt="Top Right">
    <img src="{{ asset('images/mockup_bottom_left.png') }}" class="absolute bottom-0 left-0 w-24 sm:w-36 md:w-48 lg:w-72 opacity-95 pointer-events-none z-0 select-none" alt="Bottom Left">
    <img src="{{ asset('images/mockup_bottom_right.png') }}" class="absolute bottom-0 right-0 w-16 sm:w-20 md:w-28 lg:w-40 opacity-95 pointer-events-none z-10 select-none" alt="Blossom Right">
    
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

        <!-- Global Error Alert -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative font-serif text-sm z-50">
                <strong class="font-bold">Gagal menyimpan foto:</strong>
                <ul class="list-disc pl-5 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Success Alert -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative font-serif text-sm z-50">
                {{ session('success') }}
            </div>
        @endif

        <!-- Main Workspace -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 my-auto items-start">
            
            <!-- Left Side: Upload Memory Form -->
            <div class="lg:col-span-4 bg-[#F4EFE6] border-2 border-espresso p-6 rounded-xl shadow-md flex flex-col gap-4 relative">
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-tiramisu-dark/60 w-24 h-6 rotate-[-1deg] border border-espresso opacity-70"></div>
                
                <h2 class="font-serif text-lg font-bold text-espresso-dark mt-2">[ 📝 UNGGAH MEMORI BARU ]</h2>
                <p class="font-serif text-xs text-espresso/70 leading-relaxed">Tambahkan kenangan foto terbaru (Maks. 2MB)</p>
                
                <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4 mt-2">
                    @csrf
                    <!-- Image Input -->
                    <div class="flex flex-col gap-1">
                        <label class="font-serif text-xs font-bold text-espresso">Pilih Foto:</label>
                        <input type="file" name="image" accept="image/*" required class="text-xs bg-cream-light p-2 rounded border border-cocoa-light focus:outline-none file:mr-3 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-xs file:font-serif file:bg-espresso file:text-cream-light hover:file:bg-cocoa-medium cursor-pointer">
                        @error('image')
                            <span class="text-red-700 text-[11px] font-bold mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Caption Input -->
                    <div class="flex flex-col gap-1">
                        <label class="font-serif text-xs font-bold text-espresso">Catatan Tulisan Tangan:</label>
                        <input type="text" name="caption" value="{{ old('caption') }}" placeholder="Contoh: Candid pas hangout 🍜" class="text-sm bg-cream-light p-2.5 rounded border border-cocoa-light font-hand focus:outline-none" required>
                        @error('caption')
                            <span class="text-red-700 text-[11px] font-bold mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Size Input -->
                    <div class="flex flex-col gap-1">
                        <label class="font-serif text-xs font-bold text-espresso">Ukuran Foto:</label>
                        <select name="size" class="text-sm bg-cream-light p-2.5 rounded border border-cocoa-light focus:outline-none cursor-pointer">
                            <option value="small" @selected(old('size') === 'small')>Kecil (170px)</option>
                            <option value="medium" @selected(old('size', 'medium') === 'medium')>Sedang (240px)</option>
                            <option value="large" @selected(old('size') === 'large')>Besar (290px)</option>
                        </select>
                        @error('size')
                            <span class="text-red-700 text-[11px] font-bold mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-espresso text-cream-light font-serif font-bold text-sm rounded shadow hover:bg-cocoa-medium transition mt-2 cursor-pointer">
                        SIMPAN DI SCRAPBOOK
                    </button>
                </form>
            </div>

            <!-- Right Side: Polaroid Gallery Collage -->
            <div class="lg:col-span-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 justify-center justify-items-center">
                    
                    @forelse($photos as $index => $photo)
                        @php
                            $rotations = ['rotate-[-3deg]', 'rotate-[4deg]', 'rotate-[-2deg]', 'rotate-[3deg]', 'rotate-[-4deg]'];
                            $rotation = $rotations[$index % count($rotations)];
                            
                            $sizeClasses = [
                                'small' => 'w-36 sm:w-40',
                                'medium' => 'w-44 sm:w-52',
                                'large' => 'w-48 sm:w-60'
                            ];
                            $sizeClass = $sizeClasses[$photo->size] ?? 'w-44 sm:w-52';
                        @endphp
                        <div class="polaroid-card relative bg-white p-3 pb-6 border border-gray-200 rounded-sm {{ $rotation }} {{ $sizeClass }} shadow-md hover:scale-105 transition-transform duration-200">
                            
                            <!-- Tombol Hapus Merah di Sudut Kanan Atas -->
                            <form action="{{ route('gallery.destroy', $photo->id) }}" method="POST" class="absolute -top-3 -right-3 z-30">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-800 text-white w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold shadow-md border-2 border-white cursor-pointer transition transform hover:scale-110" onclick="return confirm('Hapus foto memori ini?')" title="Hapus foto">
                                    &times;
                                </button>
                            </form>

                            <!-- Render Image -->
                            <img src="{{ str_starts_with($photo->image_path, 'data:') ? $photo->image_path : asset($photo->image_path) }}" class="w-full h-40 object-cover rounded-sm" alt="Memory">
                            <p class="font-hand text-center text-espresso text-lg mt-3">{{ $photo->caption }}</p>
                        </div>
                    @empty
                        <!-- Pesan Saat Galeri Kosong -->
                        <div class="col-span-full py-16 text-center flex flex-col items-center justify-center border-2 border-dashed border-cocoa-light/40 rounded-xl bg-cream-light/30 w-full p-8">
                            <span class="text-4xl mb-2">📸</span>
                            <p class="font-serif text-espresso font-bold text-base">Belum ada foto memori</p>
                            <p class="font-serif text-xs text-espresso/70 mt-1">Gunakan formulir di sebelah kiri untuk menambahkan foto scrapbook pertamamu!</p>
                        </div>
                    @endforelse

                </div>
            </div>

        </div>
    </div>
</body>
</html>