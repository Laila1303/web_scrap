<?php

use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PhotoboothController;
use App\Http\Controllers\TimeCapsuleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $audioDir = public_path('audio');
    $customTracks = [];
    if (file_exists($audioDir)) {
        $files = glob($audioDir.'/*.mp3');
        foreach ($files as $file) {
            $filename = basename($file);
            $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
            $parts = explode(' - ', $nameWithoutExt);

            $title = $parts[0];
            $artist = isset($parts[1]) ? $parts[1] : 'Playlist Kayla';

            $customTracks[] = [
                'title' => str_replace('_', ' ', $title),
                'artist' => str_replace('_', ' ', $artist),
                'url' => asset('audio/'.rawurlencode($filename)),
            ];
        }
    }

    return view('welcome', compact('customTracks'));
})->name('dashboard');

// Photobooth routes
Route::get('/photobooth', [PhotoboothController::class, 'index'])->name('photobooth');
Route::post('/photobooth/generate', [PhotoboothController::class, 'generate'])->name('photobooth.generate');

// Gallery routes
Route::get('/galeri', [GalleryController::class, 'index'])->name('gallery');
Route::post('/galeri', [GalleryController::class, 'store'])->name('gallery.store');
Route::delete('/galeri/{id}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

// Letter from Aku
Route::get('/surat-dari-aku', function () {
    return view('surat_dari_aku');
})->name('surat-dari-aku');

// Time Capsule routes
Route::get('/kapsul-waktu', [TimeCapsuleController::class, 'index'])->name('kapsul-waktu');
Route::post('/kapsul-waktu', [TimeCapsuleController::class, 'store'])->name('kapsul-waktu.store');

// Main Kayla photo upload route
Route::post('/upload-kayla', function (Request $request) {
    $request->validate([
        'kayla_photo' => 'required|image|max:5120',
    ]);
    if ($request->hasFile('kayla_photo')) {
        $file = $request->file('kayla_photo');
        $file->move(public_path('images'), 'kayla.jpg');

        return redirect()->back()->with('success', 'Foto album art berhasil diperbarui!');
    }

    return redirect()->back()->with('error', 'Gagal memperbarui foto.');
})->name('upload-kayla');

// Custom camera photo upload route
Route::post('/upload-camera-photo', function (Request $request) {
    $request->validate([
        'camera_photo' => 'required|image|max:5120',
    ]);
    if ($request->hasFile('camera_photo')) {
        $file = $request->file('camera_photo');
        $file->move(public_path('images'), 'custom_camera.png');

        return redirect()->back()->with('success', 'Foto digicam berhasil diperbarui!');
    }

    return redirect()->back()->with('error', 'Gagal memperbarui foto digicam.');
})->name('upload-camera-photo');

// Polaroid photo upload route
Route::post('/upload-polaroid/{id}', function (Request $request, $id) {
    $request->validate([
        'polaroid_photo' => 'required|image|max:5120',
    ]);
    if ($request->hasFile('polaroid_photo')) {
        $file = $request->file('polaroid_photo');
        $file->move(public_path('images'), 'polaroid_'.$id.'.png');

        return redirect()->back()->with('success', 'Foto polaroid '.$id.' berhasil diperbarui!');
    }

    return redirect()->back()->with('error', 'Gagal memperbarui polaroid.');
})->name('upload-polaroid');
