<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Services\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CategoryForm extends Component
{
    public ?Category $category = null;

    public bool $editMode = false;

    public string $name = '';

    public string $description = '';

    public function mount(?Category $category = null): void
    {
        if ($category?->exists) {
            $this->category = $category;
            $this->editMode = true;
            $this->name = $category->name;
            $this->description = $category->description ?? '';
        }
    }

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100', Rule::unique('categories', 'name')->ignore($this->category?->id)],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'name.min' => 'Le nom doit contenir au moins 2 caractères.',
            'name.max' => 'Le nom ne peut pas dépasser 100 caractères.',
            'name.unique' => 'Ce nom de catégorie est déjà utilisé.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description ne peut pas dépasser 500 caractères.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->editMode) {
                $this->category->update([
                    'name' => $this->name,
                    'slug' => Str::slug($this->name),
                    'description' => $this->description ?: null,
                ]);

                app(ActivityLogger::class)->log('updated', "Catégorie « {$this->name} » modifiée.", auth()->id(), $this->category);

                session()->flash('toast-success', 'Catégorie modifiée avec succès.');

                $this->redirect(route('categories.show', $this->category));
            } else {
                $category = Category::create([
                    'name' => $this->name,
                    'slug' => Str::slug($this->name),
                    'description' => $this->description ?: null,
                ]);

                app(ActivityLogger::class)->log('created', "Catégorie « {$this->name} » créée.", auth()->id(), $category);

                session()->flash('toast-success', 'Catégorie créée avec succès.');

                $this->redirect(route('categories.show', $category));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to save category', ['exception' => $e]);
            $this->addError('form-save-failed', 'Impossible de sauvegarder la catégorie. Veuillez réessayer. Si le problème persiste, contactez le support.');
        }
    }

    public function delete(): void
    {
        if (! $this->category) {
            return;
        }

        $this->category->loadCount(['reports', 'documents']);

        if ($this->category->reports_count > 0 || $this->category->documents_count > 0) {
            $this->addError('form-save-failed', 'Impossible de supprimer cette catégorie car elle contient des rapports ou documents.');

            return;
        }

        try {
            $categoryName = $this->category->name;
            $this->category->delete();

            app(ActivityLogger::class)->log('deleted', "Catégorie « {$categoryName} » supprimée.", auth()->id());

            session()->flash('toast-success', 'Catégorie supprimée avec succès.');

            $this->redirect(route('categories.index'));
        } catch (\Throwable $e) {
            Log::error('Failed to delete category', ['exception' => $e, 'category_id' => $this->category->id]);
            $this->addError('form-save-failed', 'Impossible de supprimer la catégorie. Veuillez réessayer.');
        }
    }

    public function render(): View
    {
        return view('livewire.admin.category-form');
    }
}
