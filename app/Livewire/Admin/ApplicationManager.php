<?php

namespace App\Livewire\Admin;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public string $dateFrom = '';

    public string $dateTo = '';

    public int $perPage = 15;

    /** @return array<string, array<string, mixed>> */
    protected function queryString(): array
    {
        return [
            'search' => ['except' => '', 'as' => 'q'],
            'filterStatus' => ['except' => '', 'as' => 'status'],
            'dateFrom' => ['except' => '', 'as' => 'from'],
            'dateTo' => ['except' => '', 'as' => 'to'],
            'perPage' => ['except' => 15, 'as' => 'pp'],
        ];
    }

    public function resetFilters(): void
    {
        $this->reset(['filterStatus', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
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

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function exportCsv(): StreamedResponse
    {
        $applications = Application::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->get();

        return response()->streamDownload(function () use ($applications) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Nom', 'Email', 'Statut', 'Date de candidature'], ';');

            foreach ($applications as $application) {
                fputcsv($handle, [
                    $application->name,
                    $application->email,
                    $application->status->label(),
                    $application->created_at->format('d/m/Y'),
                ], ';');
            }

            fclose($handle);
        }, 'candidatures-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function render(): View
    {
        $applications = Application::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.application-manager', [
            'applications' => $applications,
            'statuses' => ApplicationStatus::cases(),
        ]);
    }
}
