<?php

namespace App\Livewire\Admin;

use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AcceptApplication extends Component
{
    public Application $application;

    public string $agentCode = '';

    public string $password = '';

    public int $accreditationLevel = 1;

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        return [
            'agentCode' => ['required', 'string', 'min:3', 'max:20', Rule::unique('users', 'agent_code')],
            'password' => ['required', 'string', 'min:8'],
            'accreditationLevel' => ['required', 'integer', 'min:1', 'max:8'],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
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

    public function accept(): void
    {
        $this->validate();

        try {
            $agent = DB::transaction(function (): User {
                $agent = User::create([
                    'name' => $this->application->name,
                    'email' => $this->application->email,
                    'agent_code' => $this->agentCode,
                    'password' => Hash::make($this->password),
                    'role' => UserRole::Agent,
                    'accreditation_level' => $this->accreditationLevel,
                    'is_active' => true,
                ]);

                $this->application->update(['status' => ApplicationStatus::Accepted]);

                app(ActivityLogger::class)->log(
                    'created',
                    "Candidature de {$this->application->name} acceptée et agent {$this->agentCode} créé par le Directeur G.",
                    auth()->id(),
                    $this->application,
                );

                return $agent;
            });

            session()->flash('toast-success', 'Candidature acceptée et agent créé avec succès.');

            $this->redirect(route('admin.agents.show', $agent));
        } catch (\Throwable $e) {
            Log::error('Failed to accept application', [
                'exception' => $e,
                'application_id' => $this->application->id,
            ]);

            $this->addError(
                'application-accept-failed',
                'Impossible d\'accepter la candidature. Veuillez réessayer. Si le problème persiste, contactez le support.',
            );
        }
    }

    public function render(): View
    {
        return view('livewire.admin.accept-application');
    }
}
