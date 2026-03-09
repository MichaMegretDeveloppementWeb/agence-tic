<?php

namespace App\Livewire\Agent;

use App\Enums\ReminderPriority;
use App\Enums\ReminderType;
use App\Enums\UserRole;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ReminderForm extends Component
{
    public string $title = '';

    public string $content = '';

    public string $dueDate = '';

    public string $type = 'personal';

    public string $priority = 'normal';

    public string $targetUserId = '';

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'min:3', 'max:200'],
            'content' => ['nullable', 'string', 'max:1000'],
            'dueDate' => ['nullable', 'date', 'after_or_equal:today'],
            'priority' => ['required', Rule::in(array_column(ReminderPriority::cases(), 'value'))],
            'type' => ['required', Rule::in(array_column(ReminderType::cases(), 'value'))],
        ];

        if ($this->type === ReminderType::Targeted->value) {
            $rules['targetUserId'] = ['required', 'exists:users,id'];
        }

        return $rules;
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'title.required' => 'Le titre du rappel est obligatoire.',
            'title.string' => 'Le titre doit être une chaîne de caractères.',
            'title.min' => 'Le titre doit contenir au moins 3 caractères.',
            'title.max' => 'Le titre ne peut pas dépasser 200 caractères.',
            'content.string' => 'Le contenu doit être une chaîne de caractères.',
            'content.max' => 'Le contenu ne peut pas dépasser 1000 caractères.',
            'dueDate.date' => 'La date d\'échéance n\'est pas valide.',
            'dueDate.after_or_equal' => 'La date d\'échéance doit être aujourd\'hui ou dans le futur.',
            'priority.required' => 'La priorité est obligatoire.',
            'priority.in' => 'La priorité sélectionnée n\'est pas valide.',
            'type.required' => 'Le type de rappel est obligatoire.',
            'type.in' => 'Le type de rappel sélectionné n\'est pas valide.',
            'targetUserId.required' => 'L\'agent ciblé est obligatoire pour un rappel ciblé.',
            'targetUserId.exists' => 'L\'agent sélectionné n\'existe pas.',
        ];
    }

    public function save(): void
    {
        $user = Auth::user();

        // Only Director G can create global/targeted reminders
        if (! $user->isDirectorG() && $this->type !== ReminderType::Personal->value) {
            $this->type = ReminderType::Personal->value;
        }

        $this->validate();

        try {
            $data = [
                'title' => $this->title,
                'content' => $this->content ?: null,
                'type' => $this->type,
                'priority' => $this->priority,
                'created_by' => $user->id,
                'due_date' => $this->dueDate ?: null,
            ];

            if ($this->type === ReminderType::Targeted->value) {
                $data['target_user_id'] = $this->targetUserId;
            }

            Reminder::create($data);

            session()->flash('toast-success', 'Rappel créé avec succès.');

            $this->redirect(route('reminders.index'));
        } catch (\Throwable $e) {
            Log::error('Failed to create reminder', ['exception' => $e]);
            $this->addError('form-save-failed', 'Impossible de créer le rappel. Veuillez réessayer. Si le problème persiste, contactez le support.');
        }
    }

    public function render(): View
    {
        $user = Auth::user();
        $isDirector = $user->isDirectorG();

        $agents = $isDirector
            ? User::where('role', UserRole::Agent)->where('is_active', true)->orderBy('name')->get()
            : collect();

        return view('livewire.agent.reminder-form', [
            'isDirector' => $isDirector,
            'agents' => $agents,
            'reminderTypes' => ReminderType::cases(),
            'priorities' => ReminderPriority::cases(),
        ]);
    }
}
