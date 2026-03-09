<div>
    @error('avatar-save-failed')
        <x-ui.alert type="error" dismissible class="mb-5">{{ $message }}</x-ui.alert>
    @enderror

    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
        {{-- Avatar actuel ou aperçu --}}
        <div class="shrink-0">
            @if($photo)
                <img src="{{ $photo->temporaryUrl() }}" alt="Aperçu" class="h-16 w-16 rounded-full object-cover ring-2 ring-gray-200" />
            @elseif($user->avatar_path)
                <x-ui.avatar :src="Storage::disk('public')->url($user->avatar_path)" size="xl" />
            @else
                <x-ui.avatar :initials="strtoupper(substr($user->name, 0, 2))" size="xl" color="gray" />
            @endif
        </div>

        {{-- Zone de téléversement --}}
        <div class="flex-1">
            <div class="flex items-center gap-x-3">
                <label for="avatar-upload"
                    class="inline-flex cursor-pointer items-center gap-x-1.5 rounded-lg bg-white px-3 py-1.5 text-[13px] font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <x-ui.icon name="photo" class="h-4 w-4 text-gray-400" />
                    Choisir une photo
                </label>
                <input type="file" id="avatar-upload" wire:model="photo" accept="image/*" class="sr-only" />

                @if($photo)
                    <x-ui.button wire:click="save" :loading="true" target="save" size="compact">Enregistrer</x-ui.button>
                    <button type="button" wire:click="$set('photo', null)" class="text-[13px] text-gray-400 hover:text-gray-600">Annuler</button>
                @elseif($user->avatar_path)
                    <x-ui.button variant="ghost" size="compact" wire:click="remove" wire:confirm="Êtes-vous sûr de vouloir supprimer votre photo de profil ?">
                        <x-ui.icon name="trash" class="h-4 w-4 text-red-500" />
                        Supprimer
                    </x-ui.button>
                @endif
            </div>

            <p class="mt-2 text-[12px] text-gray-400">PNG, JPG ou GIF — max 2 Mo.</p>

            @error('photo')
                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
            @enderror

            {{-- Loading indicator during upload --}}
            <div wire:loading wire:target="photo" class="mt-2">
                <p class="text-[12px] text-gray-500">Téléversement en cours...</p>
            </div>
        </div>
    </div>
</div>
