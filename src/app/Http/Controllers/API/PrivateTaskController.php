<?php

namespace App\Http\Controllers\API;

use App\Enums\TaskAccess;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\TaskRequest;
use App\Http\Resources\Collections\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class PrivateTaskController extends Controller
{
    public $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index()
    {
        $folders = $this->taskService->getAll([
            'access' => TaskAccess::PRIVATE,
            'user_id' => user()->id,
            'task_id' => request('task_id', null),
            'path_id' => request('path_id', null),
            'page_id' => request('page_id', null),
        ]);

        return $this->resolve(TaskCollection::make($folders));
    }

    public function store(TaskRequest $request)
    {
        $task = $this->taskService->create(
            TaskAccess::PRIVATE,
            array_merge($request->all(), ['user_id' => user()->id])
        );

        return $this->resolve(TaskResource::make($task));
    }

    public function show(Task $task)
    {
        $task = $this->taskService->getOne($task);

        return $this->resolve(TaskResource::make($task));
    }

    public function update(TaskRequest $request, Task $task)
    {
        $task = $this->taskService->update($task, $request->all());

        return $this->resolve(TaskResource::make($task));
    }

    public function destroy(Task $task)
    {
        $this->taskService->delete($task);

        return $this->resolve(null);
    }

    public function updateStatus(TaskRequest $request, Task $task)
    {
        $task = $this->taskService->updateStatus($task, $request->status);

        return $this->resolve(TaskResource::make($task));
    }

    public function reposition()
    {
        $this->validate(request(), [
            'tasks' => 'required|array',
            'tasks.*.sub_tasks' => function ($attribute, $value, $fail) {
                if (is_array($value)) {
                    foreach ($value as $key => $subTask) {
                        $subTaskId = hash_to_id('task', $subTask['hash']);
                        $task = Task::find($subTaskId);
                        if ($task->subTasks()->count() > 0) {
                            $fail($task->name . ' cannot be drag to parent task because it has sub tasks.');
                        }
                    }
                }
            },
        ]);

        $this->taskService->reposition(request('tasks', []));

        return $this->resolve();
    }
}
