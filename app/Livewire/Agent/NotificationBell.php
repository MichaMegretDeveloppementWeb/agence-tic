<?php

namespace App\Livewire\Agent;

use App\Enums\ReminderType;
use App\Models\Reminder;
use App\Models\UserRead;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public function getUnreadCountProperty(): int
    {
        $user = Auth::user();

        // Get IDs of all reminders visible to this user that are not completed
        $visibleReminders = Reminder::query()
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
            ->pluck('id')
            ->toArray();

        if (empty($visibleReminders)) {
            return 0;
        }

        // Get IDs of reminders this user has already read
        $readIds = UserRead::readIdsFor($user->id, Reminder::class, $visibleReminders);

        // Unread = visible minus read
        return count(array_diff($visibleReminders, $readIds->toArray()));
    }

    public function render(): View
    {
        return view('livewire.agent.notification-bell');
    }
}
