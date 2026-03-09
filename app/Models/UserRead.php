<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserRead extends Model
{
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'readable_type',
        'readable_id',
        'read_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function readable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Marque un contenu comme lu pour un utilisateur.
     */
    public static function markAsRead(int $userId, string $readableType, int $readableId): void
    {
        static::updateOrCreate(
            [
                'user_id' => $userId,
                'readable_type' => $readableType,
                'readable_id' => $readableId,
            ],
            ['read_at' => now()],
        );
    }

    /**
     * Retourne les IDs lus par un utilisateur pour un type donné.
     *
     * @return \Illuminate\Support\Collection<int, int>
     */
    public static function readIdsFor(int $userId, string $readableType, array $readableIds = []): \Illuminate\Support\Collection
    {
        return static::query()
            ->where('user_id', $userId)
            ->where('readable_type', $readableType)
            ->when($readableIds, fn ($q) => $q->whereIn('readable_id', $readableIds))
            ->pluck('readable_id');
    }
}
