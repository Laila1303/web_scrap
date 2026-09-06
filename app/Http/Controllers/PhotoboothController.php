<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
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
            'template_color' => 'nullable|string|in:classic_vintage,denim_y2k,ppg_collage,polaroid_printer',
            'photo_shape' => 'nullable|string|in:square,oval',
        ]);

        $headerText = $request->input('header_text') ?: 'Capturing Moments';
        $footerText = $request->input('footer_text') ?: 'On the road, 20!';
        $templateColor = $request->input('template_color') ?: 'espresso';
        $photoShape = $request->input('photo_shape') ?: 'square';

        try {
            // Create temp directory if not exists
            $tempDir = storage_path('app/temp_photos');
            if (! file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Decode and save temporary images
            $tempPaths = [];
            foreach (['image1', 'image2', 'image3'] as $key) {
                $data = $request->input($key);
                // Extract base64
                if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                    $data = substr($data, strpos($data, ',') + 1);
                    $data = base64_decode($data);

                    $tempPath = $tempDir.'/'.Str::random(10).'.png';
                    file_put_contents($tempPath, $data);
                    $tempPaths[] = $tempPath;
                } else {
                    throw new \Exception('Format gambar tidak valid.');
                }
            }

            if (count($tempPaths) < 3) {
                throw new \Exception('Gagal mendekode semua gambar.');
            }

            // Prepare output path
            $outputDir = public_path('storage/photobooth');
            if (! file_exists($outputDir)) {
                mkdir($outputDir, 0755, true);
            }
            $outputFilename = 'strip_'.time().'_'.Str::random(5).'.png';
            $outputPath = $outputDir.'/'.$outputFilename;

            // Execute Python script only if available and not on serverless Vercel
            $isServerless = isset($_SERVER['VERCEL']) || ! file_exists('/usr/bin/python3') && ! file_exists('C:\\Windows') && ! is_executable('/usr/bin/python');

            $pythonScript = base_path('app/Python/generate_strip.py');
            $pythonBin = 'python';

            $result = null;
            try {
                $result = Process::run([
                    $pythonBin,
                    $pythonScript,
                    $tempPaths[0],
                    $tempPaths[1],
                    $tempPaths[2],
                    $outputPath,
                    $headerText,
                    $footerText,
                    $templateColor,
                    $photoShape,
                ]);
            } catch (\Throwable $procEx) {
                // If Process fails (e.g. command not found on Vercel)
                $output = 'Serverless environment detected. Process bypassed: '.$procEx->getMessage();
            }

            $output = $result ? ($result->output().$result->errorOutput()) : ($output ?? 'No output');

            // Clean up temp files
            foreach ($tempPaths as $path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            // Check if file was created successfully
            if (file_exists($outputPath)) {
                return response()->json([
                    'success' => true,
                    'url' => asset('storage/photobooth/'.$outputFilename),
                    'message' => 'Cetak foto strip berhasil!',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat foto strip. Python output: '.$output,
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }
}
