<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class InvitationsController extends Controller
{
    /**
     * @param Project $project
     * @param Request $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Project $project, Request $request): RedirectResponse
    {
        $this->authorize('manage', $project);
        $request->validate([
            'email' => ['required', 'exists:users,email']
        ], [
            'email.exists' => __('app.the_invited_email_address_must_be_associated_with_birdboard_account')
        ]);
        $user = User::whereEmail($request->get('email'))->first();
        $project->invite($user);

        return redirect($project->path());
    }
}
