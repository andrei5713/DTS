<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_code',
        'document_type',
        'subject',
        'document_date',
        'entry_date',
        'upload_by',
        'upload_by_user_id',
        'upload_to',
        'upload_to_user_id',
        'originating_office',
        'forward_to_department',
        'origin_type',
        'priority',
        'remarks',
        'routing',
        'file_path',
        'file_name',
        'status',
        'current_recipient_id',
        'forward_notes',
    ];

    protected $casts = [
        'document_date' => 'date',
        'entry_date' => 'date',
    ];

    public function uploadByUser()
    {
        return $this->belongsTo(User::class, 'upload_by_user_id');
    }

    public function uploadToUser()
    {
        return $this->belongsTo(User::class, 'upload_to_user_id');
    }

    public function currentRecipient()
    {
        return $this->belongsTo(User::class, 'current_recipient_id');
    }

    public function canUpload()
    {
        // Check if user's unit ends with /DO
        $userUnit = $this->uploadByUser->unit->full_name ?? '';
        return str_ends_with($userUnit, '/DO');
    }

    public function getNextRecipient()
    {
        // Logic to determine who should receive the document next
        $targetUnit = $this->uploadToUser->unit->full_name ?? '';
        
        // If target is not a /DO unit, find the corresponding /DO unit
        if (!str_ends_with($targetUnit, '/DO')) {
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
        
        return $this->uploadToUser;
    }
}
