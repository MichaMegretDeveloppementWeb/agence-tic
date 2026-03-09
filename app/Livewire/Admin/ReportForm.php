<?php

namespace App\Livewire\Admin;

use App\Enums\ReportStatus;
use App\Enums\ThreatLevel;
use App\Models\Category;
use App\Models\Report;
use App\Services\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ReportForm extends Component
{
    public ?Report $report = null;

    public bool $editMode = false;

    public string $code = '';

    public string $title = '';

    public string $categoryId = '';

    public string $threatLevel = 'low';

    public int $accreditationLevel = 1;

    public string $description = '';

    public string $procedures = '';

    public string $notes = '';

    public string $status = 'active';

    public function mount(?Report $report = null): void
    {
        if ($report?->exists) {
            $this->report = $report;
            $this->editMode = true;
            $this->code = $report->code;
            $this->title = $report->title;
            $this->categoryId = (string) $report->category_id;
            $this->threatLevel = $report->threat_level->value;
            $this->accreditationLevel = $report->accreditation_level;
            $this->description = $report->description ?? '';
            $this->procedures = $report->procedures ?? '';
            $this->notes = $report->notes ?? '';
            $this->status = $report->status->value;
        } else {
            $duplicateId = request()->query('duplicate');
            if ($duplicateId) {
                $source = Report::find($duplicateId);
                if ($source) {
                    $this->title = $source->title.' (copie)';
                    $this->categoryId = (string) $source->category_id;
                    $this->threatLevel = $source->threat_level->value;
                    $this->accreditationLevel = (string) $source->accreditation_level;
                    $this->description = $source->description ?? '';
                    $this->procedures = $source->procedures ?? '';
                    $this->notes = $source->notes ?? '';
                    $this->status = $source->status->value;
                }
            }
        }
    }

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        return [
            'code' => ['required', 'string', 'min:3', 'max:20', Rule::unique('reports', 'code')->ignore($this->report?->id)],
            'title' => ['required', 'string', 'min:5', 'max:200'],
            'categoryId' => ['required', 'exists:categories,id'],
            'threatLevel' => ['required', Rule::in(array_column(ThreatLevel::cases(), 'value'))],
            'accreditationLevel' => ['required', 'integer', 'min:1', 'max:8'],
            'description' => ['required', 'string', 'min:20'],
            'procedures' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(array_column(ReportStatus::cases(), 'value'))],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'code.required' => 'Le code du rapport est obligatoire.',
            'code.string' => 'Le code doit être une chaîne de caractères.',
            'code.min' => 'Le code doit contenir au moins 3 caractères.',
            'code.max' => 'Le code ne peut pas dépasser 20 caractères.',
            'code.unique' => 'Ce code de rapport est déjà utilisé.',
            'title.required' => 'Le titre du rapport est obligatoire.',
            'title.string' => 'Le titre doit être une chaîne de caractères.',
            'title.min' => 'Le titre doit contenir au moins 5 caractères.',
            'title.max' => 'Le titre ne peut pas dépasser 200 caractères.',
            'categoryId.required' => 'La catégorie est obligatoire.',
            'categoryId.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'threatLevel.required' => 'Le niveau de menace est obligatoire.',
            'threatLevel.in' => 'Le niveau de menace sélectionné n\'est pas valide.',
            'accreditationLevel.required' => 'Le niveau d\'accréditation est obligatoire.',
            'accreditationLevel.integer' => 'Le niveau d\'accréditation doit être un nombre entier.',
            'accreditationLevel.min' => 'Le niveau d\'accréditation minimum est 1.',
            'accreditationLevel.max' => 'Le niveau d\'accréditation maximum est 8.',
            'description.required' => 'La description est obligatoire.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.min' => 'La description doit contenir au moins 20 caractères.',
            'procedures.string' => 'Les procédures doivent être une chaîne de caractères.',
            'notes.string' => 'Les notes doivent être une chaîne de caractères.',
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut sélectionné n\'est pas valide.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'code' => $this->code,
                'title' => $this->title,
                'category_id' => $this->categoryId,
                'threat_level' => $this->threatLevel,
                'accreditation_level' => $this->accreditationLevel,
                'description' => $this->description,
                'procedures' => $this->procedures ?: null,
                'notes' => $this->notes ?: null,
                'status' => $this->status,
            ];

            if ($this->editMode) {
                $this->report->update($data);

                app(ActivityLogger::class)->log(
                    'updated',
                    "Rapport {$this->report->code} ({$this->report->title}) modifié.",
                    auth()->id(),
                    $this->report,
                );

                session()->flash('toast-success', 'Rapport modifié avec succès.');

                $this->redirect(route('reports.show', $this->report));
            } else {
                $report = Report::create($data);

                app(ActivityLogger::class)->log(
                    'created',
                    "Rapport {$report->code} ({$report->title}) créé.",
                    auth()->id(),
                    $report,
                );

                session()->flash('toast-success', 'Rapport créé avec succès.');

                $this->redirect(route('reports.show', $report));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to save report', [
                'exception' => $e,
                'report_id' => $this->report?->id,
            ]);
            $this->addError('form-save-failed', 'Impossible de sauvegarder le rapport. Veuillez réessayer. Si le problème persiste, contactez le support.');
        }
    }

    public function render(): View
    {
        return view('livewire.admin.report-form', [
            'categories' => Category::orderBy('name')->get(),
            'threatLevels' => ThreatLevel::cases(),
            'statuses' => ReportStatus::cases(),
        ]);
    }
}
