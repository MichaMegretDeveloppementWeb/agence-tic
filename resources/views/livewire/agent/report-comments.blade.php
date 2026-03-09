<div>
    <h2 class="text-[13px] font-semibold text-gray-900 mb-4">Discussion ({{ $comments->count() }})</h2>

    {{-- Comment form --}}
    <form wire:submit="addComment" class="mb-6">
        <div>
            <textarea
                wire:model.blur="newComment"
                rows="3"
                placeholder="Ajouter un commentaire..."
                class="block w-full rounded-lg border-0 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('newComment') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset"
            ></textarea>
            @error('newComment')
                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
            @enderror
            @error('comment-creation-failed')
                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="mt-3 flex justify-end">
            <x-ui.button type="submit" :loading="true" target="addComment" size="compact">
                Commenter
            </x-ui.button>
        </div>
    </form>

    {{-- Comments list --}}
    @if($comments->isEmpty())
        <p class="text-[13px] text-gray-400">Aucun commentaire pour le moment.</p>
    @else
        <div class="space-y-4">
            @foreach($comments as $comment)
                <div class="flex gap-x-3" wire:key="comment-{{ $comment->id }}">
                    @if($comment->user?->avatar_path)
                        <x-ui.avatar :src="Storage::disk('public')->url($comment->user->avatar_path)" size="sm" />
                    @else
                        <x-ui.avatar :initials="strtoupper(substr($comment->user?->name ?? 'A', 0, 2))" size="sm" color="gray" />
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-x-2">
                            <span class="text-[13px] font-medium text-gray-900">{{ $comment->user?->name ?? '—' }}</span>
                            <span class="text-[11px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="mt-1 text-[13px] text-gray-600 whitespace-pre-line">{{ $comment->content }}</div>
                        @if($comment->user_id === auth()->id() || auth()->user()->isDirectorG())
                            <button
                                wire:click="deleteComment({{ $comment->id }})"
                                wire:confirm="Êtes-vous sûr de vouloir supprimer ce commentaire ?"
                                class="mt-1 text-[11px] text-red-500 hover:text-red-700"
                            >Supprimer</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @error('comment-deletion-failed')
        <p class="mt-3 text-[12px] text-red-500">{{ $message }}</p>
    @enderror
</div>
