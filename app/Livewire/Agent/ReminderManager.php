<?php

namespace App\Livewire\Agent;

use App\Enums\ReminderPriority;
use App\Enums\ReminderType;
use App\Models\Reminder;
use App\Models\UserRead;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ReminderManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterType = '';

    public string $filterPriority = '';

    public bool $showCompleted = false;

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 15;

    /** @return array<string, array<string, mixed>> */
    protected function queryString(): array
    {
        return [
            'search' => ['except' => '', 'as' => 'q'],
            'filterType' => ['except' => '', 'as' => 'type'],
            'filterPriority' => ['except' => '', 'as' => 'priority'],
            'showCompleted' => ['except' => false, 'as' => 'completed'],
            'perPage' => ['except' => 15, 'as' => 'pp'],
        ];
    }

    public function resetFilters(): void
    {
        $this->reset(['filterType', 'filterPriority', 'showCompleted']);
        $this->resetPage();
    }

    public function mount(): void
    {
        $this->markVisibleAsRead();
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

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatingFilterPriority(): void
    {
        $this->resetPage();
    }

    public function updatingShowCompleted(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function toggleComplete(int $reminderId): void
    {
        $user = Auth::user();

        $reminder = Reminder::query()
            ->where('id', $reminderId)
            ->where('type', ReminderType::Personal)
            ->where('created_by', $user->id)
            ->first();

        if (! $reminder) {
            return;
        }

        try {
            $reminder->update(['is_completed' => ! $reminder->is_completed]);

            $status = $reminder->is_completed ? 'complété' : 'réactivé';
            $this->dispatch('toast', type: 'success', title: 'Succès', description: "Rappel {$status} avec succès.");
        } catch (\Throwable $e) {
            Log::error('Failed to toggle reminder', ['exception' => $e, 'reminder_id' => $reminderId]);
            $this->addError('reminder-toggle-failed', 'Impossible de modifier le rappel. Veuillez réessayer.');
        }
    }

    public function deleteReminder(int $reminderId): void
    {
        $user = Auth::user();

        $reminder = Reminder::query()
            ->where('id', $reminderId)
            ->where('type', ReminderType::Personal)
            ->where('created_by', $user->id)
            ->first();

        if (! $reminder) {
            return;
        }

        try {
            $reminder->delete();
            $this->dispatch('toast', type: 'success', title: 'Succès', description: 'Rappel supprimé avec succès.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete reminder', ['exception' => $e, 'reminder_id' => $reminderId]);
            $this->addError('reminder-deletion-failed', 'Impossible de supprimer le rappel. Veuillez réessayer.');
        }
    }

    private function markVisibleAsRead(): void
    {
        $user = Auth::user();

        $visibleIds = Reminder::query()
            ->where('is_completed', false)
            ->where(function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('type', ReminderType::Personal)
                        ->where('created_by', $user->id);
                })
                    ->orWhere(function ($q) use ($user) {
                        $q->where('type', ReminderType::Targeted)
                            ->where('target_user_id', $user->id);
                    })
                    ->orWhere('type', ReminderType::Global);
            })
            ->pluck('id');

        foreach ($visibleIds as $id) {
            UserRead::markAsRead($user->id, Reminder::class, $id);
        }
    }

    public function render(): View
    {
        $user = Auth::user();

        $reminders = Reminder::query()
            ->where(function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('type', ReminderType::Personal)
                        ->where('created_by', $user->id);
                })
                    ->orWhere(function ($q) use ($user) {
                        $q->where('type', ReminderType::Targeted)
                            ->where('target_user_id', $user->id);
                    })
                    ->orWhere('type', ReminderType::Global);
            })
            ->when(! $this->showCompleted, function ($query) {
                $query->where('is_completed', false);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('content', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterType, function ($query) {
                $query->where('type', $this->filterType);
            })
            ->when($this->filterPriority, function ($query) {
                $query->where('priority', $this->filterPriority);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $readReminderIds = UserRead::readIdsFor(
            $user->id,
            Reminder::class,
            $reminders->pluck('id')->toArray(),
        );

        return view('livewire.agent.reminder-manager', [
            'reminders' => $reminders,
            'readReminderIds' => $readReminderIds,
            'reminderTypes' => ReminderType::cases(),
            'priorities' => ReminderPriority::cases(),
        ]);
    }
}
