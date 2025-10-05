<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            // Use full unread count, not just in-page items
            'unread_count' => Notification::forUser($user->id)->unread()->count()
        ]);
    }
    
    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        
        // Scope the notification lookup to the authenticated user
        $notification = Notification::forUser($user->id)->where('id', $notificationId)->first();
        if (!$notification) {
            Log::warning('Notification not found or not owned by user when marking as read', [
                'notification_id' => $notificationId,
                'user_id' => $user->id,
            ]);
            return response()->json(['error' => 'Not found'], 404);
        }
        
        // Log the attempt
        Log::info('Mark notification as read requested', [
            'notification_id' => $notification->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'notification_user_id' => $notification->user_id,
            'notification_read' => $notification->read,
            'notification_type' => $notification->type
        ]);
        
        // Check if already read
        if ($notification->read) {
            Log::info('Notification already marked as read', [
                'notification_id' => $notification->id,
                'user_id' => $user->id
            ]);
            return response()->json(['success' => true, 'already_read' => true]);
        }
        
        // Use database transaction to ensure atomicity
        try {
            DB::transaction(function () use ($notification) {
                $notification->markAsRead();
                // Force refresh the model to get updated data
                $notification->refresh();
            });
            
            Log::info('Notification marked as read successfully', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'read_at' => $notification->read_at,
                'read_status' => $notification->read
            ]);
            
            return response()->json([
                'success' => true,
                'notification' => [
                    'id' => $notification->id,
                    'read' => $notification->read,
                    'read_at' => $notification->read_at
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Failed to mark notification as read'], 500);
        }
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