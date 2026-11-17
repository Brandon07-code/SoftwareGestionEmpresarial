<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Bandeja de notificaciones del usuario (campana del menú).
 * Cada usuario solo ve SUS notificaciones: $request->user()->notifications.
 */
class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return view('notifications.index', [
            'notifications' => $request->user()->notifications()->paginate(15),
        ]);
    }

    // Marca una como leída y lleva a la pantalla relacionada
    public function read(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect($notification->data['url'] ?? $notification->data['action_url'] ?? route('notifications.index'));
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Todas las notificaciones fueron marcadas como leídas.');
    }

    // Aliases para compatibilidad existente
    public function markAllAsRead(Request $request)
    {
        return $this->readAll($request);
    }

    public function markAsRead(Request $request, string $id)
    {
        return $this->read($request, $id);
    }
}
