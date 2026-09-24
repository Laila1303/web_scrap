<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kayla's Daily Journal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="scrapbook-paper linen-texture min-h-screen text-espresso font-sans flex flex-col justify-between p-2 sm:p-4 md:p-8 relative overflow-x-hidden">

    <!-- Corner Decorations -->
    <img src="/images/mockup_top_left.png" class="absolute top-0 left-0 w-20 sm:w-28 md:w-36 lg:w-52 opacity-95 pointer-events-none z-0 select-none" alt="Top Left">
    <img src="/images/mockup_bottom_right.png" class="absolute bottom-0 right-0 w-16 sm:w-20 md:w-28 lg:w-40 opacity-95 pointer-events-none z-0 select-none" alt="Bottom Right">

    <!-- Main Wrapper -->
    <div class="max-w-2xl w-full mx-auto z-10 flex-1 flex flex-col justify-between gap-4 my-auto">
        
        <!-- Header -->
        <header class="flex justify-between items-center border-b-2 border-dashed border-cocoa-light/30 pb-3">
            <a href="{{ url('/') }}" class="bg-espresso text-cream-light font-serif text-xs px-3 py-1.5 rounded shadow hover:bg-cocoa-medium transition flex items-center gap-1">
                <span>&larr;</span> Kembali
            </a>
            <div class="text-center">
                <h1 class="font-serif text-2xl sm:text-3xl font-bold tracking-tight text-espresso-dark">KAYLA'S JOURNAL</h1>
                <p class="font-hand text-base sm:text-lg text-cocoa-medium">Page 20 / 365 🌸</p>
            </div>
            <div class="w-16"></div>
        </header>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded font-serif text-xs text-center z-50">
                {{ session('success') }}
            </div>
        @endif

        <!-- Card Buku Agenda Bergaris (Vintage Binder + Mood) -->
        <div class="bg-[#FDFBF7] border-2 border-espresso p-4 sm:p-6 rounded-xl shadow-lg relative">
            <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-cocoa-medium text-cream-light text-[10px] uppercase font-bold tracking-wider px-4 py-0.5 rounded shadow">
                📌 DAILY NOTES &amp; MOOD
            </div>

            <!-- Bagian Mood Tracker (Menyatu di Header Kertas) -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 border-b-2 border-dashed border-cocoa-light/30 pb-3 mb-3">
                <div class="text-center sm:text-left">
                    <span class="font-mono text-[10px] uppercase tracking-wider text-cocoa-medium">DATE: {{ date('d M Y') }}</span>
                    <h3 class="font-serif text-xs sm:text-sm font-bold text-espresso">Today's Mood: 
                        <span class="font-hand text-base text-cocoa-medium font-normal">
                            {{ $todayMood ? $todayMood->mood_emoji.' '.$todayMood->mood_label : 'Belum dipilih' }}
                        </span>
                    </h3>
                </div>

                <!-- 5 Stempel Mood Lucu -->
                <div class="flex items-center gap-1.5 bg-cream-light/60 p-1.5 rounded-lg border border-cocoa-light/30">
                    @php
                        $moods = [
                            ['emoji' => '🌸', 'label' => 'Happy'],
                            ['emoji' => '✨', 'label' => 'Productive'],
                            ['emoji' => '🍵', 'label' => 'Calm'],
                            ['emoji' => '🌧️', 'label' => 'Tired'],
                            ['emoji' => '🧸', 'label' => 'Rest'],
                        ];
                    @endphp

                    @foreach($moods as $m)
                        <form action="{{ route('mood.store') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="mood_emoji" value="{{ $m['emoji'] }}">
                            <input type="hidden" name="mood_label" value="{{ $m['label'] }}">
                            <button type="submit" title="{{ $m['label'] }}" class="w-8 h-8 rounded border border-espresso/60 flex items-center justify-center text-sm transition transform hover:scale-110 active:scale-95 cursor-pointer {{ ($todayMood && $todayMood->mood_label === $m['label']) ? 'bg-espresso shadow-md ring-2 ring-cocoa-medium' : 'bg-white hover:bg-cream-light' }}">
                                {{ $m['emoji'] }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>

            <!-- Area Kertas Garis Binder untuk To-Do List -->
            <div class="border-l-4 border-double border-cocoa-light/40 pl-3 sm:pl-5 py-2 flex flex-col gap-1 min-h-[260px]">
                
                @forelse($todos as $todo)
                    <div class="flex items-center justify-between gap-2 border-b border-cocoa-light/20 py-2 hover:bg-black/[0.02] transition px-1">
                        <!-- Checklist Button + Text -->
                        <form action="{{ route('todo.toggle', $todo->id) }}" method="POST" class="flex items-center gap-2.5 flex-1 min-w-0">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-5 h-5 rounded border-2 border-espresso shrink-0 flex items-center justify-center text-xs transition cursor-pointer {{ $todo->is_completed ? 'bg-espresso text-cream-light font-bold' : 'bg-white hover:bg-cream-light' }}">
                                @if($todo->is_completed) ✓ @endif
                            </button>
                            <span class="font-serif text-xs sm:text-sm truncate select-none {{ $todo->is_completed ? 'line-through text-cocoa-light/70 italic' : 'text-espresso font-medium' }}">
                                {{ $todo->task }}
                            </span>
                        </form>

                        <!-- Tombol Hapus -->
                        <form action="{{ route('todo.destroy', $todo->id) }}" method="POST" class="shrink-0" onsubmit="return confirm('Hapus catatan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-cocoa-light hover:text-red-600 transition px-1.5 py-0.5 font-bold">
                                ✕
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <span class="text-2xl">📝</span>
                        <p class="font-hand text-sm text-cocoa-medium mt-1">Belum ada target yang ditulis. Tambah rencana baru di bawah!</p>
                    </div>
                @endforelse

                <!-- Baris Tambah Item Baru Langsung di Kertas -->
                <form action="{{ route('todo.store') }}" method="POST" class="flex items-center gap-2 border-b-2 border-dashed border-cocoa-light/40 py-2 mt-2">
                    @csrf
                    <span class="text-cocoa-medium font-bold text-sm shrink-0 pl-1">＋</span>
                    <input type="text" name="task" placeholder="Tulis rencana / target baru di sini..." class="w-full bg-transparent border-none text-xs sm:text-sm font-serif italic text-espresso focus:outline-none placeholder:text-cocoa-light/50" required autocomplete="off">
                    <button type="submit" class="shrink-0 bg-espresso text-cream-light font-serif text-[11px] px-3 py-1 rounded hover:bg-cocoa-medium transition">
                        Add Note
                    </button>
                </form>

            </div>

            <!-- Quote Footer -->
            <div class="text-center mt-5">
                <span class="font-hand text-xs sm:text-sm text-cocoa-medium italic">~ One step at a time, Kayla ~</span>
            </div>
        </div>

        <div class="h-2"></div>
    </div>

</body>
</html>