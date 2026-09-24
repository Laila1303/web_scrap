<?php

use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PhotoboothController;
use App\Http\Controllers\TimeCapsuleController;
use App\Models\DailyMood;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 1. Dashboard / Welcome
Route::get('/', function () {
    $audioDir = public_path('audio');
    $customTracks = [];
    if (file_exists($audioDir)) {
        $files = glob($audioDir.'/*.mp3');
        if ($files) {
            foreach ($files as $file) {
                $filename = basename($file);
                $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
                $parts = explode(' - ', $nameWithoutExt);

                $title = $parts[0];
                $artist = isset($parts[1]) ? $parts[1] : 'Playlist Kayla';

                $customTracks[] = [
                    'title' => str_replace('_', ' ', $title),
                    'artist' => str_replace('_', ' ', $artist),
                    'url' => '/audio/'.rawurlencode($filename),
                ];
            }
        }
    }

    return view('welcome', compact('customTracks'));
})->name('dashboard');

// 2. Photobooth
Route::get('/photobooth', [PhotoboothController::class, 'index'])->name('photobooth');
Route::post('/photobooth/generate', [PhotoboothController::class, 'generate'])->name('photobooth.generate');

// 3. Galeri (Mendukung name 'gallery' dan 'gallery.index')
Route::get('/galeri', [GalleryController::class, 'index'])->name('gallery');
Route::get('/galeri/index', [GalleryController::class, 'index'])->name('gallery.index');
Route::post('/galeri', [GalleryController::class, 'store'])->name('gallery.store');
Route::delete('/galeri/{id}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

// 4. Surat dari Aku
Route::get('/surat-dari-aku', function () {
    if (view()->exists('surat_dari_aku')) {
        return view('surat_dari_aku');
    }
    if (view()->exists('surat')) {
        return view('surat');
    }
    return redirect()->route('dashboard');
})->name('surat-dari-aku');

// 5. Kapsul Waktu
Route::get('/kapsul-waktu', function () {
    if (class_exists(TimeCapsuleController::class) && method_exists(TimeCapsuleController::class, 'index')) {
        return app(TimeCapsuleController::class)->index();
    }
    if (view()->exists('kapsul_waktu')) {
        return view('kapsul_waktu');
    }
    if (view()->exists('kapsul')) {
        return view('kapsul');
    }
    return redirect()->route('dashboard');
})->name('kapsul-waktu');

Route::post('/kapsul-waktu', function (Request $request) {
    if (class_exists(TimeCapsuleController::class) && method_exists(TimeCapsuleController::class, 'store')) {
        return app(TimeCapsuleController::class)->store($request);
    }
    return redirect()->back();
})->name('kapsul-waktu.store');

// 6. Upload Handlers di Welcome Dashboard
Route::post('/upload-kayla', function (Request $request) {
    $request->validate(['kayla_photo' => 'required|image|max:5120']);
    if ($request->hasFile('kayla_photo')) {
        $request->file('kayla_photo')->move(public_path('images'), 'kayla.jpg');
        return redirect()->back()->with('success', 'Foto album art berhasil diperbarui!');
    }
    return redirect()->back()->with('error', 'Gagal memperbarui foto.');
})->name('upload-kayla');

Route::post('/upload-camera-photo', function (Request $request) {
    $request->validate(['camera_photo' => 'required|image|max:5120']);
    if ($request->hasFile('camera_photo')) {
        $request->file('camera_photo')->move(public_path('images'), 'custom_camera.png');
        return redirect()->back()->with('success', 'Foto digicam berhasil diperbarui!');
    }
    return redirect()->back()->with('error', 'Gagal memperbarui foto digicam.');
})->name('upload-camera-photo');

Route::post('/upload-polaroid/{id}', function (Request $request, $id) {
    $request->validate(['polaroid_photo' => 'required|image|max:5120']);
    if ($request->hasFile('polaroid_photo')) {
        $request->file('polaroid_photo')->move(public_path('images'), 'polaroid_'.$id.'.png');
        return redirect()->back()->with('success', 'Foto polaroid '.$id.' berhasil diperbarui!');
    }
    return redirect()->back()->with('error', 'Gagal memperbarui polaroid.');
})->name('upload-polaroid');

// 7. Daily Journal & Mood Tracker (Filter Khusus Hari Ini)
Route::get('/daily-journal', function () {
    $today = now()->toDateString();
    $todos = [];
    $todayMood = null;

    try {
        if (class_exists(Todo::class)) {
            $todos = Todo::where(function ($query) use ($today) {
                            $query->whereDate('date', $today)
                                  ->orWhereDate('created_at', $today);
                        })
                        ->orderBy('id', 'asc')
                        ->get();
        }
    } catch (\Throwable $e) {
        $todos = [];
    }

    try {
        if (class_exists(DailyMood::class)) {
            $todayMood = DailyMood::where('date', $today)->latest()->first();
        }
    } catch (\Throwable $e) {
        $todayMood = null;
    }

    return view('daily_journal', compact('todos', 'todayMood'));
})->name('daily-journal');

// Simpan Mood Harian
Route::post('/daily-mood', function (Request $request) {
    $request->validate([
        'mood_emoji' => 'required|string',
        'mood_label' => 'required|string',
    ]);

    try {
        $today = now()->toDateString();
        DailyMood::updateOrCreate(
            ['date' => $today],
            [
                'mood_emoji' => $request->mood_emoji,
                'mood_label' => $request->mood_label,
            ]
        );
        return redirect()->route('daily-journal')->with('success', 'Mood hari ini berhasil dicatat! ✨');
    } catch (\Throwable $e) {
        return redirect()->route('daily-journal')->with('error', 'Gagal mencatat mood: ' . $e->getMessage());
    }
})->name('mood.store');

// Simpan Catatan To-Do Baru (Tersimpan dengan Tanggal Hari Ini)
Route::post('/daily-journal', function (Request $request) {
    $request->validate([
        'task' => 'required|string|max:255'
    ]);

    try {
        $today = now()->toDateString();
        Todo::create([
            'task' => $request->task,
            'is_completed' => false,
            'date' => $today,
        ]);
        return redirect()->route('daily-journal')->with('success', 'Target hari ini berhasil ditambahkan! 🌸');
    } catch (\Throwable $e) {
        return redirect()->route('daily-journal')->with('error', 'Gagal menambahkan catatan: ' . $e->getMessage());
    }
})->name('todo.store');

// Toggle Ceklis To-Do
Route::patch('/daily-journal/{todo}/toggle', function (Todo $todo) {
    try {
        $todo->update([
            'is_completed' => !$todo->is_completed
        ]);
    } catch (\Throwable $e) {}
    return redirect()->route('daily-journal');
})->name('todo.toggle');

// Hapus Catatan To-Do
Route::delete('/daily-journal/{todo}', function (Todo $todo) {
    try {
        $todo->delete();
        return redirect()->route('daily-journal')->with('success', 'Catatan berhasil dihapus!');
    } catch (\Throwable $e) {
        return redirect()->route('daily-journal')->with('error', 'Gagal menghapus catatan.');
    }
})->name('todo.destroy');