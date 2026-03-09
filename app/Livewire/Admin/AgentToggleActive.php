<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class AgentToggleActive extends Component
{
    public User $agent;

    public function toggle(): void
    {
        try {
            $this->agent->update(['is_active' => ! $this->agent->is_active]);

            $status = $this->agent->is_active ? 'activé' : 'désactivé';

            app(ActivityLogger::class)->log(
                'updated',
                "Agent {$this->agent->agent_code} ({$this->agent->name}) {$status} par le Directeur G.",
                auth()->id(),
                $this->agent,
            );

            $this->dispatch('toast', type: 'success', title: 'Succès', description: "Agent {$status} avec succès.");
        } catch (\Throwable $e) {
            Log::error('Failed to toggle agent active status', [
                'exception' => $e,
                'agent_id' => $this->agent->id,
            ]);
            $this->dispatch('toast', type: 'error', title: 'Erreur', description: 'Impossible de modifier le statut de l\'agent. Veuillez réessayer. Si le problème persiste, contactez le support.');
        }
    }

    public function render(): View
    {
        return view('livewire.admin.agent-toggle-active');
    }
}
