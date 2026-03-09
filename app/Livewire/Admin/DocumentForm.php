<?php

namespace App\Livewire\Admin;

use App\Enums\DocumentStatus;
use App\Models\Category;
use App\Models\Document;
use App\Models\Report;
use App\Services\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentForm extends Component
{
    use WithFileUploads;

    public ?Document $document = null;

    public bool $editMode = false;

    public string $title = '';

    public string $categoryId = '';

    public int $accreditationLevel = 1;

    public string $status = 'active';

    public string $reportId = '';

    public string $notes = '';

    public $file;

    public function mount(?Document $document = null): void
    {
        if ($document?->exists) {
            $this->document = $document;
            $this->editMode = true;
            $this->title = $document->title;
            $this->categoryId = (string) $document->category_id;
            $this->accreditationLevel = $document->accreditation_level;
            $this->status = $document->status->value;
            $this->reportId = $document->report_id ? (string) $document->report_id : '';
            $this->notes = $document->notes ?? '';
        } else {
            $preselectedReport = request()->query('report');
            if ($preselectedReport && Report::where('id', $preselectedReport)->exists()) {
                $this->reportId = (string) $preselectedReport;
            }
        }
    }

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'min:3', 'max:200'],
            'categoryId' => ['required', 'exists:categories,id'],
            'accreditationLevel' => ['required', 'integer', 'min:1', 'max:8'],
            'status' => ['required', 'in:active,archived,hidden'],
            'reportId' => ['nullable', 'exists:reports,id'],
            'notes' => ['nullable', 'string'],
        ];

        if ($this->editMode) {
            $rules['file'] = ['nullable', 'file', 'max:10240'];
        } else {
            $rules['file'] = ['required', 'file', 'max:10240'];
        }

        return $rules;
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'title.required' => 'Le titre du document est obligatoire.',
            'title.string' => 'Le titre doit être une chaîne de caractères.',
            'title.min' => 'Le titre doit contenir au moins 3 caractères.',
            'title.max' => 'Le titre ne peut pas dépasser 200 caractères.',
            'categoryId.required' => 'La catégorie est obligatoire.',
            'categoryId.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'accreditationLevel.required' => 'Le niveau d\'accréditation est obligatoire.',
            'accreditationLevel.integer' => 'Le niveau d\'accréditation doit être un nombre entier.',
            'accreditationLevel.min' => 'Le niveau d\'accréditation minimum est 1.',
            'accreditationLevel.max' => 'Le niveau d\'accréditation maximum est 8.',
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut sélectionné n\'est pas valide.',
            'reportId.exists' => 'Le rapport sélectionné n\'existe pas.',
            'notes.string' => 'Les notes doivent être une chaîne de caractères.',
            'file.required' => 'Le fichier est obligatoire.',
            'file.file' => 'Le fichier téléversé n\'est pas valide.',
            'file.max' => 'Le fichier ne peut pas dépasser 10 Mo.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->editMode) {
                $data = [
                    'title' => $this->title,
                    'category_id' => $this->categoryId,
                    'accreditation_level' => $this->accreditationLevel,
                    'status' => $this->status,
                    'report_id' => $this->reportId ?: null,
                    'notes' => $this->notes ?: null,
                ];

                if ($this->file) {
                    if ($this->document->file_path) {
                        Storage::disk('private')->delete($this->document->file_path);
                    }

                    $path = $this->file->store('documents', 'private');
                    $data['file_path'] = $path;
                    $data['file_name'] = $this->file->getClientOriginalName();
                    $data['mime_type'] = $this->file->getMimeType();
                    $data['file_size'] = $this->file->getSize();
                }

                $this->document->update($data);

                app(ActivityLogger::class)->log(
                    'updated',
                    "Document « {$this->document->title} » modifié.",
                    auth()->id(),
                    $this->document,
                );

                session()->flash('toast-success', 'Document modifié avec succès.');

                $this->redirect(route('library.show', $this->document));
            } else {
                $path = $this->file->store('documents', 'private');

                $document = Document::create([
                    'title' => $this->title,
                    'file_path' => $path,
                    'file_name' => $this->file->getClientOriginalName(),
                    'mime_type' => $this->file->getMimeType(),
                    'file_size' => $this->file->getSize(),
                    'category_id' => $this->categoryId,
                    'accreditation_level' => $this->accreditationLevel,
                    'uploaded_by' => Auth::id(),
                    'status' => $this->status,
                    'report_id' => $this->reportId ?: null,
                    'notes' => $this->notes ?: null,
                ]);

                app(ActivityLogger::class)->log(
                    'created',
                    "Document « {$document->title} » ajouté.",
                    auth()->id(),
                    $document,
                );

                session()->flash('toast-success', 'Document ajouté avec succès.');

                $this->redirect(route('library.show', $document));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to save document', [
                'exception' => $e,
                'document_id' => $this->document?->id,
            ]);
            $this->addError('form-save-failed', 'Impossible de sauvegarder le document. Veuillez réessayer. Si le problème persiste, contactez le support.');
        }
    }

    public function render(): View
    {
        return view('livewire.admin.document-form', [
            'categories' => Category::orderBy('name')->get(),
            'reports' => Report::orderBy('code')->get(['id', 'code', 'title']),
            'statuses' => DocumentStatus::cases(),
        ]);
    }
}
