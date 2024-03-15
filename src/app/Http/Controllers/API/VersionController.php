<?php

namespace App\Http\Controllers\API;

use App\Enums\MorphKey;
use App\Http\Controllers\Controller;
use App\Traits\ResolvesRejects;
use Illuminate\Http\Request;
use Mpociot\Versionable\Version;

class VersionController extends Controller
{
    use ResolvesRejects;

    public function revert(Request $request)
    {
        // test
        $version = Version::find($request->revert_id);
        $model = getMorphedClass($version->versionable_type)::withTrashed()->find($version->versionable_id);

        if ($model->versions->count() === 1) {
            if ($model->trashed()) {
                $model->restore();

                $this->restoreRelationships($model);

                return $this->resolve(['condition' => 1]);
            } else {
                $model->forceDelete();
                $model->versions()->delete();

                return $this->resolve(['condition' => 2]);
            }
        }

        if ($model->versions->count() > 1) {
            if ($model->trashed()) {
                $model->restore();

                $this->restoreRelationships($model);

                return $this->resolve(['condition' => 3]);
            } else {
                Version::find($request->revert_id)->revert();
                $model->versions()->where('version_id', '>', $request->revert_id)->delete();
                $model->restore();

                $this->restoreRelationships($model);

                return $this->resolve(['condition' => 4]);
            }
        }
    }

    public function restoreRelationships($model)
    {
        foreach ($model->cascadeRestores as $key => $relationship) {
            $relationship = $model->$relationship();

            if ($model->getModel()->getMorphClass() === MorphKey::PHASE_ITEM) {
                $this->phaseItemRestoreRelationships($model);
            } else {
                $relationship->restore();
            }
        }
    }

    public function phaseItemRestoreRelationships($model)
    {
        foreach ($model->cascadeRestores as $key => $relationship) {
            if ($model->$relationship()->getModel()->getMorphClass() === MorphKey::ATTACHMENT) {
                $relationship = $model->$relationship();
                $latestId = $relationship->latest()->first();
                $latestId->delete();
                $relationship->onlyTrashed()->where('id', '<', $latestId->id)->latest()->first()->restore();
            }
        }
    }
}
