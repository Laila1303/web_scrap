<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeCapsule;
use Carbon\Carbon;

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
            'unlock_custom' => 'required_if:unlock_relative,custom|nullable|date|after_or_equal:today',
        ]);

        $unlockAt = Carbon::now();

        if ($request->type === 'letter') {
            // Surat biasa langsung terbuka seketika
            $unlockAt = Carbon::now()->subMinutes(1);
        } else {
            // Pengaturan jadwal kapsul waktu
            if ($request->unlock_relative === '1_year') {
                $unlockAt = Carbon::now()->addYear();
            } elseif ($request->unlock_relative === '2_years') {
                $unlockAt = Carbon::now()->addYears(2);
            } elseif ($request->unlock_relative === '5_years') {
                $unlockAt = Carbon::now()->addYears(5);
            } elseif ($request->unlock_relative === 'custom' && $request->unlock_custom) {
                // Set tanggal kustom dengan menyamakan jam & menit saat ini
                $unlockAt = Carbon::parse($request->unlock_custom)->setTime(Carbon::now()->hour, Carbon::now()->minute, Carbon::now()->second);
            } else {
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