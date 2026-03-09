<?php

namespace App\Livewire\Web;

use App\Models\Application;
use Illuminate\View\View;
use Livewire\Component;

class ApplicationTracker extends Component
{
    public string $trackingCode = '';

    public ?Application $application = null;

    public bool $searched = false;

    /** @return array<string, array<string>> */
    protected function rules(): array
    {
        return [
            'trackingCode' => ['required', 'string', 'size:12'],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'trackingCode.required' => 'Le code de suivi est obligatoire.',
            'trackingCode.string' => 'Le code de suivi doit être une chaîne de caractères.',
            'trackingCode.size' => 'Le code de suivi doit contenir exactement 12 caractères (ex : TIC-A3B7K9M2).',
        ];
    }

    public function search(): void
    {
        $this->searched = false;
        $this->application = null;

        $this->validate();

        $this->application = Application::where('tracking_code', strtoupper($this->trackingCode))->first();
        $this->searched = true;

        if (! $this->application) {
            $this->addError('tracking-not-found', 'Aucune candidature trouvée avec ce code de suivi.');
        }
    }

    public function render(): View
    {
        return view('livewire.web.application-tracker');
    }
}
