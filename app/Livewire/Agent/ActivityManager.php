<?php

namespace App\Livewire\Agent;

use App\Models\ActivityEntry;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityManager extends Component
{
    use WithPagination;

    public string $search = '';

    public int $perPage = 20;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $entries = ActivityEntry::query()
            ->with('user')
            ->when($this->search, function ($query) {
                $query->where('message', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.agent.activity-manager', [
            'entries' => $entries,
        ]);
    }
}
