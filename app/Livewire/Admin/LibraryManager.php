<?php

namespace App\Livewire\Admin;

use App\Enums\DocumentStatus;
use App\Models\Category;
use App\Models\Document;
use App\Models\UserRead;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LibraryManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterCategory = '';

    public string $filterStatus = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public bool $myContributions = false;

    public string $dateFrom = '';

    public string $dateTo = '';

    public int $perPage = 15;

    /** @return array<string, array<string, mixed>> */
    protected function queryString(): array
    {
        return [
            'search' => ['except' => '', 'as' => 'q'],
            'filterCategory' => ['except' => '', 'as' => 'cat'],
            'filterStatus' => ['except' => '', 'as' => 'status'],
            'myContributions' => ['except' => false, 'as' => 'mine'],
            'dateFrom' => ['except' => '', 'as' => 'from'],
            'dateTo' => ['except' => '', 'as' => 'to'],
            'perPage' => ['except' => 15, 'as' => 'pp'],
        ];
    }

    public function resetFilters(): void
    {
        $this->reset(['filterCategory', 'filterStatus', 'myContributions', 'dateFrom', 'dateTo']);
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

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function exportCsv(): StreamedResponse
    {
        $user = Auth::user();

        $specialDocIds = $user->isDirectorG()
            ? collect()
            : $user->specialPermissions()
                ->where('permissionable_type', Document::class)
                ->pluck('permissionable_id');

        $documents = Document::query()
            ->with(['category', 'uploader'])
            ->where(function ($query) use ($user, $specialDocIds) {
                if (! $user->isDirectorG()) {
                    $query->where('status', 'active')
                        ->where(function ($q) use ($user, $specialDocIds) {
                            $q->where('accreditation_level', '<=', $user->accreditation_level)
                                ->orWhereIn('id', $specialDocIds);
                        });
                }
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('file_name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterCategory, function ($query) {
                $query->where('category_id', $this->filterCategory);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->myContributions, function ($query) use ($user) {
                $query->where('uploaded_by', $user->id);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->get();

        return response()->streamDownload(function () use ($documents) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Titre', 'Fichier', 'Catégorie', 'Statut', 'Accréditation', 'Ajouté par', 'Créé le'], ';');

            foreach ($documents as $document) {
                fputcsv($handle, [
                    $document->title,
                    $document->file_name,
                    $document->category?->name ?? '—',
                    $document->status->label(),
                    $document->accreditation_level,
                    $document->uploader?->name ?? '—',
                    $document->created_at->format('d/m/Y'),
                ], ';');
            }

            fclose($handle);
        }, 'documents-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function render(): View
    {
        $user = Auth::user();

        $specialDocIds = $user->isDirectorG()
            ? collect()
            : $user->specialPermissions()
                ->where('permissionable_type', Document::class)
                ->pluck('permissionable_id');

        $documents = Document::query()
            ->with(['category', 'uploader'])
            ->where(function ($query) use ($user, $specialDocIds) {
                if (! $user->isDirectorG()) {
                    $query->where('status', 'active')
                        ->where(function ($q) use ($user, $specialDocIds) {
                            $q->where('accreditation_level', '<=', $user->accreditation_level)
                                ->orWhereIn('id', $specialDocIds);
                        });
                }
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('file_name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterCategory, function ($query) {
                $query->where('category_id', $this->filterCategory);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->myContributions, function ($query) use ($user) {
                $query->where('uploaded_by', $user->id);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $readDocumentIds = UserRead::readIdsFor($user->id, Document::class, $documents->pluck('id')->toArray());

        return view('livewire.admin.library-manager', [
            'documents' => $documents,
            'readDocumentIds' => $readDocumentIds,
            'categories' => Category::orderBy('name')->get(),
            'statuses' => DocumentStatus::cases(),
        ]);
    }
}
