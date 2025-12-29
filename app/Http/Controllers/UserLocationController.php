<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;



class UserLocationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $log = \App\Models\LoginLog::where('user_id', auth()->id())->latest()->first();

        if ($log) {
            $currentLocation = $log->location ?? [];
            $currentLocation['latitude'] = $data['latitude'];
            $currentLocation['longitude'] = $data['longitude'];

            $log->update(['location' => $currentLocation]);
        }

        return response()->json(['status' => 'success']);
    }
}
