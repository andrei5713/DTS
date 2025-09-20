<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $notifications = Notification::forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
            
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $notifications->where('read', false)->count()
        ]);
    }
    
    public function markAsRead(Notification $notification)
    {
        $user = Auth::user();
        
        // Ensure user can only mark their own notifications as read
        if ($notification->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }
    
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        // Debug logging
        Log::info('Mark all as read requested', [
            'user_id' => $user->id,
            'user_name' => $user->name
        ]);
        
        // Get unread notifications count before update
        $unreadCount = Notification::forUser($user->id)->unread()->count();
        Log::info('Unread notifications before update', ['count' => $unreadCount]);
        
        $updatedCount = Notification::forUser($user->id)
            ->unread()
            ->update([
                'read' => true,
                'read_at' => now()
            ]);
            
        Log::info('Mark all as read completed', [
            'user_id' => $user->id,
            'updated_count' => $updatedCount,
            'unread_count_before' => $unreadCount
        ]);
            
        return response()->json([
            'success' => true,
            'updated_count' => $updatedCount,
            'unread_count_before' => $unreadCount
        ]);
    }
    
    public function unreadCount()
    {
        $user = Auth::user();
        
        $count = Notification::forUser($user->id)
            ->unread()
            ->count();
            
        return response()->json(['count' => $count]);
    }
}