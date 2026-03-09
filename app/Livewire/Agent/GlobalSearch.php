<?php

namespace App\Livewire\Agent;

use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\Report;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';

    /** @var array<int, array{type: string, id: int, title: string, subtitle: string, url: string}> */
    public array $results = [];

    public function updatedQuery(): void
    {
        if (strlen($this->query) < 2) {
            $this->results = [];

            return;
        }

        /** @var User $user */
        $user = Auth::user();
        $results = [];
        $search = $this->query;

        $this->searchReports($results, $search, $user);
        $this->searchDocuments($results, $search, $user);

        if ($user->isDirectorG()) {
            $this->searchAgents($results, $search);
        }

        $this->results = $results;
    }

    /**
     * @param  array<int, array{type: string, id: int, title: string, subtitle: string, url: string}>  &$results
     */
    private function searchReports(array &$results, string $search, User $user): void
    {
        $reports = Report::query()
            ->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            })
            ->when(! $user->isDirectorG(), function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->where('accreditation_level', '<=', $user->accreditation_level)
                        ->orWhereHas('specialPermissions', function ($sp) use ($user) {
                            $sp->where('user_id', $user->id);
                        });
                });
            })
            ->limit(5)
            ->get();

        foreach ($reports as $report) {
            $results[] = [
                'type' => 'Rapport',
                'id' => $report->id,
                'title' => $report->code,
                'subtitle' => $report->title,
                'url' => route('reports.show', $report),
            ];
        }
    }

    /**
     * @param  array<int, array{type: string, id: int, title: string, subtitle: string, url: string}>  &$results
     */
    private function searchDocuments(array &$results, string $search, User $user): void
    {
        $documents = Document::query()
            ->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%");
            })
            ->where('status', DocumentStatus::Active)
            ->when(! $user->isDirectorG(), function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->where('accreditation_level', '<=', $user->accreditation_level)
                        ->orWhereHas('specialPermissions', function ($sp) use ($user) {
                            $sp->where('user_id', $user->id);
                        });
                });
            })
            ->limit(5)
            ->get();

        foreach ($documents as $doc) {
            $results[] = [
                'type' => 'Document',
                'id' => $doc->id,
                'title' => $doc->title,
                'subtitle' => $doc->file_name,
                'url' => route('library.show', $doc),
            ];
        }
    }

    /**
     * @param  array<int, array{type: string, id: int, title: string, subtitle: string, url: string}>  &$results
     */
    private function searchAgents(array &$results, string $search): void
    {
        $agents = User::query()
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('agent_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->limit(5)
            ->get();

        foreach ($agents as $agent) {
            $results[] = [
                'type' => 'Agent',
                'id' => $agent->id,
                'title' => $agent->name,
                'subtitle' => $agent->agent_code.' — '.$agent->email,
                'url' => route('admin.agents.show', $agent),
            ];
        }
    }

    public function render(): View
    {
        return view('livewire.agent.global-search');
    }
}
