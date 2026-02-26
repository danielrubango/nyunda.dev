<?php

namespace App\Http\Controllers\Dashboard;

use App\Actions\Content\CreateDashboardContentSubmission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreDashboardContentRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserContentController extends Controller
{
    public function create(): View
    {
        /** @var User $user */
        $user = auth()->user();

        return view('dashboard.content.create', [
            'supportedLocales' => config('app.supported_locales', ['fr', 'en']),
            'defaultLocale' => $user->preferred_locale,
        ]);
    }

    public function store(
        StoreDashboardContentRequest $request,
        CreateDashboardContentSubmission $createDashboardContentSubmission,
    ): RedirectResponse {
        /** @var User $user */
        $user = $request->user();

        $createDashboardContentSubmission->handle(
            author: $user,
            submission: $request->submissionData(),
        );

        return redirect()
            ->route('dashboard')
            ->with('status', 'Contenu soumis avec succes.');
    }
}
