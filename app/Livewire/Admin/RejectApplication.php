<?php

namespace App\Livewire\Admin;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Services\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class RejectApplication extends Component
{
    public Application $application;

    public function reject(): void
    {
        try {
            $this->application->update(['status' => ApplicationStatus::Rejected]);

            app(ActivityLogger::class)->log(
                'updated',
                "Candidature de {$this->application->name} refusée par le Directeur G.",
                auth()->id(),
                $this->application,
            );

            session()->flash('toast-success', 'Candidature refusée avec succès.');

            $this->redirect(route('admin.applications.index'));
        } catch (\Throwable $e) {
            Log::error('Failed to reject application', [
                'exception' => $e,
                'application_id' => $this->application->id,
            ]);

            $this->addError(
                'application-reject-failed',
                'Impossible de refuser la candidature. Veuillez réessayer. Si le problème persiste, contactez le support.',
            );
        }
    }

    public function render(): View
    {
        return view('livewire.admin.reject-application');
    }
}
