<div>
    @error('profile-save-failed')
        <x-ui.alert type="error" dismissible class="mb-5">{{ $message }}</x-ui.alert>
    @enderror

    <form wire:submit="save" autocomplete="off">
        <div class="grid grid-cols-1 gap-x-4 gap-y-5 sm:grid-cols-2">
            {{-- Nom --}}
            <div>
                <label for="profile-name" class="block text-[13px] font-medium text-gray-700 mb-1.5">Nom</label>
                <input type="text" id="profile-name" wire:model.blur="name" placeholder="Votre nom complet"
                    class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('name') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset" />
                @error('name')
                    <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="profile-email" class="block text-[13px] font-medium text-gray-700 mb-1.5">Adresse e-mail</label>
                <input type="email" id="profile-email" wire:model.blur="email" placeholder="votre@email.fr"
                    class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('email') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset" />
                @error('email')
                    <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Code agent (lecture seule) --}}
            <div>
                <label for="profile-agent-code" class="block text-[13px] font-medium text-gray-700 mb-1.5">Code agent</label>
                <input type="text" id="profile-agent-code" value="{{ $user->agent_code }}" disabled
                    class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-400 bg-gray-50 ring-1 ring-inset ring-gray-200 cursor-not-allowed" />
                <p class="mt-1.5 text-[12px] text-gray-400">Ce champ ne peut pas être modifié.</p>
            </div>

            {{-- Niveau d'accréditation (lecture seule) --}}
            <div>
                <label for="profile-accreditation" class="block text-[13px] font-medium text-gray-700 mb-1.5">Niveau d'accréditation</label>
                <input type="text" id="profile-accreditation" value="Niveau {{ $user->accreditation_level }}" disabled
                    class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-400 bg-gray-50 ring-1 ring-inset ring-gray-200 cursor-not-allowed" />
                <p class="mt-1.5 text-[12px] text-gray-400">Seul le Directeur G peut modifier votre niveau.</p>
            </div>
        </div>

        {{-- Bouton --}}
        <div class="mt-5 flex items-center justify-end">
            <x-ui.button type="submit" :loading="true" target="save">Enregistrer</x-ui.button>
        </div>
    </form>
</div>
