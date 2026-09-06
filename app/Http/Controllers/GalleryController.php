<?php

namespace App\Http\Controllers;

use App\Models\GalleryPhoto;
use Illuminate\Http\Request;

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
            $realPath = $imageFile->getRealPath();
            $binary = file_get_contents($realPath);

            // Compress and resize using GD if possible to keep DB lightweight and fast
            $dataUrl = null;
            $img = @imagecreatefromstring($binary);
            if ($img) {
                $width = imagesx($img);
                $height = imagesy($img);
                $maxDim = 1000;

                if ($width > $maxDim || $height > $maxDim) {
                    if ($width > $height) {
                        $newW = $maxDim;
                        $newH = (int) ($height * ($maxDim / $width));
                    } else {
                        $newH = $maxDim;
                        $newW = (int) ($width * ($maxDim / $height));
                    }
                    $resized = imagecreatetruecolor($newW, $newH);
                    imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $width, $height);
                    imagedestroy($img);
                    $img = $resized;
                }

                ob_start();
                imagejpeg($img, null, 80);
                $optimizedBinary = ob_get_clean();
                imagedestroy($img);
                $dataUrl = 'data:image/jpeg;base64,'.base64_encode($optimizedBinary);
            } else {
                $mimeType = $imageFile->getMimeType();
                $base64Data = base64_encode($binary);
                $dataUrl = "data:{$mimeType};base64,{$base64Data}";
            }

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
