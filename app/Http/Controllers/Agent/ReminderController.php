<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ReminderController extends Controller
{
    public function index(): View
    {
        return view('agent.reminders.index');
    }

    public function create(): View
    {
        return view('agent.reminders.create');
    }
}
