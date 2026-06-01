<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mua;      // Sesuaikan dengan nama model MUA di Laravel lu
use App\Models\Booking;  // Sesuaikan dengan nama model Booking di Laravel lu

class MobileIntegrationController extends Controller
{
    // 1. Fungsi agar Mobile bisa narik data MUA hasil CRUD dari Web lu
    public function getMua()
{
    $mua = Mua::with('user')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $mua
    ]);
}

    // 2. Fungsi menerima lemparan data dari HP pas Customer klik "Booking"
    
    
}