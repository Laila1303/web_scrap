<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GalleryPhoto;

class GalleryController extends Controller
{
    public function index()
    {
        $photos = GalleryPhoto::orderBy('created_at', 'desc')->get();
        return view('galeri', compact('photos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Batas 2MB
            'caption' => 'nullable|string|max:200',
            'size' => 'required|in:small,medium,large',
        ]);

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            
            $mimeType = $imageFile->getMimeType();
            $base64Data = base64_encode(file_get_contents($imageFile->getRealPath()));
            $dataUrl = "data:{$mimeType};base64,{$base64Data}";

            GalleryPhoto::create([
                'image_path' => $dataUrl,
                'caption' => $request->caption ?: 'Memori Indah',
                'size' => $request->size,
            ]);

            return redirect()->back()->with('success', 'Foto memori berhasil ditambahkan!');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah foto.');
    }

    public function destroy($id)
    {
        $photo = GalleryPhoto::findOrFail($id);
        $photo->delete();

        return redirect()->back()->with('success', 'Foto memori berhasil dihapus!');
    }
}