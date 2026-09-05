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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'caption' => 'nullable|string|max:200',
            'size' => 'required|in:small,medium,large',
        ]);

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imagePath = $imageFile->getRealPath();

            // Resize & kompres gambar agar ukuran Base64 sangat ringan
            $sourceImage = @imagecreatefromstring(file_get_contents($imagePath));

            if ($sourceImage !== false) {
                $origWidth = imagesx($sourceImage);
                $origHeight = imagesy($sourceImage);
                $targetWidth = 800; // Standar optimal polaroid

                if ($origWidth > $targetWidth) {
                    $targetHeight = (int) (($origHeight / $origWidth) * $targetWidth);
                    $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);
                    imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $origWidth, $origHeight);
                    imagedestroy($sourceImage);
                    $sourceImage = $resizedImage;
                }

                ob_start();
                imagejpeg($sourceImage, null, 75); // Kualitas JPEG 75%
                $compressedData = ob_get_clean();
                imagedestroy($sourceImage);

                $dataUrl = 'data:image/jpeg;base64,' . base64_encode($compressedData);
            } else {
                $mime = $imageFile->getMimeType();
                $dataUrl = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($imagePath));
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