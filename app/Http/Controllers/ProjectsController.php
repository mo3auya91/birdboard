<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;
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
        $projects = auth('web')->user()->allProjects();
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
     * @return Application|RedirectResponse|Redirector
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        //persist
        $project = auth('web')->user()->projects()->create($this->validateRequest($request));

        //redirect
        return redirect($project->path())->with('success', __('app.created_successfully'));
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
        $project = Project::with('tasks', 'activities', 'members', 'owner')->findOrFail($project->id);
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
     * @return Application|JsonResponse|RedirectResponse|Redirector
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Project $project, Request $request)//: RedirectResponse
    {
        $HTTP_REFERER = str_replace(url('/'), '', request()->server->get('HTTP_REFERER'));
        $this->authorize('update', $project);
        //persist
        $project->update($this->validateRequest($request));
        if ($HTTP_REFERER == '/' . app()->getLocale() . '/projects/' . $project->id) {
        session()->flash('success', __('app.updated_successfully'));
            return response()->json([]);
        }
        //redirect
        return redirect($project->path())->with('success', __('app.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Project $project
     * @return RedirectResponse
     * @throws AuthorizationException
     * @throws Exception
     */
    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('manage', $project);
        $project->delete();
        return redirect()->route('projects.index')->with('success', __('app.deleted_successfully'));
    }

    /**
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    protected function validateRequest(Request $request): array
    {
        return $this->validate($request, [
            'title' => ['required', 'array'],
            'title.*' => ['required'],
            'description' => ['required', 'array'],
            'description.*' => ['required'],
            'notes' => ['nullable'],
        ], [], [
            'title.ar' => __('app.title_locale', ['locale' => __('app.ar')]),
            'title.en' => __('app.title_locale', ['locale' => __('app.en')]),
        ]);
    }
}
