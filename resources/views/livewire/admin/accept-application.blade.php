<div>
    @error('application-accept-failed')
        <x-ui.alert type="error" dismissible class="mb-4">{{ $message }}</x-ui.alert>
    @enderror

    <form wire:submit="accept" autocomplete="off">
        <div class="space-y-5">
            {{-- Informations du candidat (lecture seule) --}}
            <div class="rounded-lg bg-gray-50 px-4 py-3">
                <p class="text-[12px] font-medium text-gray-400 uppercase tracking-wider mb-1">Candidat</p>
                <p class="text-[13px] font-medium text-gray-900">{{ $application->name }}</p>
                <p class="text-[12px] text-gray-500">{{ $application->email }}</p>
            </div>

            {{-- Code agent --}}
            <div>
                <label for="agentCode" class="block text-[13px] font-medium text-gray-700 mb-1.5">Code agent</label>
                <input type="text" id="agentCode" wire:model.blur="agentCode" placeholder="Ex: AGT-042" class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('agentCode') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset" />
                @error('agentCode')
                    <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Mot de passe --}}
            <div x-data="{ show: false }">
                <label for="password" class="block text-[13px] font-medium text-gray-700 mb-1.5">Mot de passe</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" id="password" wire:model.blur="password" autocomplete="new-password" class="block w-full rounded-lg border-0 py-2 pr-10 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('password') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset" />
                    <button type="button" @click="show = !show" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.64 0 8.577 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.64 0-8.577-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Niveau d'accréditation --}}
            <div>
                <label for="accreditationLevel" class="block text-[13px] font-medium text-gray-700 mb-1.5">Niveau d'accréditation</label>
                <select id="accreditationLevel" wire:model.blur="accreditationLevel" class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('accreditationLevel') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} focus:ring-2 focus:ring-inset">
                    @for($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}">Niveau {{ $i }}</option>
                    @endfor
                </select>
                @error('accreditationLevel')
                    <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-3">
            <x-ui.button variant="ghost" type="button" @click="$dispatch('close-modal', 'accept-application')">Annuler</x-ui.button>
            <x-ui.button type="submit" :loading="true" target="accept">Créer l'agent</x-ui.button>
        </div>
    </form>
</div>
