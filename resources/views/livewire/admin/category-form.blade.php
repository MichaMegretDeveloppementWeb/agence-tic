<div>
    @error('form-save-failed')
        <x-ui.alert type="error" dismissible class="mb-5">{{ $message }}</x-ui.alert>
    @enderror

    <form wire:submit="save" autocomplete="off">
        <div class="space-y-8">
            {{-- Informations --}}
            <div>
                <div class="mb-4">
                    <h2 class="text-[13px] font-semibold text-gray-900">Informations</h2>
                    <p class="mt-1 text-[13px] text-gray-500">Nom et description de la catégorie.</p>
                </div>
                <x-ui.card>
                    <div class="grid grid-cols-1 gap-x-4 gap-y-5">
                        {{-- Nom --}}
                        <div>
                            <label for="category-name" class="block text-[13px] font-medium text-gray-700 mb-1.5">Nom</label>
                            <input
                                type="text"
                                id="category-name"
                                wire:model.blur="name"
                                placeholder="Nom de la catégorie"
                                class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('name') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset"
                            />
                            @error('name')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="category-description" class="block text-[13px] font-medium text-gray-700 mb-1.5">
                                Description
                                <span class="font-normal text-gray-400">(optionnel)</span>
                            </label>
                            <textarea
                                id="category-description"
                                wire:model.blur="description"
                                rows="3"
                                placeholder="Description de la catégorie..."
                                class="block w-full rounded-lg border-0 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('description') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} placeholder:text-gray-400 focus:ring-2 focus:ring-inset"
                            ></textarea>
                            @error('description')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-ui.card>
            </div>

            {{-- Boutons --}}
            <div class="flex items-center justify-end gap-x-3">
                <x-ui.button variant="ghost" :href="$editMode ? route('categories.show', $category) : route('categories.index')">Annuler</x-ui.button>
                <x-ui.button type="submit" :loading="true" target="save">
                    {{ $editMode ? 'Enregistrer' : 'Créer la catégorie' }}
                </x-ui.button>
            </div>
        </div>
    </form>

    {{-- Zone de danger (edit mode uniquement) --}}
    @if($editMode && auth()->user()->isDirectorG())
        <div class="mt-8">
            <div class="mb-4">
                <h2 class="text-[13px] font-semibold text-red-600">Zone de danger</h2>
                <p class="mt-1 text-[13px] text-gray-500">Actions irréversibles sur cette catégorie.</p>
            </div>
            <x-ui.card variant="danger">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[13px] font-medium text-gray-900">Supprimer la catégorie</p>
                        <p class="text-[12px] text-gray-400">Cette action est irréversible. La catégorie ne peut être supprimée que si elle est vide.</p>
                    </div>
                    <x-ui.button variant="danger" @click="$dispatch('open-modal', 'delete-category')">Supprimer</x-ui.button>
                </div>
            </x-ui.card>
        </div>

        <x-ui.modal name="delete-category" variant="confirm" title="Supprimer cette catégorie ?">
            Cette action est irréversible. La catégorie sera définitivement supprimée.

            <x-slot:actions>
                <x-ui.button variant="ghost" @click="$dispatch('close-modal', 'delete-category')">Annuler</x-ui.button>
                <x-ui.button variant="danger" wire:click="delete" :loading="true" target="delete">Supprimer</x-ui.button>
            </x-slot:actions>
        </x-ui.modal>
    @endif
</div>
