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
        try {
            $mua = Mua::all(); // Mengambil semua data MUA dari database MySQL
            return response()->json([
                'status' => 'success',
                'data' => $mua
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 2. Fungsi menerima lemparan data dari HP pas Customer klik "Booking"
    public function storeBooking(Request $request)
    {
        // Validasi input data dari Flutter Nike
        $request->validate([
            'mua_id' => 'required',
            'customer_email' => 'required', // Kita tangkap email login Firebase HP-nya
            'booking_date' => 'required',
            'service' => 'required',
            'location' => 'required',       // Lokasi acara/makeup
        ]);

        try {
            // Simpan langsung ke database MySQL Laravel
            $booking = Booking::create([
                'mua_id' => $request->mua_id,
                'customer_email' => $request->customer_email, 
                'booking_date' => $request->booking_date,
                'service' => $request->service,
                'location' => $request->location,
                'status' => 'pending', // Otomatis berstatus pending biar bisa di-monitoring MUA di web
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'BOOM! Data booking dari HP berhasil masuk ke Web Laravel!',
                'data' => $booking
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan ke database: ' . $e->getMessage()
            ], 500);
        }
    }
}