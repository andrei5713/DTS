<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public static function createDocumentReceivedNotification($document, $recipientUserId)
    {
        $recipient = User::find($recipientUserId);
        if (!$recipient) {
            return null;
        }

        return Notification::create([
            'user_id' => $recipientUserId,
            'type' => 'document_received',
            'title' => 'New Document Received',
            'message' => "You have received a new document: {$document->subject}",
            'data' => [
                'document_id' => $document->id,
                'tracking_code' => $document->tracking_code,
                'document_type' => $document->document_type,
                'upload_by' => $document->upload_by,
                'originating_office' => $document->originating_office,
            ]
        ]);
    }

    public static function createDocumentForwardedNotification($document, $recipientUserId)
    {
        $recipient = User::find($recipientUserId);
        if (!$recipient) {
            return null;
        }

        return Notification::create([
            'user_id' => $recipientUserId,
            'type' => 'document_forwarded',
            'title' => 'Document Forwarded',
            'message' => "A document has been forwarded to you: {$document->subject}",
            'data' => [
                'document_id' => $document->id,
                'tracking_code' => $document->tracking_code,
                'document_type' => $document->document_type,
                'forwarded_by' => $document->current_recipient_id,
            ]
        ]);
    }

    public static function createDocumentCompliedNotification($document, $uploaderUserId)
    {
        $uploader = User::find($uploaderUserId);
        if (!$uploader) {
            return null;
        }

        return Notification::create([
            'user_id' => $uploaderUserId,
            'type' => 'document_complied',
            'title' => 'Document Complied',
            'message' => "Your document has been complied: {$document->subject}",
            'data' => [
                'document_id' => $document->id,
                'tracking_code' => $document->tracking_code,
                'complied_by' => $document->complied_by,
                'complied_at' => $document->complied_at,
            ]
        ]);
    }

    public static function createDocumentRejectedNotification($document, $uploaderUserId)
    {
        $uploader = User::find($uploaderUserId);
        if (!$uploader) {
            return null;
        }

        return Notification::create([
            'user_id' => $uploaderUserId,
            'type' => 'document_rejected',
            'title' => 'Document Rejected',
            'message' => "Your document has been rejected: {$document->subject}. Reason: {$document->rejection_reason}",
            'data' => [
                'document_id' => $document->id,
                'tracking_code' => $document->tracking_code,
                'rejection_reason' => $document->rejection_reason,
                'rejected_at' => now(),
            ]
        ]);
    }

    public static function createDocumentReceivedIncomingNotification($document, $recipientUserId)
    {
        $recipient = User::find($recipientUserId);
        if (!$recipient) {
            return null;
        }

        return Notification::create([
            'user_id' => $recipientUserId,
            'type' => 'document_received_incoming',
            'title' => 'Document Received',
            'message' => "You have received a new document in your incoming documents: {$document->subject}",
            'data' => [
                'document_id' => $document->id,
                'tracking_code' => $document->tracking_code,
                'document_type' => $document->document_type,
                'upload_by' => $document->upload_by,
                'originating_office' => $document->originating_office,
            ]
        ]);
    }

    public static function createDocumentForwardedReceivedNotification($document, $recipientUserId)
    {
        $recipient = User::find($recipientUserId);
        if (!$recipient) {
            return null;
        }

        return Notification::create([
            'user_id' => $recipientUserId,
            'type' => 'document_forwarded_received',
            'title' => 'Forwarded Document Received',
            'message' => "You have received a forwarded document: {$document->subject}",
            'data' => [
                'document_id' => $document->id,
                'tracking_code' => $document->tracking_code,
                'document_type' => $document->document_type,
                'forwarded_by' => $document->forwarded_by,
                'forward_notes' => $document->forward_notes,
            ]
        ]);
    }

}
