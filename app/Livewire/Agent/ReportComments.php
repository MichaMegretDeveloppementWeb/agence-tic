<?php

namespace App\Livewire\Agent;

use App\Models\ActivityEntry;
use App\Models\Report;
use App\Models\ReportComment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ReportComments extends Component
{
    public Report $report;

    public string $newComment = '';

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        return [
            'newComment' => ['required', 'string', 'min:3', 'max:2000'],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'newComment.required' => 'Le commentaire est obligatoire.',
            'newComment.string' => 'Le commentaire doit être une chaîne de caractères.',
            'newComment.min' => 'Le commentaire doit contenir au moins 3 caractères.',
            'newComment.max' => 'Le commentaire ne peut pas dépasser 2000 caractères.',
        ];
    }

    public function addComment(): void
    {
        $this->validate();

        $user = Auth::user();

        try {
            ReportComment::create([
                'report_id' => $this->report->id,
                'user_id' => $user->id,
                'content' => $this->newComment,
            ]);

            ActivityEntry::create([
                'user_id' => $user->id,
                'subject_type' => Report::class,
                'subject_id' => $this->report->id,
                'event_type' => 'commented',
                'message' => 'A ajouté un commentaire sur le rapport.',
            ]);

            $this->reset('newComment');
            $this->dispatch('toast', type: 'success', title: 'Succès', description: 'Commentaire ajouté avec succès.');
        } catch (\Throwable $e) {
            Log::error('Failed to add report comment', [
                'exception' => $e,
                'report_id' => $this->report->id,
                'user_id' => $user->id,
            ]);
            $this->addError('comment-creation-failed', 'Impossible d\'ajouter le commentaire. Veuillez réessayer. Si le problème persiste, contactez le support.');
        }
    }

    public function deleteComment(int $commentId): void
    {
        $user = Auth::user();

        $comment = ReportComment::query()
            ->where('id', $commentId)
            ->where('report_id', $this->report->id)
            ->first();

        if (! $comment) {
            return;
        }

        if ($comment->user_id !== $user->id && ! $user->isDirectorG()) {
            return;
        }

        try {
            $comment->delete();
            $this->dispatch('toast', type: 'success', title: 'Succès', description: 'Commentaire supprimé avec succès.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete report comment', [
                'exception' => $e,
                'comment_id' => $commentId,
                'report_id' => $this->report->id,
                'user_id' => $user->id,
            ]);
            $this->addError('comment-deletion-failed', 'Impossible de supprimer le commentaire. Veuillez réessayer.');
        }
    }

    public function render(): View
    {
        $comments = $this->report->comments()
            ->with('user')
            ->latest()
            ->get();

        return view('livewire.agent.report-comments', [
            'comments' => $comments,
        ]);
    }
}
