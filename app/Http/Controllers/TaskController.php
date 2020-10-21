<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
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
     */
    public function store(Project $project, Request $request): JsonResponse
    {
        $user = auth('web')->user();
        /** @var User $user */
        abort_if($user->isNot($project->owner), 403);
        $data = $this->validate($request, [
            'body' => ['required']
        ]);
        $task = $project->addTask($data);
        return response()->json($task);
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
     * @throws ValidationException
     */
    public function update(Project $project, Task $task, Request $request): JsonResponse
    {
        $user = auth('web')->user();
        /** @var User $user */
        abort_if($user->isNot($task->project->owner), 403);
        $data = $this->validate($request, [
            'body' => ['required'],
            'is_completed' => ['nullable'],
        ]);
        $data['is_completed'] = $request->filled('is_completed') ? 1 : 0;
        $task->update($data);
        return response()->json($task);
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
