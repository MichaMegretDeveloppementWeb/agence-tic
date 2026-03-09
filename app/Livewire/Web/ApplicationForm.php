<?php

namespace App\Livewire\Web;

use App\Mail\NewApplicationNotification;
use App\Models\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Component;

class ApplicationForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $motivation = '';

    public string $experience = '';

    public bool $submitted = false;

    public string $trackingCode = '';

    /** @return array<string, array<string>> */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'motivation' => ['required', 'string', 'min:50', 'max:2000'],
            'experience' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'name.required' => 'Le nom ou pseudonyme est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'name.min' => 'Le nom doit contenir au moins 2 caractères.',
            'name.max' => 'Le nom ne peut pas dépasser 100 caractères.',
            'email.required' => "L'adresse e-mail est obligatoire.",
            'email.email' => "L'adresse e-mail n'est pas valide.",
            'email.max' => "L'adresse e-mail ne peut pas dépasser 255 caractères.",
            'motivation.required' => 'Le texte de motivation est obligatoire.',
            'motivation.string' => 'La motivation doit être une chaîne de caractères.',
            'motivation.min' => 'La motivation doit contenir au moins 50 caractères.',
            'motivation.max' => 'La motivation ne peut pas dépasser 2000 caractères.',
            'experience.string' => "L'expérience doit être une chaîne de caractères.",
            'experience.max' => "L'expérience ne peut pas dépasser 1000 caractères.",
        ];
    }

    public function submit(): void
    {
        $validated = $this->validate();

        $key = 'application-submit:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('application-submit-throttled', "Trop de soumissions. Veuillez réessayer dans {$seconds} secondes.");

            return;
        }

        RateLimiter::hit($key, 3600);

        try {
            $trackingCode = $this->generateTrackingCode();
            $validated['tracking_code'] = $trackingCode;

            $application = Application::create($validated);

            Mail::to(config('app.director_email', 'directeur@agence-tic.fr'))
                ->queue(new NewApplicationNotification($application));

            $this->trackingCode = $trackingCode;
            $this->submitted = true;
        } catch (\Throwable $e) {
            Log::error('Failed to save application', [
                'exception' => $e,
                'email' => $this->email,
            ]);

            $this->addError('application-submit-failed', "Impossible d'envoyer votre candidature. Veuillez réessayer. Si le problème persiste, contactez le support.");
        }
    }

    private function generateTrackingCode(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        do {
            $suffix = '';
            for ($i = 0; $i < 8; $i++) {
                $suffix .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $code = 'TIC-' . $suffix;
        } while (Application::where('tracking_code', $code)->exists());

        return $code;
    }

    public function render(): View
    {
        return view('livewire.web.application-form');
    }
}
