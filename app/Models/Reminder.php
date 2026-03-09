<?php

namespace App\Models;

use App\Enums\ReminderPriority;
use App\Enums\ReminderType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    /** @use HasFactory<\Database\Factories\ReminderFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'title',
        'content',
        'type',
        'created_by',
        'target_user_id',
        'is_completed',
        'due_date',
        'priority',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'type' => ReminderType::class,
            'priority' => ReminderPriority::class,
            'is_completed' => 'boolean',
            'due_date' => 'date',
        ];
    }

    /* ----------------------------------------------------------------
     |  Relations
     | ---------------------------------------------------------------- */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
