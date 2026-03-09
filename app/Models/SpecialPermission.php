<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SpecialPermission extends Model
{
    /** @use HasFactory<\Database\Factories\SpecialPermissionFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'permissionable_type',
        'permissionable_id',
        'granted_by',
    ];

    /* ----------------------------------------------------------------
     |  Relations
     | ---------------------------------------------------------------- */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function permissionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}
