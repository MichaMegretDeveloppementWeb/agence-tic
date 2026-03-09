<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AgentForm extends Component
{
    public ?User $agent = null;

    public bool $editMode = false;

    public string $name = '';

    public string $email = '';

    public string $agentCode = '';

    public string $password = '';

    public int $accreditationLevel = 1;

    public bool $isActive = true;

    public function mount(?User $agent = null): void
    {
        if ($agent?->exists) {
            $this->agent = $agent;
            $this->editMode = true;
            $this->name = $agent->name;
            $this->email = $agent->email;
            $this->agentCode = $agent->agent_code;
            $this->accreditationLevel = $agent->accreditation_level;
            $this->isActive = $agent->is_active;
        }
    }

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->agent?->id)],
            'agentCode' => ['required', 'string', 'min:3', 'max:20', Rule::unique('users', 'agent_code')->ignore($this->agent?->id)],
            'accreditationLevel' => ['required', 'integer', 'min:1', 'max:8'],
        ];

        if ($this->editMode) {
            $rules['password'] = ['nullable', 'string', 'min:8'];
        } else {
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        return $rules;
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'agent est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'name.min' => 'Le nom doit contenir au moins 2 caractères.',
            'name.max' => 'Le nom ne peut pas dépasser 100 caractères.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'email.max' => 'L\'adresse e-mail ne peut pas dépasser 255 caractères.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'agentCode.required' => 'Le code agent est obligatoire.',
            'agentCode.string' => 'Le code agent doit être une chaîne de caractères.',
            'agentCode.min' => 'Le code agent doit contenir au moins 3 caractères.',
            'agentCode.max' => 'Le code agent ne peut pas dépasser 20 caractères.',
            'agentCode.unique' => 'Ce code agent est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'accreditationLevel.required' => 'Le niveau d\'accréditation est obligatoire.',
            'accreditationLevel.integer' => 'Le niveau d\'accréditation doit être un nombre entier.',
            'accreditationLevel.min' => 'Le niveau d\'accréditation minimum est 1.',
            'accreditationLevel.max' => 'Le niveau d\'accréditation maximum est 8.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->editMode) {
                $data = [
                    'name' => $this->name,
                    'email' => $this->email,
                    'agent_code' => $this->agentCode,
                    'accreditation_level' => $this->accreditationLevel,
                    'is_active' => $this->isActive,
                ];

                if ($this->password) {
                    $data['password'] = Hash::make($this->password);
                }

                $this->agent->update($data);

                app(ActivityLogger::class)->log(
                    'updated',
                    "Agent {$this->agent->agent_code} ({$this->agent->name}) modifié par le Directeur G.",
                    auth()->id(),
                    $this->agent,
                );

                session()->flash('toast-success', 'Agent modifié avec succès.');

                $this->redirect(
                    route('admin.agents.show', $this->agent),
                    navigate: false,
                );
            } else {
                $agent = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'agent_code' => $this->agentCode,
                    'password' => Hash::make($this->password),
                    'role' => UserRole::Agent,
                    'accreditation_level' => $this->accreditationLevel,
                    'is_active' => true,
                ]);

                app(ActivityLogger::class)->log(
                    'created',
                    "Agent {$agent->agent_code} ({$agent->name}) créé par le Directeur G.",
                    auth()->id(),
                    $agent,
                );

                session()->flash('toast-success', 'Agent créé avec succès.');

                $this->redirect(
                    route('admin.agents.show', $agent),
                    navigate: false,
                );
            }
        } catch (\Throwable $e) {
            Log::error('Failed to save agent', [
                'exception' => $e,
                'agent_id' => $this->agent?->id,
            ]);
            $this->addError('form-save-failed', 'Impossible de sauvegarder l\'agent. Veuillez réessayer. Si le problème persiste, contactez le support.');
        }
    }

    public function render(): View
    {
        return view('livewire.admin.agent-form');
    }
}
