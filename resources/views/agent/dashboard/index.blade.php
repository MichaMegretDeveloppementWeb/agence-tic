@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')

    {{-- En-tête --}}
    <div>
        <h1 class="text-lg font-semibold text-gray-900">Tableau de bord</h1>
        <p class="mt-0.5 text-[13px] text-gray-500">
            Bienvenue, {{ $user->name }} — Accréditation niveau {{ $user->accreditation_level }} · {{ $user->role->label() }}
        </p>
    </div>

    {{-- Statistiques --}}
    @if($user->isDirectorG())
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            <x-ui.stat-card
                label="Total rapports"
                :value="(string) $totalReports"
                icon="clipboard-document-list"
            />
            <x-ui.stat-card
                label="Total documents"
                :value="(string) $totalDocuments"
                icon="folder"
            />
            <x-ui.stat-card
                label="Niveau d'accréditation"
                :value="(string) $user->accreditation_level . ' / 8'"
                icon="lock-closed"
            />
            <x-ui.stat-card
                label="Rappels actifs"
                :value="(string) $reminders->count()"
                icon="bell"
            />
            <x-ui.stat-card
                label="Agents actifs"
                :value="$activeAgents . ' / ' . $totalAgents"
                icon="users"
            />
            <x-ui.stat-card
                label="Candidatures en attente"
                :value="(string) $pendingApplications"
                icon="envelope"
            />
        </div>
    @else
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.stat-card
                label="Rapports accessibles"
                :value="(string) $reportsCount"
                icon="clipboard-document-list"
            />
            <x-ui.stat-card
                label="Documents accessibles"
                :value="(string) $documentsCount"
                icon="folder"
            />
            <x-ui.stat-card
                label="Niveau d'accréditation"
                :value="(string) $user->accreditation_level . ' / 8'"
                icon="lock-closed"
            />
            <x-ui.stat-card
                label="Rappels actifs"
                :value="(string) $reminders->count()"
                icon="bell"
            />
        </div>
    @endif

    {{-- Contenu principal — deux colonnes égales --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Rappels actifs --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-[13px] font-semibold text-gray-900">Rappels actifs</h2>
                <x-ui.button variant="ghost" size="compact" href="{{ route('reminders.index') }}">
                    Voir tout
                </x-ui.button>
            </div>

            @if($reminders->isEmpty())
                <x-ui.empty-state
                    icon="bell"
                    title="Aucun rappel actif"
                    description="Vous n'avez aucun rappel en cours pour le moment."
                />
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($reminders as $reminder)
                        <div class="py-3 first:pt-0 last:pb-0">
                            <div class="flex items-center justify-between gap-x-3">
                                <p class="text-[13px] font-medium text-gray-900 truncate">{{ $reminder->title }}</p>
                                @if($reminder->due_date)
                                    <span class="shrink-0 text-[11px] text-gray-400">
                                        {{ $reminder->due_date->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                            @if($reminder->content)
                                <p class="mt-0.5 text-[12px] text-gray-400 truncate">{{ $reminder->content }}</p>
                            @endif
                            <div class="mt-1.5 flex items-center gap-x-1.5">
                                <x-ui.badge :color="$reminder->type->badgeColor()" dot>
                                    {{ $reminder->type->label() }}
                                </x-ui.badge>
                                <x-ui.badge :color="$reminder->priority?->badgeColor() ?? 'blue'">
                                    {{ $reminder->priority?->label() ?? 'Normale' }}
                                </x-ui.badge>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>

        {{-- Activité récente --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-[13px] font-semibold text-gray-900">Activité récente</h2>
                <x-ui.button variant="ghost" size="compact" href="{{ route('activity.index') }}">
                    Voir tout
                </x-ui.button>
            </div>

            @if($recentActivity->isEmpty())
                <x-ui.empty-state
                    icon="chart-bar"
                    title="Aucune activité"
                    description="Aucune activité récente à afficher."
                />
            @else
                <x-ui.timeline>
                    @foreach($recentActivity as $entry)
                        <x-ui.timeline.item
                            :title="e($entry->message)"
                            :date="$entry->created_at->diffForHumans()"
                            color="gray"
                        />
                    @endforeach
                </x-ui.timeline>
            @endif
        </x-ui.card>

    </div>

    {{-- Candidatures en attente — Directeur G uniquement --}}
    @if($user->isDirectorG())
        <div class="mt-6">
            <x-ui.card :padding="false">
                <div class="flex items-center justify-between px-5 py-4">
                    <h2 class="text-[13px] font-semibold text-gray-900">Candidatures en attente</h2>
                    <x-ui.button variant="ghost" size="compact" href="{{ route('admin.applications.index') }}">
                        Voir tout
                    </x-ui.button>
                </div>

                @if($recentApplications->isEmpty())
                    <div class="px-5 pb-4">
                        <x-ui.empty-state
                            icon="envelope"
                            title="Aucune candidature"
                            description="Il n'y a aucune candidature en attente de traitement."
                        />
                    </div>
                @else
                    <x-ui.table>
                        <x-ui.table.head>
                            <x-ui.table.header-cell :first="true">Candidat</x-ui.table.header-cell>
                            <x-ui.table.header-cell hidden="sm">Email</x-ui.table.header-cell>
                            <x-ui.table.header-cell hidden="md">Date</x-ui.table.header-cell>
                            <x-ui.table.header-cell :last="true" align="right">Statut</x-ui.table.header-cell>
                        </x-ui.table.head>
                        <x-ui.table.body>
                            @foreach($recentApplications as $application)
                                <x-ui.table.row class="cursor-pointer hover:bg-gray-50" onclick="window.location='{{ route('admin.applications.show', $application) }}'">
                                    <x-ui.table.cell :first="true">
                                        <div class="flex items-center gap-x-3">
                                            <x-ui.avatar
                                                :initials="strtoupper(substr($application->name, 0, 2))"
                                                size="default"
                                                color="amber"
                                            />
                                            <p class="text-[13px] font-medium text-gray-900">{{ $application->name }}</p>
                                        </div>
                                    </x-ui.table.cell>
                                    <x-ui.table.cell hidden="sm">
                                        <span class="text-[13px] text-gray-600">{{ $application->email }}</span>
                                    </x-ui.table.cell>
                                    <x-ui.table.cell hidden="md">
                                        <span class="text-[12px] text-gray-400">{{ $application->created_at->format('d/m/Y') }}</span>
                                    </x-ui.table.cell>
                                    <x-ui.table.cell :last="true" align="right">
                                        <x-ui.badge color="amber" dot>En attente</x-ui.badge>
                                    </x-ui.table.cell>
                                </x-ui.table.row>
                            @endforeach
                        </x-ui.table.body>
                    </x-ui.table>
                @endif
            </x-ui.card>
        </div>
    @endif

@endsection
