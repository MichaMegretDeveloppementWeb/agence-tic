<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Models\Document;
use App\Models\Report;
use App\Models\SpecialPermission;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class PermissionManager extends Component
{
    use WithPagination;

    public string $search = '';

    public int $perPage = 15;

    public bool $showGrantForm = false;

    public string $agentId = '';

    public string $permissionableType = '';

    public string $permissionableId = '';

    /** @return array<string, array<string>> */
    protected function rules(): array
    {
        return [
            'agentId' => ['required', 'exists:users,id'],
            'permissionableType' => ['required', 'in:report,document'],
            'permissionableId' => ['required', 'integer'],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'agentId.required' => 'L\'agent est obligatoire.',
            'agentId.exists' => 'L\'agent sélectionné n\'existe pas.',
            'permissionableType.required' => 'Le type de ressource est obligatoire.',
            'permissionableType.in' => 'Le type de ressource doit être un rapport ou un document.',
            'permissionableId.required' => 'La ressource est obligatoire.',
            'permissionableId.integer' => 'La ressource sélectionnée n\'est pas valide.',
        ];
    }

    public function updatedPermissionableType(): void
    {
        $this->permissionableId = '';
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function openGrantForm(): void
    {
        $this->resetValidation();
        $this->reset(['agentId', 'permissionableType', 'permissionableId']);
        $this->showGrantForm = true;
    }

    public function closeGrantForm(): void
    {
        $this->showGrantForm = false;
    }

    public function grant(): void
    {
        $this->validate();

        $morphType = $this->permissionableType === 'report' ? Report::class : Document::class;
        $resource = $morphType::find($this->permissionableId);

        if (! $resource) {
            $this->addError('permission-grant-failed', 'La ressource sélectionnée n\'existe pas.');

            return;
        }

        $exists = SpecialPermission::query()
            ->where('user_id', $this->agentId)
            ->where('permissionable_type', $morphType)
            ->where('permissionable_id', $this->permissionableId)
            ->exists();

        if ($exists) {
            $this->addError('permission-grant-failed', 'Cet agent possède déjà cette permission.');

            return;
        }

        try {
            $permission = SpecialPermission::create([
                'user_id' => $this->agentId,
                'permissionable_type' => $morphType,
                'permissionable_id' => $this->permissionableId,
                'granted_by' => Auth::id(),
            ]);

            $agent = User::find($this->agentId);
            $resourceType = $this->permissionableType === 'report' ? 'rapport' : 'document';
            $resourceName = $resource->code ?? $resource->title;
            app(ActivityLogger::class)->log('created', "Permission spéciale accordée à {$agent->agent_code} sur {$resourceType} {$resourceName}.", auth()->id(), $permission);

            $this->closeGrantForm();
            $this->dispatch('toast', type: 'success', title: 'Succès', description: 'Permission accordée avec succès.');
        } catch (\Throwable $e) {
            Log::error('Failed to grant permission', ['exception' => $e]);
            $this->addError('permission-grant-failed', 'Impossible d\'accorder la permission. Veuillez réessayer. Si le problème persiste, contactez le support.');
        }
    }

    public function revoke(int $permissionId): void
    {
        $permission = SpecialPermission::find($permissionId);

        if (! $permission) {
            return;
        }

        try {
            $agentCode = $permission->user->agent_code ?? 'inconnu';
            $permission->delete();
            app(ActivityLogger::class)->log('deleted', "Permission spéciale révoquée pour {$agentCode}.", auth()->id(), $permission);
            $this->dispatch('toast', type: 'success', title: 'Succès', description: 'Permission révoquée avec succès.');
        } catch (\Throwable $e) {
            Log::error('Failed to revoke permission', ['exception' => $e, 'permission_id' => $permissionId]);
            $this->addError('permission-revoke-failed', 'Impossible de révoquer la permission. Veuillez réessayer.');
        }
    }

    public function render(): View
    {
        $permissions = SpecialPermission::query()
            ->with(['user', 'permissionable', 'grantedBy'])
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('agent_code', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate($this->perPage);

        $agents = User::query()
            ->where('role', UserRole::Agent)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $reports = Report::orderBy('code')->get(['id', 'code', 'title']);
        $documents = Document::orderBy('title')->get(['id', 'title', 'file_name']);

        return view('livewire.admin.permission-manager', [
            'permissions' => $permissions,
            'agents' => $agents,
            'reports' => $reports,
            'documents' => $documents,
        ]);
    }
}
