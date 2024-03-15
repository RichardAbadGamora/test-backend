<?php

namespace App\Services;

use App\Enums\GroupAccess;
use App\Enums\GroupType;
use App\Enums\MorphKey;
use App\Models\Group;
use App\Models\Path;
use App\Models\Task;
use App\Traits\PaginatesOrLists;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\QueryBuilder;

class TaskService
{
    use PaginatesOrLists;

    public function getAll($options = [])
    {
        $user_id = Arr::get($options, 'user_id');
        $access = Arr::get($options, 'access');
        $task_id = Arr::get($options, 'task_id');
        $path_id = Arr::get($options, 'path_id');
        $page_id = Arr::get($options, 'page_id');

        $path = Path::find($path_id);

        $tasks = $path->tasks()
            ->where(compact('access', 'task_id'))
            ->where('page_id', $page_id)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where(compact('user_id'));
            })
            ->orderBy('order');

        $query = QueryBuilder::for($tasks)
            ->allowedIncludes(['user', 'path', 'subTasks']);

        return $this->paginateOrList($query);
    }

    public function getOne(Task $task)
    {
        return QueryBuilder::for(Task::class)
            ->allowedIncludes(['user', 'path', 'subTasks'])
            ->findOrFail($task->id);
    }

    public function create($access, $data)
    {
        return Task::create(
            array_merge(
                compact('access'),
                $data
            )
        );
    }

    public function update(Task $task, $data)
    {
        $task->update($data);

        return $task;
    }

    public function delete(Task $task)
    {
        $task->delete();
        $task->subTasks()->delete();

        return $task;
    }

    public function updateStatus(Task $task, $status)
    {
        $task->status = $status;
        $task->save();

        $task->subTasks()->update(['status' => $status]);

        return $task->fresh()->load('subTasks');
    }

    public function reposition($tasks)
    {
        foreach ($tasks as $task) {
            $this->repositionTask($task);

            foreach ($task['sub_tasks'] as $subTask) {
                $this->repositionTask($subTask);
            }
        }
    }

    private function repositionTask($task)
    {
        $this->update(
            Task::find(hash_to_id(MorphKey::TASK, $task['hash'])),
            [
                'order' => $task['order'],
                'task_id' => $task['task_hash'] ? hash_to_id(MorphKey::TASK, $task['task_hash']) : null,
            ]
        );
    }
}
