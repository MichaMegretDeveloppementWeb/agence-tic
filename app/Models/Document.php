<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Document extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'title',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'category_id',
        'accreditation_level',
        'report_id',
        'uploaded_by',
        'status',
        'notes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'accreditation_level' => 'integer',
            'status' => DocumentStatus::class,
        ];
    }

    /* ----------------------------------------------------------------
     |  Relations
     | ---------------------------------------------------------------- */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function specialPermissions(): MorphMany
    {
        return $this->morphMany(SpecialPermission::class, 'permissionable');
    }
}
