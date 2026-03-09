<?php

namespace App\Livewire\Auth;

use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

class LoginForm extends Component
{
    public string $agent_code = '';

    public string $password = '';

    public bool $remember = false;

    /** @return array<string, array<string>> */
    protected function rules(): array
    {
        return [
            'agent_code' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'agent_code.required' => "L'identifiant d'agent est obligatoire.",
            'agent_code.string' => "L'identifiant d'agent doit être une chaîne de caractères.",
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
        ];
    }

    public function authenticate(): void
    {
        $this->validate();

        $throttleKey = Str::lower($this->agent_code) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            $this->addError(
                'login-throttled',
                "Trop de tentatives de connexion. Veuillez réessayer dans {$seconds} secondes."
            );

            return;
        }

        $authenticated = Auth::attempt(
            ['agent_code' => $this->agent_code, 'password' => $this->password],
            $this->remember,
        );

        if (! $authenticated) {
            RateLimiter::hit($throttleKey, 60);

            $this->addError('login-failed', 'Identifiant ou mot de passe incorrect.');

            return;
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();

            $this->addError('login-failed', 'Votre compte a été désactivé. Contactez le Directeur G.');

            return;
        }

        RateLimiter::clear($throttleKey);
        session()->regenerate();

        Log::info('Agent connected', [
            'agent_code' => $user->agent_code,
            'ip' => request()->ip(),
        ]);

        app(ActivityLogger::class)->log('login', "{$user->agent_code} s'est connecté.", $user->id, $user);

        $this->redirect(route('dashboard'));
    }

    public function render(): View
    {
        return view('livewire.auth.login-form');
    }
}
