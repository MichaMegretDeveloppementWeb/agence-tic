<?php

namespace App\Http\Controllers;

use App\Enums\DocumentStatus;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('categories.index');
    }

    public function show(Category $category): View
    {
        $user = Auth::user();
        $category->loadCount(['reports', 'documents']);

        $reports = $category->reports()
            ->when(! $user->isDirectorG(), function ($query) use ($user) {
                $query->where('accreditation_level', '<=', $user->accreditation_level);
            })
            ->latest()
            ->limit(10)
            ->get();

        $documents = $category->documents()
            ->when(! $user->isDirectorG(), function ($query) use ($user) {
                $query->where('accreditation_level', '<=', $user->accreditation_level);
            })
            ->where('status', DocumentStatus::Active)
            ->latest()
            ->limit(10)
            ->get();

        return view('categories.show', [
            'category' => $category,
            'reports' => $reports,
            'documents' => $documents,
            'user' => $user,
        ]);
    }

    public function create(): View
    {
        abort_unless(Auth::user()->isDirectorG(), Response::HTTP_FORBIDDEN);

        return view('categories.create');
    }

    public function edit(Category $category): View
    {
        abort_unless(Auth::user()->isDirectorG(), Response::HTTP_FORBIDDEN);

        return view('categories.edit', ['category' => $category]);
    }
}
