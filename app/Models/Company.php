<?php

namespace App\Models;

use App\Models\LegalLetter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'website',
        'description',
        'is_active',
        'api_key',
        'api_key_created_at',
        'api_key_last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'api_key_created_at' => 'datetime',
        'api_key_last_used_at' => 'datetime',
    ];

    /**
     * Get the users for the company.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope a query to only include active companies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the company's operators.
     */
    public function operators()
    {
        return $this->users()->where('role', 'Operator');
    }

    /**
     * Get the company's RW users.
     */
    public function rwUsers()
    {
        return $this->users()->where('role', 'RW');
    }

    /**
     * Get the legal letter requests associated with this company
     */
    public function legalLetters()
    {
        return $this->belongsToMany(LegalLetter::class, 'legal_letter_company', 'company_id', 'request_legal_letter_id')
            ->withPivot(['status', 'notes', 'activated_at', 'deactivated_at', 'updated_by'])
            ->withTimestamps();
    }

    /**
     * Get only active legal letter requests for this company
     */
    public function activeLegalLetters()
    {
        return $this->legalLetters()->wherePivot('status', 'active');
    }

    /**
     * Get only inactive legal letter requests for this company
     */
    public function inactiveLegalLetters()
    {
        return $this->legalLetters()->wherePivot('status', 'inactive');
    }

    /**
     * Generate a new API key for the company
     */
    public function generateApiKey(): string
    {
        $apiKey = 'ck_' . bin2hex(random_bytes(30)); // 60 characters + prefix
        
        $this->update([
            'api_key' => $apiKey,
            'api_key_created_at' => now(),
            'api_key_last_used_at' => null,
        ]);

        return $apiKey;
    }

    /**
     * Revoke the API key
     */
    public function revokeApiKey(): void
    {
        $this->update([
            'api_key' => null,
            'api_key_created_at' => null,
            'api_key_last_used_at' => null,
        ]);
    }

    /**
     * Update the last used timestamp for API key
     */
    public function updateApiKeyLastUsed(): void
    {
        $this->update([
            'api_key_last_used_at' => now(),
        ]);
    }

    /**
     * Check if company has an active API key
     */
    public function hasApiKey(): bool
    {
        return !empty($this->api_key);
    }

    /**
     * Find company by API key
     */
    public static function findByApiKey(string $apiKey): ?self
    {
        return static::where('api_key', $apiKey)->where('is_active', true)->first();
    }
}
