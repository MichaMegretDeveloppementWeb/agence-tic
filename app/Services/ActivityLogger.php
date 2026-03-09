<?php

namespace App\Services;

use App\Models\ActivityEntry;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    public function log(
        string $eventType,
        string $message,
        ?int $userId = null,
        ?Model $subject = null,
    ): ActivityEntry {
        return ActivityEntry::create([
            'user_id' => $userId,
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'subject_id' => $subject?->getKey(),
            'event_type' => $eventType,
            'message' => $message,
        ]);
    }
}
