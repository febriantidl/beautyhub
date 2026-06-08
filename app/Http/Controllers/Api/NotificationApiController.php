<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    // GET /api/notifications
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($notif) {
                return [
                    'id'         => $notif->id,
                    'type'       => $notif->type,
                    'title'      => $notif->data['title'] ?? 'Notifikasi',
                    'message'    => $notif->data['message'] ?? '',
                    'data'       => $notif->data,
                    'read_at'    => $notif->read_at,
                    'created_at' => $notif->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $notifications,
        ]);
    }

    // POST /api/notifications/read-all
    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}