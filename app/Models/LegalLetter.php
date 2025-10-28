<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LegalLetter extends Model
{
    use HasFactory;

    protected $table = 'legal_letters';

    protected $fillable = [
        'title',
        'description',
        'created_by',
    ];

    /**
     * Get the user who created this request
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    /**
     * Get the companies associated with this legal letter request
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'legal_letter_company', 'legal_letter_id', 'company_id')
            ->withPivot(['status', 'notes', 'activated_at', 'deactivated_at', 'updated_by'])
            ->withTimestamps();
    }

    /**
     * Get only active companies for this request
     */
    public function activeCompanies()
    {
        return $this->companies()->wherePivot('status', 'active');
    }

    /**
     * Get only inactive companies for this request
     */
    public function inactiveCompanies()
    {
        return $this->companies()->wherePivot('status', 'inactive');
    }

    /**
     * Attach a company to this request with status
     */
    public function attachCompany($companyId, $status = 'active', $notes = null, $updatedBy = null)
    {
        $pivotData = [
            'status' => $status,
            'notes' => $notes,
            'updated_by' => $updatedBy,
        ];

        if ($status === 'active') {
            $pivotData['activated_at'] = now();
        } else {
            $pivotData['deactivated_at'] = now();
        }

        return $this->companies()->attach($companyId, $pivotData);
    }

    /**
     * Update company status for this request
     */
    public function updateCompanyStatus($companyId, $status, $notes = null, $updatedBy = null)
    {
        $pivotData = [
            'status' => $status,
            'notes' => $notes,
            'updated_by' => $updatedBy,
        ];

        if ($status === 'active') {
            $pivotData['activated_at'] = now();
            $pivotData['deactivated_at'] = null;
        } else {
            $pivotData['deactivated_at'] = now();
        }

        return $this->companies()->updateExistingPivot($companyId, $pivotData);
    }
}
