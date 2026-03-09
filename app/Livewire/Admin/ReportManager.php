<?php

namespace App\Livewire\Admin;

use App\Enums\ReportStatus;
use App\Enums\ThreatLevel;
use App\Models\Category;
use App\Models\Report;
use App\Models\UserRead;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterCategory = '';

    public string $filterStatus = '';

    public string $filterThreatLevel = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public bool $myContributions = false;

    public string $dateFrom = '';

    public string $dateTo = '';

    public int $perPage = 15;

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function updatingMyContributions(): void
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

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterThreatLevel(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function exportCsv(): StreamedResponse
    {
        $user = Auth::user();

        $specialReportIds = $user->isDirectorG()
            ? collect()
            : $user->specialPermissions()
                ->where('permissionable_type', Report::class)
                ->pluck('permissionable_id');

        $reports = Report::query()
            ->with('category')
            ->where(function ($query) use ($user, $specialReportIds) {
                if (! $user->isDirectorG()) {
                    $query->where('accreditation_level', '<=', $user->accreditation_level)
                        ->orWhereIn('id', $specialReportIds);
                }
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', "%{$this->search}%")
                        ->orWhere('title', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterCategory, function ($query) {
                $query->where('category_id', $this->filterCategory);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterThreatLevel, function ($query) {
                $query->where('threat_level', $this->filterThreatLevel);
            })
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->myContributions, function ($query) use ($user) {
                $query->whereHas('activityEntries', function ($aq) use ($user) {
                    $aq->where('user_id', $user->id)
                        ->where('event_type', 'created');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->get();

        return response()->streamDownload(function () use ($reports) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Code', 'Titre', 'Catégorie', 'Niveau de menace', 'Statut', 'Accréditation', 'Créé le'], ';');

            foreach ($reports as $report) {
                fputcsv($handle, [
                    $report->code,
                    $report->title,
                    $report->category?->name ?? '—',
                    $report->threat_level->label(),
                    $report->status->label(),
                    $report->accreditation_level,
                    $report->created_at->format('d/m/Y'),
                ], ';');
            }

            fclose($handle);
        }, 'rapports-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function render(): View
    {
        $user = Auth::user();

        $specialReportIds = $user->isDirectorG()
            ? collect()
            : $user->specialPermissions()
                ->where('permissionable_type', Report::class)
                ->pluck('permissionable_id');

        $reports = Report::query()
            ->with('category')
            ->where(function ($query) use ($user, $specialReportIds) {
                if (! $user->isDirectorG()) {
                    $query->where('accreditation_level', '<=', $user->accreditation_level)
                        ->orWhereIn('id', $specialReportIds);
                }
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', "%{$this->search}%")
                        ->orWhere('title', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterCategory, function ($query) {
                $query->where('category_id', $this->filterCategory);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterThreatLevel, function ($query) {
                $query->where('threat_level', $this->filterThreatLevel);
            })
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->myContributions, function ($query) use ($user) {
                $query->whereHas('activityEntries', function ($aq) use ($user) {
                    $aq->where('user_id', $user->id)
                        ->where('event_type', 'created');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $readReportIds = UserRead::readIdsFor($user->id, Report::class, $reports->pluck('id')->toArray());

        return view('livewire.admin.report-manager', [
            'reports' => $reports,
            'readReportIds' => $readReportIds,
            'categories' => Category::orderBy('name')->get(),
            'statuses' => ReportStatus::cases(),
            'threatLevels' => ThreatLevel::cases(),
        ]);
    }
}
