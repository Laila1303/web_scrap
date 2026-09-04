<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\GalleryPhoto;
use Illuminate\Support\Str;

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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // Limit to 10MB
            'caption' => 'nullable|string|max:200',
            'size' => 'required|in:small,medium,large',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('gallery', 'public');
            $imagePath = 'storage/' . $path;

            GalleryPhoto::create([
                'image_path' => $imagePath,
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
        
        // Remove physical file from public path
        $filePath = public_path($photo->image_path);
        if (file_exists($filePath)) {
            @unlink($filePath);
        }

        $photo->delete();

        return redirect()->back()->with('success', 'Foto memori berhasil dihapus!');
    }
}
