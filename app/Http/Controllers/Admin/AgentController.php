<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class AgentController extends Controller
{
    public function index(): View
    {
        return view('admin.agents.index');
    }

    public function create(): View
    {
        return view('admin.agents.create');
    }

    public function show(User $user): View
    {
        abort_unless($user->role === UserRole::Agent, 404);
        $user->load(['specialPermissions.permissionable', 'activityEntries' => fn ($q) => $q->latest()->limit(10)]);

        return view('admin.agents.show', ['agent' => $user]);
    }

    public function edit(User $user): View
    {
        abort_unless($user->role === UserRole::Agent, 404);

        return view('admin.agents.edit', ['agent' => $user]);
    }
}
