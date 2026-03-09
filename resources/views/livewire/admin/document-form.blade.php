<div>
    @error('form-save-failed')
        <x-ui.alert type="error" dismissible class="mb-5">{{ $message }}</x-ui.alert>
    @enderror

    <form wire:submit="save">
        <div class="space-y-8">
            {{-- Informations du document --}}
            <div>
                <div class="mb-4">
                    <h2 class="text-[13px] font-semibold text-gray-900">Informations du document</h2>
                    <p class="mt-1 text-[13px] text-gray-500">Titre et classification du document.</p>
                </div>
                <x-ui.card>
                    <div class="grid grid-cols-1 gap-x-4 gap-y-5 sm:grid-cols-2">
                        {{-- Titre --}}
                        <div class="sm:col-span-2">
                            <label for="title" class="block text-[13px] font-medium text-gray-700 mb-1.5">Titre</label>
                            <input type="text" id="title" wire:model.blur="title" placeholder="Titre du document"
                                class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('title') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset" />
                            @error('title')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

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

                        {{-- Rapport associé --}}
                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">
                                Rapport associé
                                <span class="font-normal text-gray-400">(optionnel)</span>
                            </label>
                            <x-combobox
                                :options="$reports->map(fn($r) => ['id' => $r->id, 'label' => $r->code . ' — ' . $r->title])->toArray()"
                                wire-model="reportId"
                                placeholder="Aucun rapport"
                                :has-error="$errors->has('reportId')"
                                :nullable="true"
                            />
                            @error('reportId')
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

            {{-- Fichier --}}
            <div>
                <div class="mb-4">
                    <h2 class="text-[13px] font-semibold text-gray-900">Fichier</h2>
                    <p class="mt-1 text-[13px] text-gray-500">
                        @if($editMode)
                            Téléversez un nouveau fichier pour remplacer l'existant, ou laissez vide pour conserver le fichier actuel.
                        @else
                            Fichier à associer au document (max. 10 Mo).
                        @endif
                    </p>
                </div>
                <x-ui.card>
                    <div>
                        @if($editMode && $document->file_name)
                            <div class="mb-4 flex items-center gap-x-3 rounded-lg bg-gray-50 px-4 py-3">
                                <x-ui.icon name="document-duplicate" class="h-5 w-5 text-gray-400" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[13px] font-medium text-gray-900">{{ $document->file_name }}</p>
                                    <p class="text-[12px] text-gray-400">{{ number_format($document->file_size / 1024, 1) }} Ko</p>
                                </div>
                            </div>
                        @endif

                        <label class="block text-[13px] font-medium text-gray-700 mb-1.5">
                            {{ $editMode ? 'Nouveau fichier' : 'Fichier' }}
                            @if($editMode)
                                <span class="font-normal text-gray-400">(optionnel)</span>
                            @endif
                        </label>
                        <div x-data="{ dragging: false, fileName: null }"
                             @dragover.prevent="dragging = true"
                             @dragleave.prevent="dragging = false"
                             @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                             :class="dragging ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'"
                             class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed px-6 py-8 transition-colors cursor-pointer"
                             @click="$refs.fileInput.click()">
                            <x-ui.icon name="arrow-down-tray" class="h-10 w-10 text-gray-300" />
                            <p class="mt-2 text-[13px] text-gray-600"><span class="font-medium text-indigo-600">Cliquez pour sélectionner</span> ou glissez-déposez</p>
                            <p class="mt-1 text-[12px] text-gray-400">PDF, DOC, etc. — max 10 Mo</p>
                            <input x-ref="fileInput" type="file" wire:model="file" class="hidden" />
                        </div>
                        @if($file)
                            <div class="mt-3 flex items-center gap-x-2 rounded-lg bg-emerald-50 px-3 py-2">
                                <x-ui.icon name="check-circle" class="h-4 w-4 text-emerald-500" />
                                <span class="text-[13px] text-emerald-700">{{ $file->getClientOriginalName() }}</span>
                            </div>
                        @endif
                        @error('file')
                            <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </x-ui.card>
            </div>

            {{-- Notes --}}
            <div>
                <div class="mb-4">
                    <h2 class="text-[13px] font-semibold text-gray-900">Notes</h2>
                    <p class="mt-1 text-[13px] text-gray-500">Annotations complémentaires sur le document.</p>
                </div>
                <x-ui.card>
                    <div>
                        <label for="notes" class="block text-[13px] font-medium text-gray-700 mb-1.5">
                            Notes <span class="font-normal text-gray-400">(optionnel)</span>
                        </label>
                        <textarea id="notes" wire:model.blur="notes" rows="3" placeholder="Notes complémentaires..."
                            class="block w-full rounded-lg border-0 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('notes') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset"></textarea>
                        @error('notes')
                            <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </x-ui.card>
            </div>

            {{-- Boutons --}}
            <div class="flex items-center justify-end gap-x-3">
                <x-ui.button variant="ghost" :href="$editMode ? route('library.show', $document) : route('library.index')">Annuler</x-ui.button>
                <x-ui.button type="submit" :loading="true" target="save">
                    {{ $editMode ? 'Enregistrer' : 'Ajouter le document' }}
                </x-ui.button>
            </div>
        </div>
    </form>
</div>
