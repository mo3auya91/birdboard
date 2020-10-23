<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        $projects = auth('web')->user()->projects;
        return Inertia::render('Project/Home', ['projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Project/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        //persist
        $project = auth('web')->user()->projects()->create($this->validateRequest($request));

        //redirect
        return redirect($project->path());
        //return redirect()->route('projects.show', ['project' => $project->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param Project $project
     * @return Response
     * @throws AuthorizationException
     */
    public function show(Project $project): Response
    {
        $this->authorize('update', $project);
        $project = Project::with('tasks')->findOrFail($project->id);
        return Inertia::render('Project/Show', ['project' => $project]);
        //return view('projects.show', ['project' => $project]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Project $project
     * @return Response
     */
    public function edit(Project $project)
    {
        return Inertia::render('Project/Edit', ['project' => $project]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Project $project
     * @param Request $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Project $project, Request $request): RedirectResponse
    {
        $this->authorize('update', $project);
        //persist
        $project->update($this->validateRequest($request));
        //redirect
        return redirect($project->path());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Project $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        //
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'title' => ['required'],
            'description' => ['required'],
            'notes' => ['nullable'],
        ]);
    }
}
