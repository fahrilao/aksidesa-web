<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestLegalLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'name',
        'nik',
        'description',
        'ktp_image_path',
        'kk_image_path',
        'status',
        'requested_by',
        'assigned_company_id',
        'legal_letter_id',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    protected $attributes = [
        'status' => 'Waiting',
    ];

    protected $appends = [
        'ktp_image_url',
        'kk_image_url',
    ];

    /**
     * Get the user who requested this legal letter
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the company assigned to handle this request
     */
    public function assignedCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'assigned_company_id');
    }

    /**
     * Get the associated legal letter (if created)
     */
    public function legalLetter(): BelongsTo
    {
        return $this->belongsTo(LegalLetter::class, 'legal_letter_id');
    }

    /**
     * Get the legal letter template that matches the requested title
     */
    public function requestedLegalLetter(): BelongsTo
    {
        return $this->belongsTo(LegalLetter::class, 'title', 'title');
    }

    /**
     * Scope for waiting requests
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', 'Waiting');
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope for processing requests
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'Processing');
    }

    /**
     * Scope for completed requests
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    /**
     * Scope for requests assigned to a specific company
     */
    public function scopeAssignedToCompany($query, $companyId)
    {
        return $query->where('assigned_company_id', $companyId);
    }

    /**
     * Scope for requests made by a specific user
     */
    public function scopeRequestedBy($query, $userId)
    {
        return $query->where('requested_by', $userId);
    }

    /**
     * Get the full URL for KTP image
     */
    public function getKtpImageUrlAttribute(): ?string
    {
        return $this->ktp_image_path ? asset('storage/' . $this->ktp_image_path) : null;
    }

    /**
     * Get the full URL for KK image
     */
    public function getKkImageUrlAttribute(): ?string
    {
        return $this->kk_image_path ? asset('storage/' . $this->kk_image_path) : null;
    }

    /**
     * Check if request has KTP image
     */
    public function hasKtpImage(): bool
    {
        return !empty($this->ktp_image_path);
    }

    /**
     * Check if request has KK image
     */
    public function hasKkImage(): bool
    {
        return !empty($this->kk_image_path);
    }

    /**
     * Check if request has all required documents
     */
    public function hasAllDocuments(): bool
    {
        return $this->hasKtpImage() && $this->hasKkImage();
    }
}
