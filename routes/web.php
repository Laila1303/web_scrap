<?php

use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PhotoboothController;
use App\Http\Controllers\TimeCapsuleController;
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