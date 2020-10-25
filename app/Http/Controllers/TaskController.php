<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Project $project)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Project $project)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Project $project
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function store(Project $project, Request $request): JsonResponse
    {
        $this->authorize('update', $project);
        $data = $this->validate($request, [
            'body' => ['required']
        ]);
        $project->addTask($data);
        $project = Project::with('tasks', 'activities')->findOrFail($project->id);
        return response()->json($project);
    }

    /**
     * Display the specified resource.
     *
     * @param Task $task
     * @return Response
     */
    public function show(Project $project, Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Task $task
     * @return Response
     */
    public function edit(Project $project, Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Project $project
     * @param Task $task
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Project $project, Task $task, Request $request): JsonResponse
    {
        $this->authorize('update', $project);
        $data = $this->validate($request, [
            'body' => ['sometimes', 'required'],
        ]);
        $task->update($data);
        $request->get('is_completed') ? $task->complete() : $task->inComplete();

        $project = Project::with('tasks', 'activities')->findOrFail($project->id);
        return response()->json($project);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Task $task
     * @return Response
     */
    public function destroy(Project $project, Task $task)
    {
        //
    }
}
