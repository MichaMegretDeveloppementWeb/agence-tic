<?php

namespace App\Livewire\Agent;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ProfileForm extends Component
{
    public string $name = '';

    public string $email = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore(Auth::id())],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'name.min' => 'Le nom doit contenir au moins 2 caractères.',
            'name.max' => 'Le nom ne peut pas dépasser 100 caractères.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'email.max' => 'L\'adresse e-mail ne peut pas dépasser 255 caractères.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        try {
            $user = Auth::user();
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
            ]);

            $this->dispatch('toast', type: 'success', title: 'Succès', description: 'Profil mis à jour avec succès.');
        } catch (\Throwable $e) {
            Log::error('Failed to update profile', [
                'exception' => $e,
                'user_id' => Auth::id(),
            ]);
            $this->addError('profile-save-failed', 'Impossible de mettre à jour le profil. Veuillez réessayer. Si le problème persiste, contactez le support.');
        }
    }

    public function render(): View
    {
        return view('livewire.agent.profile-form', [
            'user' => Auth::user(),
        ]);
    }
}
