<div>
    @error('permission-grant-failed')
        <x-ui.alert type="error" dismissible class="mb-5">{{ $message }}</x-ui.alert>
    @enderror
    @error('permission-revoke-failed')
        <x-ui.alert type="error" dismissible class="mb-5">{{ $message }}</x-ui.alert>
    @enderror

    {{-- En-tête --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">Permissions spéciales</h1>
            <p class="mt-0.5 text-[13px] text-gray-500">Accordez un accès exceptionnel à un agent sur une ressource spécifique.</p>
        </div>
        @if(!$showGrantForm)
            <x-ui.button wire:click="openGrantForm">
                <x-ui.icon name="plus" class="h-4 w-4" />
                Accorder une permission
            </x-ui.button>
        @endif
    </div>

    {{-- Recherche --}}
    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:max-w-xs">
            <x-ui.icon name="magnifying-glass" class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Rechercher par nom ou code agent..."
                class="w-full rounded-lg border-0 bg-white py-1.5 pl-8 pr-3 text-[13px] text-gray-900 ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-gray-900"
            />
        </div>
    </div>

    {{-- Formulaire d'attribution --}}
    @if($showGrantForm)
        <div class="mt-4">
            <x-ui.card>
                <h3 class="text-[13px] font-semibold text-gray-900 mb-4">Accorder une permission spéciale</h3>
                <form wire:submit="grant" class="space-y-5">
                    <div class="grid grid-cols-1 gap-x-4 gap-y-5 sm:grid-cols-3">
                        {{-- Agent --}}
                        <div>
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Agent</label>
                            <x-combobox
                                :options="$agents->map(fn($a) => ['id' => $a->id, 'label' => $a->agent_code . ' — ' . $a->name])->toArray()"
                                wire-model="agentId"
                                placeholder="Sélectionner un agent"
                                :has-error="$errors->has('agentId')"
                            />
                            @error('agentId')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Type de ressource --}}
                        <div>
                            <label for="perm-type" class="block text-[13px] font-medium text-gray-700 mb-1.5">Type de ressource</label>
                            <select
                                id="perm-type"
                                wire:model.live="permissionableType"
                                class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-900 ring-1 ring-inset {{ $errors->has('permissionableType') ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-200 focus:ring-gray-900' }} focus:ring-2 focus:ring-inset"
                            >
                                <option value="">Sélectionner un type</option>
                                <option value="report">Rapport</option>
                                <option value="document">Document</option>
                            </select>
                            @error('permissionableType')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ressource (dynamique selon le type) --}}
                        <div x-data="{ type: @entangle('permissionableType') }">
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Ressource</label>

                            {{-- Combobox rapports --}}
                            <div x-show="type === 'report'" x-cloak>
                                <x-combobox
                                    :options="$reports->map(fn($r) => ['id' => $r->id, 'label' => $r->code . ' — ' . $r->title])->toArray()"
                                    wire-model="permissionableId"
                                    placeholder="Sélectionner un rapport"
                                    :has-error="$errors->has('permissionableId')"
                                />
                            </div>

                            {{-- Combobox documents --}}
                            <div x-show="type === 'document'" x-cloak>
                                <x-combobox
                                    :options="$documents->map(fn($d) => ['id' => $d->id, 'label' => $d->title])->toArray()"
                                    wire-model="permissionableId"
                                    placeholder="Sélectionner un document"
                                    :has-error="$errors->has('permissionableId')"
                                />
                            </div>

                            {{-- Placeholder quand aucun type n'est sélectionné --}}
                            <select
                                x-show="type !== 'report' && type !== 'document'"
                                x-cloak
                                disabled
                                class="block w-full rounded-lg border-0 py-2 text-[13px] text-gray-400 bg-gray-50 ring-1 ring-inset ring-gray-200 cursor-not-allowed"
                            >
                                <option>Sélectionnez d'abord un type</option>
                            </select>

                            @error('permissionableId')
                                <p class="mt-1.5 text-[12px] text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-x-3 pt-2">
                        <x-ui.button variant="ghost" type="button" wire:click="closeGrantForm">Annuler</x-ui.button>
                        <x-ui.button type="submit" :loading="true" target="grant">Accorder</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    @endif

    {{-- Tableau des permissions --}}
    <div class="mt-4">
        @if($permissions->isEmpty())
            <x-ui.empty-state
                icon="lock-closed"
                title="Aucune permission spéciale"
                :description="$search
                    ? 'Aucune permission ne correspond à vos critères de recherche.'
                    : 'Aucune permission spéciale n\'a été accordée pour le moment.'"
            >
                @if(!$showGrantForm)
                    <x-ui.button wire:click="openGrantForm">
                        <x-ui.icon name="plus" class="h-4 w-4" />
                        Accorder une permission
                    </x-ui.button>
                @endif
            </x-ui.empty-state>
        @else
            <x-ui.table>
                <x-ui.table.head>
                    <x-ui.table.header-cell :first="true">Agent</x-ui.table.header-cell>
                    <x-ui.table.header-cell hidden="sm">Type</x-ui.table.header-cell>
                    <x-ui.table.header-cell>Ressource</x-ui.table.header-cell>
                    <x-ui.table.header-cell hidden="md">Accordée par</x-ui.table.header-cell>
                    <x-ui.table.header-cell hidden="md">Date</x-ui.table.header-cell>
                    <x-ui.table.header-cell :last="true"></x-ui.table.header-cell>
                </x-ui.table.head>
                <x-ui.table.body>
                    @foreach($permissions as $permission)
                        <x-ui.table.row>
                            <x-ui.table.cell :first="true">
                                <div>
                                    <p class="text-[13px] font-medium text-gray-900">{{ $permission->user?->name ?? '—' }}</p>
                                    <p class="text-[12px] text-gray-400">{{ $permission->user?->agent_code ?? '—' }}</p>
                                </div>
                            </x-ui.table.cell>
                            <x-ui.table.cell hidden="sm">
                                <x-ui.badge :color="$permission->permissionable instanceof \App\Models\Report ? 'blue' : 'indigo'">
                                    {{ $permission->permissionable instanceof \App\Models\Report ? 'Rapport' : 'Document' }}
                                </x-ui.badge>
                            </x-ui.table.cell>
                            <x-ui.table.cell>
                                @if($permission->permissionable instanceof \App\Models\Report)
                                    <div>
                                        <p class="text-[13px] font-medium text-gray-900">{{ $permission->permissionable->code }}</p>
                                        <p class="text-[12px] text-gray-400">{{ $permission->permissionable->title }}</p>
                                    </div>
                                @elseif($permission->permissionable instanceof \App\Models\Document)
                                    <span class="text-[13px] text-gray-900">{{ $permission->permissionable->title }}</span>
                                @else
                                    <span class="text-[13px] text-gray-400">Ressource supprimée</span>
                                @endif
                            </x-ui.table.cell>
                            <x-ui.table.cell hidden="md">
                                <span class="text-[13px] text-gray-600">{{ $permission->grantedBy?->name ?? '—' }}</span>
                            </x-ui.table.cell>
                            <x-ui.table.cell hidden="md">
                                <span class="text-[12px] text-gray-400">{{ $permission->created_at->format('d/m/Y') }}</span>
                            </x-ui.table.cell>
                            <x-ui.table.cell :last="true">
                                <x-ui.tooltip text="Révoquer la permission">
                                    <x-ui.button
                                        variant="danger"
                                        size="compact"
                                        wire:click="revoke({{ $permission->id }})"
                                        wire:confirm="Êtes-vous sûr de vouloir révoquer cette permission ?"
                                    >
                                        Révoquer
                                    </x-ui.button>
                                </x-ui.tooltip>
                            </x-ui.table.cell>
                        </x-ui.table.row>
                    @endforeach
                </x-ui.table.body>
            </x-ui.table>

            <div class="mt-4 flex items-center justify-between">
                <x-per-page />
                <x-ui.pagination :paginator="$permissions" mode="livewire" />
            </div>
        @endif
    </div>
</div>
