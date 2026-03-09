@extends('layouts.app')

@section('title', $agent->agent_code)

@section('content')
    {{-- Breadcrumb --}}
    <x-ui.breadcrumb>
        <x-ui.breadcrumb.item :href="route('admin.agents.index')">Agents</x-ui.breadcrumb.item>
        <x-ui.breadcrumb.item>{{ $agent->agent_code }}</x-ui.breadcrumb.item>
    </x-ui.breadcrumb>

    <div class="mt-4 space-y-6">

        {{-- Profile header card --}}
        <x-ui.card>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-x-4">
                    @if($agent->avatar_path)
                            <x-ui.avatar :src="Storage::disk('public')->url($agent->avatar_path)" size="xl" />
                        @else
                            <x-ui.avatar :initials="strtoupper(mb_substr($agent->name, 0, 2))" size="xl" color="gray" />
                        @endif
                    <div>
                        <div class="flex items-center gap-x-2.5">
                            <h1 class="text-lg font-semibold text-gray-900">{{ $agent->name }}</h1>
                            <x-ui.badge :color="$agent->is_active ? 'emerald' : 'red'" dot>
                                {{ $agent->is_active ? 'Actif' : 'Inactif' }}
                            </x-ui.badge>
                        </div>
                        <p class="mt-0.5 text-[13px] text-gray-500">{{ $agent->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-x-2">
                    <x-ui.button variant="secondary" :href="route('admin.agents.index')">
                        <x-ui.icon name="arrow-left" class="h-4 w-4" />
                        Retour
                    </x-ui.button>
                    <x-ui.button variant="secondary" :href="route('admin.agents.edit', $agent)">
                        <x-ui.icon name="pencil-square" class="h-4 w-4" />
                        Modifier
                    </x-ui.button>
                    @livewire('admin.agent-toggle-active', ['agent' => $agent])
                </div>
            </div>
        </x-ui.card>

        {{-- Metadata bar --}}
        <div class="rounded-xl border border-gray-200 bg-white px-5 py-3">
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                {{-- Role --}}
                <div class="flex items-center gap-x-2">
                    <x-ui.icon name="users" class="h-4 w-4 text-gray-400" />
                    <span class="text-[13px] text-gray-600">
                        {{ $agent->isDirectorG() ? 'Directeur G' : 'Agent' }}
                    </span>
                </div>

                <span class="hidden sm:inline text-gray-200">|</span>

                {{-- Accreditation level --}}
                <div class="flex items-center gap-x-2">
                    <x-ui.icon name="lock-closed" class="h-4 w-4 text-gray-400" />
                    <span class="text-[13px] text-gray-600">Niveau {{ $agent->accreditation_level }}</span>
                </div>

                <span class="hidden sm:inline text-gray-200">|</span>

                {{-- Agent code --}}
                <div class="flex items-center gap-x-2">
                    <x-ui.icon name="clipboard-document-list" class="h-4 w-4 text-gray-400" />
                    <span class="text-[13px] font-medium text-gray-900">{{ $agent->agent_code }}</span>
                </div>

                <span class="hidden sm:inline text-gray-200">|</span>

                {{-- Created date --}}
                <div class="flex items-center gap-x-2">
                    <x-ui.icon name="bolt" class="h-4 w-4 text-gray-400" />
                    <span class="text-[11px] text-gray-400">Membre depuis</span>
                    <span class="text-[13px] text-gray-600">{{ $agent->created_at->format('d/m/Y') }}</span>
                </div>

                @if($agent->last_login_at)
                    <span class="hidden sm:inline text-gray-200">|</span>

                    {{-- Last login --}}
                    <div class="flex items-center gap-x-2">
                        <x-ui.icon name="arrow-path" class="h-4 w-4 text-gray-400" />
                        <span class="text-[11px] text-gray-400">Dernière connexion</span>
                        <span class="text-[13px] text-gray-600">{{ $agent->last_login_at->format('d/m/Y \a H:i') }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Special permissions --}}
        <div>
            <h2 class="text-[13px] font-semibold text-gray-900 mb-3">Permissions spéciales</h2>
            @if($agent->specialPermissions->isNotEmpty())
                <x-ui.card :padding="false">
                    <x-ui.table>
                        <x-ui.table.head>
                            <x-ui.table.header-cell :first="true">Type</x-ui.table.header-cell>
                            <x-ui.table.header-cell>Élément</x-ui.table.header-cell>
                            <x-ui.table.header-cell :last="true">Accordée le</x-ui.table.header-cell>
                        </x-ui.table.head>
                        <x-ui.table.body>
                            @foreach($agent->specialPermissions as $permission)
                                <x-ui.table.row>
                                    <x-ui.table.cell :first="true">
                                        <span class="text-[13px] text-gray-600">
                                            @if($permission->permissionable_type === \App\Models\Report::class)
                                                Rapport
                                            @elseif($permission->permissionable_type === \App\Models\Document::class)
                                                Document
                                            @else
                                                {{ class_basename($permission->permissionable_type) }}
                                            @endif
                                        </span>
                                    </x-ui.table.cell>
                                    <x-ui.table.cell>
                                        <span class="text-[13px] text-gray-900">
                                            @if($permission->permissionable)
                                                {{ $permission->permissionable->title ?? $permission->permissionable->code ?? $permission->permissionable->name ?? '—' }}
                                            @else
                                                <span class="text-gray-400">Élément supprimé</span>
                                            @endif
                                        </span>
                                    </x-ui.table.cell>
                                    <x-ui.table.cell :last="true">
                                        <span class="text-[13px] text-gray-400">{{ $permission->created_at->format('d/m/Y') }}</span>
                                    </x-ui.table.cell>
                                </x-ui.table.row>
                            @endforeach
                        </x-ui.table.body>
                    </x-ui.table>
                </x-ui.card>
            @else
                <x-ui.card>
                    <p class="text-[13px] text-gray-400">Aucune permission spéciale.</p>
                </x-ui.card>
            @endif
        </div>

        {{-- Recent activity --}}
        <div>
            <h2 class="text-[13px] font-semibold text-gray-900 mb-3">Activité récente</h2>
            @if($agent->activityEntries->isNotEmpty())
                <x-ui.card>
                    <x-ui.timeline>
                        @foreach($agent->activityEntries as $entry)
                            <x-ui.timeline.item
                                :title="e($entry->message)"
                                :date="$entry->created_at->diffForHumans()"
                                color="gray"
                            />
                        @endforeach
                    </x-ui.timeline>
                </x-ui.card>
            @else
                <x-ui.card>
                    <p class="text-[13px] text-gray-400">Aucune activité récente.</p>
                </x-ui.card>
            @endif
        </div>

    </div>
@endsection
