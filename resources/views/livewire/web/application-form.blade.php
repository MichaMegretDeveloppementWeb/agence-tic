<div>
    @if($submitted)
        {{-- Confirmation --}}
        <x-ui.card>
            <div class="text-center py-6">
                <div class="mx-auto mb-4 flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50">
                    <x-ui.icon name="check-circle" class="h-5 w-5 text-emerald-600" />
                </div>
                <h3 class="text-[13px] font-semibold text-gray-900">Candidature envoyée</h3>
                <p class="mt-2 text-[13px] text-gray-600">
                    Votre candidature a bien été transmise au Directeur G.
                    Vous serez contacté à l'adresse indiquée si votre profil est retenu.
                </p>

                @if($trackingCode)
                    <div class="mt-4 rounded-lg bg-gray-50 px-4 py-3">
                        <p class="text-[12px] text-gray-500">Votre code de suivi</p>
                        <p class="mt-1 text-lg font-semibold tracking-wider text-gray-900">{{ $trackingCode }}</p>
                        <p class="mt-1 text-[12px] text-gray-400">Conservez ce code pour suivre l'état de votre candidature.</p>
                    </div>
                @endif

                <div class="mt-6 flex items-center justify-center gap-x-3">
                    <x-ui.button variant="secondary" href="{{ route('home') }}">
                        Retour à l'accueil
                    </x-ui.button>
                    <x-ui.button href="{{ route('recruitment.tracking') }}">
                        Suivre ma candidature
                    </x-ui.button>
                </div>
            </div>
        </x-ui.card>
    @else
        {{-- Formulaire --}}
        @error('application-submit-failed')
            <x-ui.alert type="error" dismissible class="mb-5">{{ $message }}</x-ui.alert>
        @enderror

        @error('application-submit-throttled')
            <x-ui.alert type="warning" dismissible class="mb-5">{{ $message }}</x-ui.alert>
        @enderror

        <x-ui.card>
            <form wire:submit="submit" class="space-y-5">

                {{-- Nom / Pseudonyme --}}
                <div>
                    <label for="name" class="block text-[13px] font-medium text-gray-700 mb-1.5">Nom ou pseudonyme</label>
                    <input
                        type="text"
                        id="name"
                        wire:model.blur="name"
                        placeholder="Votre nom ou pseudonyme"
                        class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('name') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset"
                    />
                    @error('name')
                        <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-[13px] font-medium text-gray-700 mb-1.5">Adresse e-mail</label>
                    <input
                        type="email"
                        id="email"
                        wire:model.blur="email"
                        placeholder="votre@email.com"
                        class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('email') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset"
                    />
                    @error('email')
                        <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Motivation --}}
                <div>
                    <label for="motivation" class="block text-[13px] font-medium text-gray-700 mb-1.5">Motivation</label>
                    <textarea
                        id="motivation"
                        wire:model.blur="motivation"
                        rows="5"
                        placeholder="Expliquez pourquoi vous souhaitez rejoindre l'Agence TIC (50 caractères minimum)..."
                        class="block w-full rounded-lg border-0 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('motivation') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset"
                    ></textarea>
                    @error('motivation')
                        <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-[12px] text-gray-400">{{ strlen($motivation) }} / 2000 caractères</p>
                </div>

                {{-- Expérience --}}
                <div>
                    <label for="experience" class="block text-[13px] font-medium text-gray-700 mb-1.5">
                        Expérience ou commentaire libre
                        <span class="font-normal text-gray-400">(optionnel)</span>
                    </label>
                    <textarea
                        id="experience"
                        wire:model.blur="experience"
                        rows="3"
                        placeholder="Compétences, expériences passées, informations complémentaires..."
                        class="block w-full rounded-lg border-0 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('experience') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset"
                    ></textarea>
                    @error('experience')
                        <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bouton --}}
                <div class="flex items-center justify-end gap-x-3 pt-2">
                    <x-ui.button variant="ghost" href="{{ route('home') }}">Annuler</x-ui.button>
                    <x-ui.button type="submit" :loading="true" target="submit">Envoyer la candidature</x-ui.button>
                </div>

            </form>
        </x-ui.card>
    @endif
</div>
