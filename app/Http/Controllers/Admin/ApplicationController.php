<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(): View
    {
        return view('admin.applications.index');
    }

    public function show(Application $application): View
    {
        return view('admin.applications.show', ['application' => $application]);
    }
}
