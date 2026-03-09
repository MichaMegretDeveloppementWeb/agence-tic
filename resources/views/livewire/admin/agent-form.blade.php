<div>
    @error('form-save-failed')
        <x-ui.alert type="error" dismissible class="mb-5">{{ $message }}</x-ui.alert>
    @enderror

    <form wire:submit="save" autocomplete="off">
        <div class="space-y-8">
            {{-- Informations de l'agent --}}
            <div>
                <div class="mb-4">
                    <h2 class="text-[13px] font-semibold text-gray-900">Informations de l'agent</h2>
                    <p class="mt-1 text-[13px] text-gray-500">Identité et coordonnées de l'agent.</p>
                </div>
                <x-ui.card>
                    <div class="grid grid-cols-1 gap-x-4 gap-y-5 sm:grid-cols-2">
                        {{-- Nom --}}
                        <div>
                            <label for="name" class="block text-[13px] font-medium text-gray-700 mb-1.5">Nom</label>
                            <input type="text" id="name" wire:model.blur="name" placeholder="Nom complet de l'agent"
                                class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('name') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset" />
                            @error('name')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-[13px] font-medium text-gray-700 mb-1.5">Adresse e-mail</label>
                            <input type="email" id="email" wire:model.blur="email" placeholder="agent@agence-tic.fr"
                                class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('email') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset" />
                            @error('email')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Code agent --}}
                        <div>
                            <label for="agentCode" class="block text-[13px] font-medium text-gray-700 mb-1.5">Code agent</label>
                            <input type="text" id="agentCode" wire:model.blur="agentCode" placeholder="TIC-001"
                                class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('agentCode') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset" />
                            @error('agentCode')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Mot de passe --}}
                        <div x-data="{ show: false }">
                            <label for="password" class="block text-[13px] font-medium text-gray-700 mb-1.5">
                                Mot de passe
                                @if($editMode)
                                    <span class="font-normal text-gray-400">(laisser vide pour ne pas modifier)</span>
                                @endif
                            </label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" id="password" wire:model.blur="password" autocomplete="new-password" placeholder="Minimum 8 caractères"
                                    class="block w-full rounded-lg border-0 py-2 pr-10 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('password') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset" />
                                <button type="button" @click="show = !show" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.64 0 8.577 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.64 0-8.577-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-ui.card>
            </div>

            {{-- Accréditation --}}
            <div>
                <div class="mb-4">
                    <h2 class="text-[13px] font-semibold text-gray-900">Accréditation</h2>
                    <p class="mt-1 text-[13px] text-gray-500">Niveau d'accès aux contenus classifiés (1 = minimal, 8 = maximal).</p>
                </div>
                <x-ui.card>
                    <div class="max-w-xs">
                        <label for="accreditationLevel" class="block text-[13px] font-medium text-gray-700 mb-1.5">Niveau d'accréditation</label>
                        <select id="accreditationLevel" wire:model.blur="accreditationLevel"
                            class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('accreditationLevel') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} focus:ring-2 focus:ring-inset">
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}">Niveau {{ $i }}</option>
                            @endfor
                        </select>
                        @error('accreditationLevel')
                            <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </x-ui.card>
            </div>

            {{-- Statut du compte (edit mode only) --}}
            @if($editMode)
                <div>
                    <div class="mb-4">
                        <h2 class="text-[13px] font-semibold text-gray-900">Statut du compte</h2>
                        <p class="mt-1 text-[13px] text-gray-500">Activer ou désactiver l'accès de l'agent à l'application.</p>
                    </div>
                    <x-ui.card>
                        <x-ui.toggle
                            label="Compte actif"
                            description="Un agent désactivé ne pourra plus se connecter ni accéder à l'application."
                            :checked="$isActive"
                            wire:click="$toggle('isActive')"
                        />
                    </x-ui.card>
                </div>
            @endif

            {{-- Boutons --}}
            <div class="flex items-center justify-end gap-x-3">
                <x-ui.button variant="ghost" :href="$editMode ? route('admin.agents.show', $agent) : route('admin.agents.index')">Annuler</x-ui.button>
                <x-ui.button type="submit" :loading="true" target="save">
                    {{ $editMode ? 'Enregistrer' : 'Créer l\'agent' }}
                </x-ui.button>
            </div>
        </div>
    </form>
</div>
