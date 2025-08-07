<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get documents based on user role and unit
        $documents = Document::with(['uploadByUser', 'uploadToUser', 'currentRecipient'])
            ->where(function($query) use ($user) {
                $query->where('upload_by_user_id', $user->id)
                      ->orWhere('upload_to_user_id', $user->id)
                      ->orWhere('current_recipient_id', $user->id);
            })
            ->latest()
            ->get();
            
        return response()->json([
            'documents' => $documents
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tracking_code' => 'required|string|unique:documents',
            'document_type' => 'required|string',
            'subject' => 'required|string',
            'entry_date' => 'required|date',
            'upload_by' => 'required|string',
            'upload_to' => 'required|string',
            'originating_office' => 'required|string',
            'forward_to_department' => 'nullable|string',
            'origin_type' => 'required|string',
            'priority' => 'required|string',
            'remarks' => 'nullable|string',
            'routing' => 'required|string',
            'file' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        $user = Auth::user();
        
        // Check if user can upload (must be from /DO unit)
        $userUnit = $user->unit->full_name ?? '';
        if (!str_ends_with($userUnit, '/DO')) {
            return response()->json([
                'success' => false,
                'message' => 'Only users from Director\'s Office units can upload documents.'
            ], 403);
        }

        // Find the upload_to user
        $uploadToUser = User::where('name', $request->upload_to)->first();
        if (!$uploadToUser) {
            return response()->json([
                'success' => false,
                'message' => 'Selected user not found.'
            ], 400);
        }

        // Handle file upload
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        // Determine the initial recipient based on routing logic
        $targetUnit = $uploadToUser->unit->full_name ?? '';
        $initialRecipient = $this->getInitialRecipient($targetUnit);

        // Create the document
        $document = Document::create([
            'tracking_code' => $request->tracking_code,
            'document_type' => $request->document_type,
            'subject' => $request->subject,
            'document_date' => $request->document_date ?? now(),
            'entry_date' => $request->entry_date,
            'upload_by' => $request->upload_by,
            'upload_by_user_id' => $user->id,
            'upload_to' => $request->upload_to,
            'upload_to_user_id' => $uploadToUser->id,
            'originating_office' => $request->originating_office,
            'forward_to_department' => $request->forward_to_department,
            'origin_type' => $request->origin_type,
            'priority' => $request->priority,
            'remarks' => $request->remarks,
            'routing' => $request->routing,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'status' => 'pending',
            'current_recipient_id' => $initialRecipient->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully.',
            'document' => $document
        ]);
    }

    public function receive(Document $document)
    {
        $user = Auth::user();
        
        if ($document->current_recipient_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to receive this document.'
            ], 403);
        }

        $document->update([
            'status' => 'received',
            'current_recipient_id' => $user->id, // Move to the user who accepted it
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document accepted successfully.'
        ]);
    }

    public function forward(Request $request, Document $document)
    {
        $user = Auth::user();
        
        if ($document->current_recipient_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to forward this document.'
            ], 403);
        }

        $request->validate([
            'forward_to_user' => 'required|string',
            'forward_notes' => 'nullable|string',
        ]);

        // Find the user to forward to
        $forwardToUser = User::where('name', $request->forward_to_user)->first();
        if (!$forwardToUser) {
            return response()->json([
                'success' => false,
                'message' => 'Selected user not found.'
            ], 400);
        }

        $document->update([
            'status' => 'forwarded',
            'current_recipient_id' => $forwardToUser->id,
            'forward_notes' => $request->forward_notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document forwarded successfully.'
        ]);
    }

    public function reject(Document $document)
    {
        $user = Auth::user();
        
        if ($document->current_recipient_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to reject this document.'
            ], 403);
        }

        $document->update([
            'status' => 'rejected',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document rejected successfully.'
        ]);
    }

    private function getInitialRecipient($targetUnit)
    {
        // If target unit ends with /DO, send directly
        if (str_ends_with($targetUnit, '/DO')) {
            return User::whereHas('unit', function($query) use ($targetUnit) {
                $query->where('full_name', $targetUnit);
            })->first();
        }
        
        // Otherwise, route to the appropriate /DO unit
        if (str_starts_with($targetUnit, 'FD/')) {
            return User::whereHas('unit', function($query) {
                $query->where('full_name', 'FD/DO');
            })->first();
        } else {
            return User::whereHas('unit', function($query) {
                $query->where('full_name', 'CPMSD/DO');
            })->first();
        }
    }

    public function canUserUpload()
    {
        $user = Auth::user();
        $userUnit = $user->unit->full_name ?? '';
        $canUpload = str_ends_with($userUnit, '/DO');
        
        return response()->json([
            'can_upload' => $canUpload,
            'user_unit' => $userUnit
        ]);
    }
}
