<div>
    @error('form-save-failed')
        <x-ui.alert type="error" dismissible class="mb-5">{{ $message }}</x-ui.alert>
    @enderror

    <form wire:submit="save">
        <div class="space-y-8">
            {{-- Identification du rapport --}}
            <div>
                <div class="mb-4">
                    <h2 class="text-[13px] font-semibold text-gray-900">Identification</h2>
                    <p class="mt-1 text-[13px] text-gray-500">Code unique et titre du rapport TIC.</p>
                </div>
                <x-ui.card>
                    <div class="grid grid-cols-1 gap-x-4 gap-y-5 sm:grid-cols-2">
                        {{-- Code --}}
                        <div>
                            <label for="code" class="block text-[13px] font-medium text-gray-700 mb-1.5">Code</label>
                            <input type="text" id="code" wire:model.blur="code" placeholder="RPT-001"
                                class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('code') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset" />
                            @error('code')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Titre --}}
                        <div>
                            <label for="title" class="block text-[13px] font-medium text-gray-700 mb-1.5">Titre</label>
                            <input type="text" id="title" wire:model.blur="title" placeholder="Titre du rapport"
                                class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('title') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset" />
                            @error('title')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-ui.card>
            </div>

            {{-- Classification --}}
            <div>
                <div class="mb-4">
                    <h2 class="text-[13px] font-semibold text-gray-900">Classification</h2>
                    <p class="mt-1 text-[13px] text-gray-500">Catégorie, niveau de menace et accréditation requise.</p>
                </div>
                <x-ui.card>
                    <div class="grid grid-cols-1 gap-x-4 gap-y-5 sm:grid-cols-2">
                        {{-- Catégorie --}}
                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Catégorie</label>
                            <x-combobox
                                :options="$categories->map(fn($c) => ['id' => $c->id, 'label' => $c->name])->toArray()"
                                wire-model="categoryId"
                                placeholder="Sélectionner une catégorie"
                                :has-error="$errors->has('categoryId')"
                            />
                            @error('categoryId')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Niveau de menace --}}
                        <div>
                            <label for="threatLevel" class="block text-[13px] font-medium text-gray-700 mb-1.5">Niveau de menace</label>
                            <select id="threatLevel" wire:model.blur="threatLevel"
                                class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('threatLevel') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} focus:ring-2 focus:ring-inset">
                                @foreach($threatLevels as $level)
                                    <option value="{{ $level->value }}">{{ $level->label() }}</option>
                                @endforeach
                            </select>
                            @error('threatLevel')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Niveau d'accréditation --}}
                        <div>
                            <label for="accreditationLevel" class="block text-[13px] font-medium text-gray-700 mb-1.5">Niveau d'accréditation requis</label>
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

                        {{-- Statut --}}
                        <div>
                            <label for="status" class="block text-[13px] font-medium text-gray-700 mb-1.5">Statut</label>
                            <select id="status" wire:model.blur="status"
                                class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('status') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} focus:ring-2 focus:ring-inset">
                                @foreach($statuses as $statusOption)
                                    <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-ui.card>
            </div>

            {{-- Contenu du rapport --}}
            <div>
                <div class="mb-4">
                    <h2 class="text-[13px] font-semibold text-gray-900">Contenu</h2>
                    <p class="mt-1 text-[13px] text-gray-500">Description détaillée, procédures et notes associées.</p>
                </div>
                <x-ui.card>
                    <div class="space-y-5">
                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-[13px] font-medium text-gray-700 mb-1.5">Description</label>
                            <textarea id="description" wire:model.blur="description" rows="4" placeholder="Description détaillée du rapport (minimum 20 caractères)"
                                class="block w-full rounded-lg border-0 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('description') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset"></textarea>
                            @error('description')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Procédures --}}
                        <div>
                            <label for="procedures" class="block text-[13px] font-medium text-gray-700 mb-1.5">Procédures</label>
                            <textarea id="procedures" wire:model.blur="procedures" rows="3" placeholder="Procédures à suivre (optionnel)"
                                class="block w-full rounded-lg border-0 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('procedures') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset"></textarea>
                            @error('procedures')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label for="notes" class="block text-[13px] font-medium text-gray-700 mb-1.5">Notes</label>
                            <textarea id="notes" wire:model.blur="notes" rows="3" placeholder="Notes complémentaires (optionnel)"
                                class="block w-full rounded-lg border-0 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('notes') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset"></textarea>
                            @error('notes')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-ui.card>
            </div>

            {{-- Boutons --}}
            <div class="flex items-center justify-end gap-x-3">
                <x-ui.button variant="ghost" :href="$editMode ? route('reports.show', $report) : route('reports.index')">Annuler</x-ui.button>
                <x-ui.button type="submit" :loading="true" target="save">
                    {{ $editMode ? 'Enregistrer' : 'Créer le rapport' }}
                </x-ui.button>
            </div>
        </div>
    </form>
</div>
