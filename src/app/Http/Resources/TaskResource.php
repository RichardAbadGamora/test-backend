<?php

namespace App\Http\Resources;

use App\Enums\MorphKey;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            unset_keys(parent::toArray($request), ['id', 'task_id', 'path_id', 'user_id']),
            [
                'task_hash' => id_to_hash(MorphKey::TASK, $this->task_id),
                'path_hash' => id_to_hash(MorphKey::PATH, $this->path_id),
                'user_hash' => id_to_hash(MorphKey::USER, $this->user_id),
                'user' => UserResource::make($this->whenLoaded('user')),
                'path' => PathResource::make($this->whenLoaded('path')),
                'sub_tasks' => TaskResource::collection($this->whenLoaded('subTasks')),
            ]
        );
    }
}
