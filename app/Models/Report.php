<?php

namespace App\Models;

use App\Enums\ReportStatus;
use App\Enums\ThreatLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Report extends Model
{
    /** @use HasFactory<\Database\Factories\ReportFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'code',
        'title',
        'category_id',
        'threat_level',
        'accreditation_level',
        'description',
        'procedures',
        'notes',
        'status',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'threat_level' => ThreatLevel::class,
            'accreditation_level' => 'integer',
            'status' => ReportStatus::class,
        ];
    }

    /* ----------------------------------------------------------------
     |  Relations
     | ---------------------------------------------------------------- */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function specialPermissions(): MorphMany
    {
        return $this->morphMany(SpecialPermission::class, 'permissionable');
    }

    public function activityEntries(): MorphMany
    {
        return $this->morphMany(ActivityEntry::class, 'subject');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ReportComment::class);
    }
}
