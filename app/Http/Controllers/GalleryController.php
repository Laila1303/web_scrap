<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GalleryPhoto;

class GalleryController extends Controller
{
    /**
     * Menampilkan halaman galeri dengan semua foto yang diurutkan berdasarkan waktu pembuatan terbaru.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $photos = GalleryPhoto::orderBy('created_at', 'desc')->get();
        return view('galeri', compact('photos'));
    }

    /**
     * Menyimpan foto memori baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            // Batas maksimal file disesuaikan menjadi 2MB (2048 KB) agar stabil
            // saat dikonversi ke Base64 dan disimpan di serverless environment/database.
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', 
            'caption' => 'nullable|string|max:200',
            'size' => 'required|in:small,medium,large',
        ]);

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            
            // Mengambil tipe MIME gambar (misalnya image/jpeg)
            $mimeType = $imageFile->getMimeType();
            
            // Membaca konten file gambar dan mengonversinya ke string Base64
            // Ini bekerja tanpa memerlukan ekstensi PHP GD yang mungkin tidak ada di Vercel
            $base64Data = base64_encode(file_get_contents($imageFile->getRealPath()));
            
            // Membuat Data URL lengkap untuk ditampilkan langsung di tag <img> src
            $dataUrl = "data:{$mimeType};base64,{$base64Data}";

            // Simpan Data URL (Base64) langsung ke kolom LONGTEXT di database
            GalleryPhoto::create([
                'image_path' => $dataUrl,
                'caption' => $request->caption ?: 'Memori Indah',
                'size' => $request->size,
            ]);

            return redirect()->back()->with('success', 'Foto memori berhasil ditambahkan!');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah foto.');
    }

    /**
     * Menghapus foto memori yang ditentukan dari database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Mencari foto berdasarkan ID atau memunculkan error 404 jika tidak ditemukan
        $photo = GalleryPhoto::findOrFail($id);
        
        // Hapus data dari database.
        // Karena menggunakan penyimpanan Base64 di database, tidak ada file fisik yang perlu dihapus.
        $photo->delete();

        return redirect()->back()->with('success', 'Foto memori berhasil dihapus!');
    }
}