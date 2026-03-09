<?php

namespace App\Http\Controllers\Agent;

use App\Enums\ApplicationStatus;
use App\Enums\ReminderType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ActivityEntry;
use App\Models\Application;
use App\Models\Document;
use App\Models\Reminder;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();

        $reminders = Reminder::query()
            ->where('is_completed', false)
            ->where(function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('type', ReminderType::Personal)
                        ->where('created_by', $user->id);
                })
                    ->orWhere(function ($q) use ($user) {
                        $q->where('type', ReminderType::Targeted)
                            ->where('target_user_id', $user->id);
                    })
                    ->orWhere('type', ReminderType::Global);
            })
            ->latest()
            ->limit(5)
            ->get();

        $recentActivity = ActivityEntry::query()
            ->with('user')
            ->latest()
            ->limit(8)
            ->get();

        $reportsCount = Report::query()
            ->where('accreditation_level', '<=', $user->accreditation_level)
            ->count();

        $documentsCount = Document::query()
            ->where('accreditation_level', '<=', $user->accreditation_level)
            ->where('status', 'active')
            ->count();

        $viewData = [
            'user' => $user,
            'reminders' => $reminders,
            'recentActivity' => $recentActivity,
            'reportsCount' => $reportsCount,
            'documentsCount' => $documentsCount,
        ];

        if ($user->isDirectorG()) {
            $pendingApplications = Application::where('status', ApplicationStatus::Pending)->count();
            $totalAgents = User::where('role', UserRole::Agent)->count();
            $activeAgents = User::where('role', UserRole::Agent)->where('is_active', true)->count();
            $recentApplications = Application::where('status', ApplicationStatus::Pending)
                ->latest()
                ->limit(5)
                ->get();

            $viewData = array_merge($viewData, [
                'pendingApplications' => $pendingApplications,
                'totalAgents' => $totalAgents,
                'activeAgents' => $activeAgents,
                'recentApplications' => $recentApplications,
                'totalReports' => Report::count(),
                'totalDocuments' => Document::where('status', 'active')->count(),
            ]);
        }

        return view('agent.dashboard.index', $viewData);
    }
}
