<div>
    @error('form-save-failed')
        <x-ui.alert type="error" dismissible class="mb-5">{{ $message }}</x-ui.alert>
    @enderror

    <form wire:submit="save">
        <div class="space-y-8">
            {{-- Informations du rappel --}}
            <div>
                <div class="mb-4">
                    <h2 class="text-[13px] font-semibold text-gray-900">Informations</h2>
                    <p class="mt-1 text-[13px] text-gray-500">Titre et détails du rappel.</p>
                </div>
                <x-ui.card>
                    <div class="grid grid-cols-1 gap-x-4 gap-y-5">
                        {{-- Titre --}}
                        <div>
                            <label for="reminder-title" class="block text-[13px] font-medium text-gray-700 mb-1.5">Titre</label>
                            <input
                                type="text"
                                id="reminder-title"
                                wire:model.blur="title"
                                placeholder="Titre du rappel"
                                class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('title') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset"
                            />
                            @error('title')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Contenu --}}
                        <div>
                            <label for="reminder-content" class="block text-[13px] font-medium text-gray-700 mb-1.5">
                                Contenu
                                <span class="font-normal text-gray-400">(optionnel)</span>
                            </label>
                            <textarea
                                id="reminder-content"
                                wire:model.blur="content"
                                rows="3"
                                placeholder="Détails du rappel..."
                                class="block w-full rounded-lg border-0 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('content') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset"
                            ></textarea>
                            @error('content')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Date d'échéance --}}
                        <div class="sm:max-w-xs">
                            <label for="reminder-due-date" class="block text-[13px] font-medium text-gray-700 mb-1.5">
                                Date d'échéance
                                <span class="font-normal text-gray-400">(optionnel)</span>
                            </label>
                            <input
                                type="date"
                                id="reminder-due-date"
                                wire:model.blur="dueDate"
                                class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('dueDate') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} focus:ring-2 focus:ring-inset"
                            />
                            @error('dueDate')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Priorité --}}
                        <div class="sm:max-w-xs">
                            <label for="reminder-priority" class="block text-[13px] font-medium text-gray-700 mb-1.5">Priorité</label>
                            <select
                                id="reminder-priority"
                                wire:model.blur="priority"
                                class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('priority') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} focus:ring-2 focus:ring-inset"
                            >
                                @foreach($priorities as $p)
                                    <option value="{{ $p->value }}">{{ $p->label() }}</option>
                                @endforeach
                            </select>
                            @error('priority')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-ui.card>
            </div>

            {{-- Type de rappel (Directeur G uniquement) --}}
            @if($isDirector)
                <div>
                    <div class="mb-4">
                        <h2 class="text-[13px] font-semibold text-gray-900">Type de rappel</h2>
                        <p class="mt-1 text-[13px] text-gray-500">Choisissez la portée du rappel.</p>
                    </div>
                    <x-ui.card>
                        <div class="grid grid-cols-1 gap-x-4 gap-y-5">
                            {{-- Type --}}
                            <div>
                                <label for="reminder-type" class="block text-[13px] font-medium text-gray-700 mb-1.5">Type</label>
                                <select
                                    id="reminder-type"
                                    wire:model.live="type"
                                    class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('type') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} focus:ring-2 focus:ring-inset"
                                >
                                    @foreach($reminderTypes as $reminderType)
                                        <option value="{{ $reminderType->value }}">{{ $reminderType->label() }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Agent ciblé (affiché uniquement pour type "targeted") --}}
                            @if($type === 'targeted')
                                <div>
                                    <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Agent ciblé</label>
                                    <x-combobox
                                        :options="$agents->map(fn($a) => ['id' => $a->id, 'label' => $a->agent_code . ' — ' . $a->name])->toArray()"
                                        wire-model="targetUserId"
                                        placeholder="Sélectionner un agent"
                                        :has-error="$errors->has('targetUserId')"
                                    />
                                    @error('targetUserId')
                                        <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </x-ui.card>
                </div>
            @endif

            {{-- Boutons --}}
            <div class="flex items-center justify-end gap-x-3">
                <x-ui.button variant="ghost" :href="route('reminders.index')">Annuler</x-ui.button>
                <x-ui.button type="submit" :loading="true" target="save">Créer le rappel</x-ui.button>
            </div>
        </div>
    </form>
</div>
