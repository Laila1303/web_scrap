<?php

namespace App\Http\Controllers;

use App\Models\TimeCapsule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimeCapsuleController extends Controller
{
    public function index()
    {
        $capsules = TimeCapsule::orderBy('created_at', 'desc')->get();

        return view('kapsul_waktu', compact('capsules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sender' => 'nullable|string|max:100',
            'content' => 'required|string',
            'type' => 'required|in:letter,time_capsule',
            'unlock_relative' => 'nullable|string',
            'unlock_custom' => 'nullable|date',
        ]);

        $unlockAt = Carbon::now();

        if ($request->type === 'letter') {
            // Surat biasa langsung terbuka seketika
            $unlockAt = Carbon::now()->subMinutes(1);
        } else {
            // Logika pembukaan kapsul waktu
            if ($request->unlock_relative === '2_years') {
                $unlockAt = Carbon::now()->addYears(2);
            } elseif ($request->unlock_relative === '5_years') {
                $unlockAt = Carbon::now()->addYears(5);
            } elseif ($request->unlock_relative === 'custom') {
                if (! empty($request->unlock_custom)) {
                    $unlockAt = Carbon::parse($request->unlock_custom)->setTime(Carbon::now()->hour, Carbon::now()->minute, Carbon::now()->second);
                } else {
                    // Fallback jika tanggal tidak sengaja kosong: dibuat terkunci 1 tahun ke depan
                    $unlockAt = Carbon::now()->addYear();
                }
            } else {
                // Default: 1 tahun
                $unlockAt = Carbon::now()->addYear();
            }
        }

        TimeCapsule::create([
            'sender' => $request->sender ?: 'Sahabat Misterius',
            'content' => $request->content,
            'unlock_at' => $unlockAt,
            'type' => $request->type,
        ]);

        return redirect()->back()->with('success', 'Pesan berhasil disimpan ke dalam kapsul waktu!');
    }
}
