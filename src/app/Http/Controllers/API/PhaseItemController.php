<?php

namespace App\Http\Controllers\API;

use App\Enums\FileAction;
use App\Enums\MorphKey;
use App\Enums\PhaseItemContentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\PhaseItemRequest;
use App\Http\Resources\Collections\PhaseItemCollection;
use App\Http\Resources\PhaseItemResource;
use App\Models\PhaseItem;
use App\Services\StorageService;
use App\Traits\PaginatesOrLists;
use App\Traits\UploadsFile;
use Spatie\QueryBuilder\QueryBuilder;

class PhaseItemController extends Controller
{
    use UploadsFile, PaginatesOrLists;

    public $storage = null;

    public function __construct()
    {
        $this->storage = new StorageService();
    }



    public function index()
    {
        $trashed_only = request('trashed_only') === 'true';

        $buildQuery = PhaseItem::when($trashed_only, function ($query) {
            return $query->onlyTrashed();
        }, function ($query) {
            return $query->where('phase_id', request('phase_id'))->with('image')
                ->orderBy('created_at');
        });

        $query = QueryBuilder::for($buildQuery);

        $query = $this->paginateOrList($query);

        return $this->resolve(PhaseItemCollection::make($query));
    }

    public function store(PhaseItemRequest $request)
    {
        $file = null;
        $payload = request()->all();

        if (request('content_type') === PhaseItemContentType::ATTACHMENT) {
            $file = request()->file('content_value');
            $payload['content_value'] = null;
        }

        $phaseItem = PhaseItem::create($payload);

        if (request('content_type') === PhaseItemContentType::ATTACHMENT) {
            $this->uploadFile($file, MorphKey::PHASE_ITEM, $phaseItem->id);
        }

        return $this->resolve(PhaseItemResource::make($phaseItem));
    }

    public function update(PhaseItem $phaseItem, PhaseItemRequest $request)
    {
        $file = null;
        $payload = request()->all();
        $image = $phaseItem->image;

        if (request('content_type') === PhaseItemContentType::ATTACHMENT) {
            $file = request()->file('content_value');
            $payload['content_value'] = null;

            if ($payload['file_action'] === FileAction::CHANGE) {
                $image?->delete();

                $this->uploadFile($file, MorphKey::PHASE_ITEM, $phaseItem->id);
            }
        } else {
            $payload['file_action'] = FileAction::DELETE;
        }

        if ($payload['file_action'] === FileAction::DELETE && $image) {
            // $this->storage->delete($image->path);
            $image->delete();
        }

        $payload['meta'] = array_merge($phaseItem->meta ?: [], ['updated_at' => now()->toDateTimeString()]);

        $phaseItem->update($payload);

        return $this->resolve(PhaseItemResource::make($phaseItem));
    }

    public function destroy(PhaseItem $phaseItem)
    {
        $phaseItem->delete();

        return $this->resolve(PhaseItemResource::make($phaseItem));
    }

    public function restore($phaseItem)
    {
        $phaseItem_id = hash_to_id(MorphKey::PHASE_ITEM, $phaseItem);

        $phaseItem = PhaseItem::withTrashed()->findOrFail($phaseItem_id);
        $phaseItem->restore();

        return $this->resolve(PhaseItemResource::make($phaseItem));
    }
}
