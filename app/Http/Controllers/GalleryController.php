<?php

namespace App\Http\Controllers;

use App\Models\GalleryPhoto;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $photos = GalleryPhoto::latest()->get();
        return view('galeri', compact('photos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:10240',
            'caption' => 'nullable|string|max:100',
            'size' => 'nullable|string|in:small,medium,large',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $dataUrl = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));

            GalleryPhoto::create([
                'image_path' => $dataUrl,
                'caption' => $request->caption ?: 'Memori Indah',
                'size' => $request->size ?: 'medium',
            ]);

            // Gunakan 'gallery' sesuai nama route di web.php
            return redirect()->route('gallery')->with('success', 'Foto memori berhasil ditambahkan!');
        }

        return redirect()->back()->with('error', 'Gagal membaca file foto.');
    }

    public function destroy($id)
    {
        $photo = GalleryPhoto::findOrFail($id);
        $photo->delete();

        return redirect()->back()->with('success', 'Foto memori berhasil dihapus!');
    }
}