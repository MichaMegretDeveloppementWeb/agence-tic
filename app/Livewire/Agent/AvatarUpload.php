<?php

namespace App\Livewire\Agent;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class AvatarUpload extends Component
{
    use WithFileUploads;

    public $photo;

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        return [
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'photo.image' => 'Le fichier doit être une image (PNG, JPG, GIF, etc.).',
            'photo.max' => 'L\'image ne peut pas dépasser 2 Mo.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        if (! $this->photo) {
            return;
        }

        try {
            $user = Auth::user();

            // Delete old avatar if exists
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $path = $this->photo->store('avatars', 'public');

            $user->update([
                'avatar_path' => $path,
            ]);

            $this->reset('photo');

            $this->dispatch('toast', type: 'success', title: 'Succès', description: 'Photo de profil mise à jour avec succès.');
        } catch (\Throwable $e) {
            Log::error('Failed to upload avatar', [
                'exception' => $e,
                'user_id' => Auth::id(),
            ]);
            $this->addError('avatar-save-failed', 'Impossible de mettre à jour la photo de profil. Veuillez réessayer. Si le problème persiste, contactez le support.');
        }
    }

    public function remove(): void
    {
        try {
            $user = Auth::user();

            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);

                $user->update([
                    'avatar_path' => null,
                ]);
            }

            $this->reset('photo');

            $this->dispatch('toast', type: 'success', title: 'Succès', description: 'Photo de profil supprimée avec succès.');
        } catch (\Throwable $e) {
            Log::error('Failed to remove avatar', [
                'exception' => $e,
                'user_id' => Auth::id(),
            ]);
            $this->addError('avatar-save-failed', 'Impossible de supprimer la photo de profil. Veuillez réessayer. Si le problème persiste, contactez le support.');
        }
    }

    public function render(): View
    {
        return view('livewire.agent.avatar-upload', [
            'user' => Auth::user(),
        ]);
    }
}
