<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Check if user is Administrator
     */
    public function isAdministrator(): bool
    {
        return $this->role === 'Administrator';
    }

    /**
     * Check if user is Operator
     */
    public function isOperator(): bool
    {
        return $this->role === 'Operator';
    }

    /**
     * Check if user is RW (Read/Write)
     */
    public function isRW(): bool
    {
        return $this->role === 'RW';
    }

    /**
     * Get all available roles
     */
    public static function getRoles(): array
    {
        return ['Administrator', 'Operator', 'RW'];
    }

    /**
     * Check if user has permission level
     */
    public function hasPermissionLevel(string $requiredRole): bool
    {
        $hierarchy = [
            'RW' => 1,
            'Operator' => 2,
            'Administrator' => 3
        ];

        return $hierarchy[$this->role] >= $hierarchy[$requiredRole];
    }

    /**
     * Get the company that owns the user.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Check if user requires a company (all except Administrator)
     */
    public function requiresCompany(): bool
    {
        return !$this->isAdministrator();
    }
}
