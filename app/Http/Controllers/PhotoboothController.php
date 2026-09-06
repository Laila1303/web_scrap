<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PhotoboothController extends Controller
{
    public function index()
    {
        return view('photobooth');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'image1' => 'required|string',
            'image2' => 'required|string',
            'image3' => 'required|string',
            'header_text' => 'nullable|string|max:80',
            'footer_text' => 'nullable|string|max:80',
            'template_color' => 'nullable|string',
            'photo_shape' => 'nullable|string',
        ]);

        try {
            $outputDir = public_path('storage/photobooth');
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // Simpan data foto jika dikirim via Base64
            $outputFilename = 'strip_' . time() . '_' . Str::random(5) . '.png';
            $outputPath = $outputDir . '/' . $outputFilename;

            // Decode foto pertama sebagai representasi strip
            $data = $request->input('image1');
            if (preg_match('/^data:image\/(\w+);base64,/', $data)) {
                $data = substr($data, strpos($data, ',') + 1);
                $data = base64_decode($data);
                file_put_contents($outputPath, $data);
            }

            return response()->json([
                'success' => true,
                'url' => asset('storage/photobooth/' . $outputFilename),
                'message' => 'Cetak foto strip berhasil!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}