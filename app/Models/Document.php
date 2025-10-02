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
        'forwarded_by',
        'forwarded_by_user_id',
        'originating_office',
        'forward_to_department',
        'origin_type',
        'priority',
        'remarks',
        'routing',
        'file_path',
        'file_name',
        'status',
        'rejection_reason',
        'current_recipient_id',
        'forward_notes',
        'complied_by',
        'complied_at',
        'compliance_notes',
        'archived_by',
        'archived_at',
        'archive_notes',
        'unarchived_by',
        'unarchived_at',
        'unarchive_notes',
        'accepted_by_do_id',
        'accepted_by_do_at',
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

    public function acceptedByDo()
    {
        return $this->belongsTo(User::class, 'accepted_by_do_id');
    }


    public function forwardedByUser()
    {
        return $this->belongsTo(User::class, 'forwarded_by_user_id');
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

    public function getOriginatingUnit()
    {
        // Get the originating unit from the uploader
        return $this->uploadByUser->unit->full_name ?? '';
    }

    public function getForwardedUnit()
    {
        // Get the unit that the document is being forwarded to
        return $this->uploadToUser->unit->full_name ?? '';
    }

    public function isOriginatingUnitDO()
    {
        // Check if the originating unit is a DO
        $originatingUnit = $this->getOriginatingUnit();
        return str_ends_with($originatingUnit, '/DO');
    }

    public function isForwardedUnitDO()
    {
        // Check if the forwarded unit is a DO
        $forwardedUnit = $this->getForwardedUnit();
        return str_ends_with($forwardedUnit, '/DO');
    }
}
