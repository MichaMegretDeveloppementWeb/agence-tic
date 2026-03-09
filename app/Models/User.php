<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /** @var list<string> */
    protected $fillable = [
        'agent_code',
        'name',
        'email',
        'password',
        'role',
        'accreditation_level',
        'is_active',
        'avatar_path',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'accreditation_level' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /* ----------------------------------------------------------------
     |  Accessors
     | ---------------------------------------------------------------- */

    public function isDirectorG(): bool
    {
        return $this->role === UserRole::DirectorG;
    }

    public function isAgent(): bool
    {
        return $this->role === UserRole::Agent;
    }

    /**
     * Vérifie si l'utilisateur peut accéder à un contenu protégé.
     * Règle : niveau suffisant OU permission spéciale OU Directeur G.
     */
    public function canAccess(int $requiredLevel, ?string $permissionableType = null, ?int $permissionableId = null): bool
    {
        if ($this->isDirectorG()) {
            return true;
        }

        if ($this->accreditation_level >= $requiredLevel) {
            return true;
        }

        if ($permissionableType && $permissionableId) {
            return $this->specialPermissions()
                ->where('permissionable_type', $permissionableType)
                ->where('permissionable_id', $permissionableId)
                ->exists();
        }

        return false;
    }

    /* ----------------------------------------------------------------
     |  Relations
     | ---------------------------------------------------------------- */

    public function specialPermissions(): HasMany
    {
        return $this->hasMany(SpecialPermission::class);
    }

    public function createdReminders(): HasMany
    {
        return $this->hasMany(Reminder::class, 'created_by');
    }

    public function targetedReminders(): HasMany
    {
        return $this->hasMany(Reminder::class, 'target_user_id');
    }

    public function uploadedDocuments(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function grantedPermissions(): HasMany
    {
        return $this->hasMany(SpecialPermission::class, 'granted_by');
    }

    public function activityEntries(): HasMany
    {
        return $this->hasMany(ActivityEntry::class);
    }

    public function reportComments(): HasMany
    {
        return $this->hasMany(ReportComment::class);
    }
}
