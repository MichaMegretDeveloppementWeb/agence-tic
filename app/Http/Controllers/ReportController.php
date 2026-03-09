<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\UserRead;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index');
    }

    public function show(Report $report): View
    {
        $user = Auth::user();

        abort_unless(
            $user->canAccess($report->accreditation_level, Report::class, $report->id),
            Response::HTTP_FORBIDDEN,
            'Vous n\'avez pas l\'accréditation nécessaire pour consulter ce rapport.'
        );

        UserRead::markAsRead($user->id, Report::class, $report->id);

        $report->load([
            'category',
            'documents.uploader',
            'activityEntries' => fn ($q) => $q->with('user')->latest()->limit(20),
        ]);

        return view('reports.show', [
            'report' => $report,
            'user' => $user,
        ]);
    }

    public function create(): View
    {
        return view('reports.create');
    }

    public function edit(Report $report): View
    {
        $report->load('category');

        return view('reports.edit', ['report' => $report]);
    }
}
