<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AgentManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterLevel = '';

    public string $filterStatus = '';

    public string $sortBy = 'agent_code';

    public string $sortDirection = 'asc';

    public int $perPage = 15;

    /** @return array<string, array<string, mixed>> */
    protected function queryString(): array
    {
        return [
            'search' => ['except' => '', 'as' => 'q'],
            'filterLevel' => ['except' => '', 'as' => 'level'],
            'filterStatus' => ['except' => '', 'as' => 'status'],
            'perPage' => ['except' => 15, 'as' => 'pp'],
        ];
    }

    public function resetFilters(): void
    {
        $this->reset(['filterLevel', 'filterStatus']);
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterLevel(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $agentId): void
    {
        $agent = User::where('role', UserRole::Agent)->find($agentId);

        if (! $agent) {
            return;
        }

        try {
            $agent->update(['is_active' => ! $agent->is_active]);

            $status = $agent->is_active ? 'activé' : 'désactivé';

            app(ActivityLogger::class)->log(
                'updated',
                "Agent {$agent->agent_code} {$status} par le Directeur G.",
                auth()->id(),
                $agent,
            );

            $this->dispatch('toast', type: 'success', title: 'Succès', description: "Agent {$status} avec succès.");
        } catch (\Throwable $e) {
            Log::error('Failed to toggle agent status', ['exception' => $e, 'agent_id' => $agentId]);
            $this->addError('agent-toggle-failed', 'Impossible de modifier le statut de l\'agent. Veuillez réessayer.');
        }
    }

    public function exportCsv(): StreamedResponse
    {
        $agents = User::query()
            ->where('role', UserRole::Agent)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('agent_code', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterLevel, function ($query) {
                $query->where('accreditation_level', $this->filterLevel);
            })
            ->when($this->filterStatus !== '', function ($query) {
                if ($this->filterStatus === 'active') {
                    $query->where('is_active', true);
                } elseif ($this->filterStatus === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->get();

        return response()->streamDownload(function () use ($agents) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Code agent', 'Nom', 'Email', 'Rôle', 'Niveau', 'Statut'], ';');

            foreach ($agents as $agent) {
                fputcsv($handle, [
                    $agent->agent_code,
                    $agent->name,
                    $agent->email,
                    $agent->role->label(),
                    $agent->accreditation_level,
                    $agent->is_active ? 'Actif' : 'Inactif',
                ], ';');
            }

            fclose($handle);
        }, 'agents-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function render(): View
    {
        $agents = User::query()
            ->where('role', UserRole::Agent)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('agent_code', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterLevel, function ($query) {
                $query->where('accreditation_level', $this->filterLevel);
            })
            ->when($this->filterStatus !== '', function ($query) {
                if ($this->filterStatus === 'active') {
                    $query->where('is_active', true);
                } elseif ($this->filterStatus === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.agent-manager', [
            'agents' => $agents,
        ]);
    }
}
