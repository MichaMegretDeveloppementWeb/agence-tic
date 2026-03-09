<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\UserRead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LibraryController extends Controller
{
    public function index(): View
    {
        return view('library.index');
    }

    public function show(Document $document): View
    {
        $user = Auth::user();

        abort_unless(
            $user->canAccess($document->accreditation_level, Document::class, $document->id),
            Response::HTTP_FORBIDDEN,
            'Vous n\'avez pas l\'accréditation nécessaire pour consulter ce document.'
        );

        UserRead::markAsRead($user->id, Document::class, $document->id);

        $document->load(['category', 'uploader', 'report']);

        return view('library.show', [
            'document' => $document,
            'user' => $user,
        ]);
    }

    public function create(): View
    {
        return view('library.create');
    }

    public function edit(Document $document): View
    {
        $document->load(['category', 'report']);

        return view('library.edit', ['document' => $document]);
    }

    public function download(Document $document): StreamedResponse
    {
        $user = Auth::user();

        abort_unless(
            $user->canAccess($document->accreditation_level, Document::class, $document->id),
            Response::HTTP_FORBIDDEN,
            'Vous n\'avez pas l\'accréditation nécessaire pour télécharger ce document.'
        );

        abort_unless(
            Storage::disk('private')->exists($document->file_path),
            Response::HTTP_NOT_FOUND,
            'Le fichier demandé est introuvable.'
        );

        return Storage::disk('private')->download(
            $document->file_path,
            $document->file_name
        );
    }

    public function preview(Document $document): \Symfony\Component\HttpFoundation\Response
    {
        $user = Auth::user();

        abort_unless(
            $user->canAccess($document->accreditation_level, Document::class, $document->id),
            Response::HTTP_FORBIDDEN,
            'Vous n\'avez pas l\'accréditation nécessaire pour consulter ce document.'
        );

        abort_unless(
            Storage::disk('private')->exists($document->file_path),
            Response::HTTP_NOT_FOUND,
            'Le fichier demandé est introuvable.'
        );

        $previewableTypes = [
            'application/pdf',
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword',
            'text/plain', 'text/csv', 'text/html', 'text/markdown',
            'application/rtf',
        ];

        abort_unless(
            in_array($document->mime_type, $previewableTypes),
            Response::HTTP_NOT_FOUND,
            'Ce type de fichier ne supporte pas l\'aperçu.'
        );

        return Storage::disk('private')->response($document->file_path, $document->file_name, [
            'Content-Type' => $document->mime_type,
            'Content-Disposition' => 'inline; filename="'.$document->file_name.'"',
        ]);
    }
}
