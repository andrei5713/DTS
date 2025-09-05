<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get documents based on user role and unit
        // Users can only see documents they uploaded OR documents where they are the current recipient
        $documents = Document::with(['uploadByUser', 'uploadToUser', 'currentRecipient'])
            ->where(function($query) use ($user) {
                $query->where('upload_by_user_id', $user->id)
                      ->orWhere('current_recipient_id', $user->id);
            })
            ->latest()
            ->get();
            
        return response()->json([
            'documents' => $documents
        ]);
    }

    public function incoming(Request $request)
    {
        $user = Auth::user();
        $userUnit = $user->unit->full_name ?? '';
        $isDO = str_ends_with($userUnit, '/DO');
        $showAll = $request->query('all', false);

        if ($isDO) {
            // For DO users: show documents where they are current recipient OR documents they have acted upon
            $documents = Document::with(['uploadByUser', 'uploadToUser', 'currentRecipient'])
                ->where(function($query) use ($user) {
                    $query->where('current_recipient_id', $user->id)
                          ->orWhere(function($subQuery) use ($user) {
                              $subQuery->where('do_approved_by', $user->name)
                                      ->whereNotNull('do_approved_by');
                          });
                })
                ->latest()
                ->get();
        } else {
            // For non-DO users: show only documents where they are the current recipient
            $documents = Document::with(['uploadByUser', 'uploadToUser', 'currentRecipient'])
                ->where('current_recipient_id', $user->id)
                ->latest()
                ->get();
        }

        return response()->json([
            'documents' => $documents
        ]);
    }

    public function outgoing()
    {
        $user = Auth::user();
        $userUnit = $user->unit->full_name ?? '';
        $isDO = str_ends_with($userUnit, '/DO');
        
        if ($isDO) {
            // For DO users: show documents they uploaded OR documents they have acted upon
            $documents = Document::with(['uploadByUser', 'uploadToUser', 'currentRecipient'])
                ->where(function($query) use ($user) {
                    $query->where('upload_by_user_id', $user->id)
                          ->orWhere(function($subQuery) use ($user) {
                              $subQuery->where('do_approved_by', $user->name)
                                      ->whereNotNull('do_approved_by');
                          });
                })
                ->latest()
                ->get();
        } else {
            // For non-DO users: show only documents they uploaded
            $documents = Document::with(['uploadByUser', 'uploadToUser', 'currentRecipient'])
                ->where('upload_by_user_id', $user->id)
                ->latest()
                ->get();
        }
            
        return response()->json([
            'documents' => $documents
        ]);
    }

    public function archived()
    {
        $user = Auth::user();
        
        // Get archived documents - documents that have been completed/archived
        $documents = Document::with(['uploadByUser', 'uploadToUser', 'currentRecipient'])
            ->where('status', 'archived')
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
        
        // Check if user can upload (must be encoder or admin)
        if (!in_array($user->role, ['encoder', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only encoders and admins can upload documents.'
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

        // Determine the initial recipient based on the forwarded unit's DO
        // Documents are always routed to the DO of the unit being forwarded to, not the originating unit
        $forwardedUnit = $uploadToUser->unit->full_name ?? '';
        $initialRecipient = $this->getInitialRecipient($forwardedUnit);
        
        // Check if we found a valid recipient
        if (!$initialRecipient) {
            return response()->json([
                'success' => false,
                'message' => 'Could not determine recipient for the forwarded unit. Please check unit configuration.'
            ], 400);
        }
        
        // Log for debugging
        Log::info('Document routing', [
            'forwarded_unit' => $forwardedUnit,
            'initial_recipient_id' => $initialRecipient ? $initialRecipient->id : 'null',
            'initial_recipient_name' => $initialRecipient ? $initialRecipient->name : 'null',
            'initial_recipient_unit' => $initialRecipient ? $initialRecipient->unit->full_name ?? 'null' : 'null'
        ]);

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
        
        // Check if user can perform actions on this document
        if (!$this->canUserPerformAction($document, 'receive')) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to receive this document. Only the DO of the forwarded unit can perform actions.'
            ], 403);
        }

        // Check if this is a DO user accepting the document
        $currentUserUnit = $user->unit->full_name ?? '';
        $isDO = str_ends_with($currentUserUnit, '/DO');

        if ($isDO) {
            // Update DO approval status and keep document in DO's received documents
            $document->update([
                'do_approval_status' => 'accepted',
                'do_approved_by' => $user->name,
                'do_approved_at' => now(),
                'status' => 'received',
                'current_recipient_id' => $user->id, // Keep it with the DO
            ]);

            // Also create a copy/forward to the intended recipient if different from DO
            $intendedRecipient = $document->uploadToUser;
            if ($intendedRecipient && $intendedRecipient->id !== $user->id) {
                // Create a new document entry for the intended recipient
                Document::create([
                    'tracking_code' => $document->tracking_code . '-COPY',
                    'document_type' => $document->document_type,
                    'subject' => $document->subject,
                    'document_date' => $document->document_date,
                    'entry_date' => $document->entry_date,
                    'upload_by' => $document->upload_by,
                    'upload_by_user_id' => $document->upload_by_user_id,
                    'upload_to' => $document->upload_to,
                    'upload_to_user_id' => $document->upload_to_user_id,
                    'originating_office' => $document->originating_office,
                    'forward_to_department' => $document->forward_to_department,
                    'origin_type' => $document->origin_type,
                    'priority' => $document->priority,
                    'remarks' => $document->remarks,
                    'routing' => $document->routing,
                    'file_path' => $document->file_path,
                    'file_name' => $document->file_name,
                    'status' => 'received',
                    'current_recipient_id' => $intendedRecipient->id,
                    'do_approval_status' => 'accepted',
                    'do_approved_by' => $user->name,
                    'do_approved_at' => now(),
                    'forward_notes' => 'Delivered to intended recipient after DO acceptance by ' . $user->name,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Document accepted and delivered to ' . $intendedRecipient->name . "'s received documents. Document also remains in your received documents."
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Document accepted successfully and remains in your received documents.'
            ]);
        } else {
            // Regular user receiving document (non-DO)
            $document->update([
                'status' => 'received',
                'current_recipient_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document received successfully.'
            ]);
        }
    }

    public function respond(Request $request, Document $document)
    {
        $user = Auth::user();
        
        // Check if user can perform actions on this document
        if (!$this->canUserPerformAction($document, 'respond')) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to respond to this document. Only the DO of the forwarded unit can perform actions.'
            ], 403);
        }

        $request->validate([
            'response_message' => 'required|string|max:1000',
        ]);

        // Update document with response
        $document->update([
            'response_message' => $request->response_message,
            'responded_by' => $user->name,
            'responded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Response sent successfully to ' . $document->uploadByUser->name . '.'
        ]);
    }

    public function forward(Request $request, Document $document)
    {
        $user = Auth::user();
        
        // Check if user can perform actions on this document
        if (!$this->canUserPerformAction($document, 'forward')) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to forward this document. Only the DO of the forwarded unit can perform actions.'
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

        // Check if this is a DO forwarding to the intended recipient
        $userUnit = $user->unit->full_name ?? '';
        $isDOForwarding = str_ends_with($userUnit, '/DO');
        
        if ($isDOForwarding && $forwardToUser->id === $document->upload_to_user_id) {
            // DO is forwarding to the intended recipient
            $document->update([
                'status' => 'forwarded',
                'current_recipient_id' => $forwardToUser->id,
                'forward_notes' => $request->forward_notes,
            ]);
        } else {
            // Regular forwarding - restrict to same department
            $userDepartment = explode('/', $userUnit)[0];
            $forwardToUserDepartment = explode('/', $forwardToUser->unit->full_name ?? '')[0];
            
            if ($userDepartment !== $forwardToUserDepartment) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only forward documents within your own department.'
                ], 403);
            }
            
            $document->update([
                'status' => 'forwarded',
                'current_recipient_id' => $forwardToUser->id,
                'forward_notes' => $request->forward_notes,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Document forwarded successfully.'
        ]);
    }

    public function reject(Document $document)
    {
        $user = Auth::user();
        
        // Check if user can perform actions on this document
        if (!$this->canUserPerformAction($document, 'reject')) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to reject this document. Only the DO of the forwarded unit can perform actions.'
            ], 403);
        }

        // Check if this is a DO user rejecting the document
        $currentUserUnit = $user->unit->full_name ?? '';
        $isDO = str_ends_with($currentUserUnit, '/DO');

        if ($isDO) {
            // Update DO approval status and keep document in DO's received documents
            $document->update([
                'do_approval_status' => 'rejected',
                'do_approved_by' => $user->name,
                'do_approved_at' => now(),
                'status' => 'received', // Keep as received so it shows in DO's received documents
                'current_recipient_id' => $user->id, // Keep it with the DO
            ]);
        } else {
            // Regular user rejecting document (non-DO)
            $document->update([
                'status' => 'rejected',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Document rejected successfully.'
        ]);
    }

    private function getInitialRecipient($forwardedUnit)
    {
        // Always route to the DO of the forwarded unit
        // Extract the department prefix (e.g., 'FD' from 'FD/DO', 'CPMSD' from 'CPMSD/DO')
        $departmentPrefix = explode('/', $forwardedUnit)[0];
        
        // Log the search process
        Log::info('Searching for initial recipient', [
            'forwarded_unit' => $forwardedUnit,
            'department_prefix' => $departmentPrefix
        ]);
        
        // Route to the DO of that department
        $doUser = User::whereHas('unit', function($query) use ($departmentPrefix) {
            $query->where('full_name', $departmentPrefix . '/DO');
        })->first();
        
        if ($doUser) {
            Log::info('Found DO user', [
                'user_id' => $doUser->id,
                'user_name' => $doUser->name,
                'user_unit' => $doUser->unit->full_name ?? 'No unit'
            ]);
            return $doUser;
        }
        
        // If no DO user found, try to find any user from that department
        $fallbackUser = User::whereHas('unit', function($query) use ($departmentPrefix) {
            $query->where('full_name', 'LIKE', $departmentPrefix . '%');
        })->first();
        
        if ($fallbackUser) {
            Log::info('Found fallback user', [
                'user_id' => $fallbackUser->id,
                'user_name' => $fallbackUser->name,
                'user_unit' => $fallbackUser->unit->full_name ?? 'No unit'
            ]);
            return $fallbackUser;
        }
        
        // Last resort: find any user with a similar name
        $lastResortUser = User::where('name', 'LIKE', '%' . $departmentPrefix . '%')->first();
        
        if ($lastResortUser) {
            Log::info('Found last resort user', [
                'user_id' => $lastResortUser->id,
                'user_name' => $lastResortUser->name,
                'user_unit' => $lastResortUser->unit->full_name ?? 'No unit'
            ]);
            return $lastResortUser;
        }
        
        Log::warning('No user found for forwarded unit', [
            'forwarded_unit' => $forwardedUnit,
            'department_prefix' => $departmentPrefix
        ]);
        
        return null;
    }

    private function getNextRecipient($document, $currentUser)
    {
        // Get the intended recipient from the document
        $intendedRecipient = $document->uploadToUser;
        
        // If the current user is a DO and they're accepting, forward to the intended recipient
        $currentUserUnit = $currentUser->unit->full_name ?? '';
        $isDO = str_ends_with($currentUserUnit, '/DO');
        
        if ($isDO && $intendedRecipient && $intendedRecipient->id !== $currentUser->id) {
            Log::info('DO accepting document, forwarding to intended recipient', [
                'current_user' => $currentUser->name,
                'current_user_unit' => $currentUserUnit,
                'intended_recipient' => $intendedRecipient->name,
                'intended_recipient_unit' => $intendedRecipient->unit->full_name ?? 'No unit'
            ]);
            return $intendedRecipient;
        }
        
        // If the current user is the intended recipient, no further forwarding needed
        if ($intendedRecipient && $intendedRecipient->id === $currentUser->id) {
            Log::info('Current user is the intended recipient, no further forwarding needed', [
                'current_user' => $currentUser->name,
                'intended_recipient' => $intendedRecipient->name
            ]);
            return null;
        }
        
        // Check if there's a specific routing path defined
        if ($document->routing && $document->routing !== 'internal') {
            // Parse routing information (e.g., "Department Head → Admin")
            $routingParts = explode('→', $document->routing);
            if (count($routingParts) > 1) {
                $nextStep = trim($routingParts[1]); // Get the next step in routing
                
                // Find user based on routing step
                $nextUser = User::where('name', 'LIKE', '%' . $nextStep . '%')
                    ->orWhereHas('unit', function($query) use ($nextStep) {
                        $query->where('full_name', 'LIKE', '%' . $nextStep . '%');
                    })
                    ->first();
                
                if ($nextUser && $nextUser->id !== $currentUser->id) {
                    Log::info('Found next recipient based on routing', [
                        'routing' => $document->routing,
                        'next_step' => $nextStep,
                        'next_user' => $nextUser->name
                    ]);
                    return $nextUser;
                }
            }
        }
        
        // Default: return the intended recipient if different from current user
        if ($intendedRecipient && $intendedRecipient->id !== $currentUser->id) {
            Log::info('Default forwarding to intended recipient', [
                'current_user' => $currentUser->name,
                'intended_recipient' => $intendedRecipient->name
            ]);
            return $intendedRecipient;
        }
        
        Log::info('No next recipient found', [
            'current_user' => $currentUser->name,
            'document_routing' => $document->routing,
            'intended_recipient' => $intendedRecipient ? $intendedRecipient->name : 'None'
        ]);
        
        return null;
    }

    public function canUserUpload()
    {
        $user = Auth::user();
        $userUnit = $user->unit->full_name ?? '';
        $canUpload = in_array($user->role, ['encoder', 'admin']);
        
        return response()->json([
            'can_upload' => $canUpload,
            'user_unit' => $userUnit,
            'user_role' => $user->role
        ]);
    }

    public function canUserPerformAction(Document $document, $action)
    {
        $user = Auth::user();
        
        // Only the current recipient can perform actions
        if ($document->current_recipient_id !== $user->id) {
            return false;
        }
        
        // Check if the user is from the DO of the forwarded unit (not originating unit)
        $userUnit = $user->unit->full_name ?? '';
        $forwardedUnit = $document->uploadToUser->unit->full_name ?? '';
        
        // Extract department prefixes
        $userDepartment = explode('/', $userUnit)[0];
        $forwardedDepartment = explode('/', $forwardedUnit)[0];
        
        // User must be from the DO of the forwarded unit
        $isDO = str_ends_with($userUnit, '/DO');
        $departmentsMatch = $userDepartment === $forwardedDepartment;
        
        // Log for debugging
        Log::info('canUserPerformAction check', [
            'user_id' => $user->id,
            'user_unit' => $userUnit,
            'forwarded_unit' => $forwardedUnit,
            'user_department' => $userDepartment,
            'forwarded_department' => $forwardedDepartment,
            'is_do' => $isDO,
            'departments_match' => $departmentsMatch,
            'can_perform' => $isDO && $departmentsMatch
        ]);
        
        // Simplified logic: If user is current recipient and is from a DO unit, allow actions
        // This ensures DO users can see and act on documents routed to them
        return $isDO;
    }

    public function debugUserDocuments()
    {
        $user = Auth::user();
        
        // Get all documents where user is current recipient
        $currentRecipientDocs = Document::with(['uploadByUser', 'uploadToUser', 'currentRecipient'])
            ->where('current_recipient_id', $user->id)
            ->get();
            
        // Get all documents where user is upload_to
        $uploadToDocs = Document::with(['uploadByUser', 'uploadToUser', 'currentRecipient'])
            ->where('upload_to_user_id', $user->id)
            ->get();
            
        // Get all documents where user uploaded
        $uploadedDocs = Document::with(['uploadByUser', 'uploadToUser', 'currentRecipient'])
            ->where('upload_by_user_id', $user->id)
            ->get();
        
        // Get all documents in the system for debugging
        $allDocs = Document::with(['uploadByUser', 'uploadToUser', 'currentRecipient'])
            ->latest()
            ->take(10)
            ->get();
        
        return response()->json([
            'user_info' => [
                'id' => $user->id,
                'name' => $user->name,
                'unit' => $user->unit ? $user->unit->full_name : 'No unit',
                'role' => $user->role
            ],
            'current_recipient_docs' => $currentRecipientDocs,
            'upload_to_docs' => $uploadToDocs,
            'uploaded_docs' => $uploadedDocs,
            'all_docs_sample' => $allDocs
        ]);
    }
}
