<?php

namespace App\Livewire\Agent;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class PasswordForm extends Component
{
    public string $currentPassword = '';

    public string $newPassword = '';

    public string $newPasswordConfirmation = '';

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        return [
            'currentPassword' => ['required', 'current_password'],
            'newPassword' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'currentPassword.required' => 'Le mot de passe actuel est obligatoire.',
            'currentPassword.current_password' => 'Le mot de passe actuel est incorrect.',
            'newPassword.required' => 'Le nouveau mot de passe est obligatoire.',
            'newPassword.string' => 'Le nouveau mot de passe doit être une chaîne de caractères.',
            'newPassword.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'newPassword.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        try {
            $user = Auth::user();
            $user->update([
                'password' => Hash::make($this->newPassword),
            ]);

            $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation']);

            $this->dispatch('toast', type: 'success', title: 'Succès', description: 'Mot de passe modifié avec succès.');
        } catch (\Throwable $e) {
            Log::error('Failed to update password', [
                'exception' => $e,
                'user_id' => Auth::id(),
            ]);
            $this->addError('password-save-failed', 'Impossible de modifier le mot de passe. Veuillez réessayer. Si le problème persiste, contactez le support.');
        }
    }

    public function render(): View
    {
        return view('livewire.agent.password-form');
    }
}
