<?php

namespace App\Http\Controllers\API;

use App\Enums\MorphKey;
use App\Enums\PageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\FloatRepositionRequest;
use App\Http\Requests\API\PageRequest;
use App\Http\Requests\API\RepositionPagesRequest;
use App\Http\Resources\Collections\PageCollection;
use App\Http\Resources\PageResource;
use App\Models\Page;
use App\Models\Path;
use App\Traits\PaginatesOrLists;
use Spatie\QueryBuilder\QueryBuilder;

class PageController extends Controller
{
    use PaginatesOrLists;

    public function index()
    {
        $query = QueryBuilder::for(Page::class)
            ->allowedIncludes(['path', 'user', 'iframe'])
            ->allowedFilters(['name', 'type', 'visibility'])
            ->allowedSorts(['name', 'type', 'visibility']);

        $query = $this->paginateOrList($query);

        return $this->resolve(PageCollection::make($query));
    }

    public function store(PageRequest $request)
    {
        $payload = array_merge($request->all(), [
            'user_id' => user()->id,
        ]);

        $payload = $this->appendMeta($payload);

        $page = Path::find($request->path_id)
            ->pages()
            ->create($payload);

        return $this->resolve(PageResource::make($page));
    }

    public function appendMeta($payload)
    {
        if (in_array($payload['type'], [PageType::FIGMA_DESIGN_EMBED, PageType::FIGMA_PROTOTYPE_EMBED])) {
            $payload['meta'] = [
                'embed_code' => request('embed_code')
            ];
        }

        return $payload;
    }

    public function show(Page $page)
    {
        $query = QueryBuilder::for(Page::class)
            ->allowedIncludes(['path', 'user'])
            ->findOrFail($page->id);

        return $this->resolve(PageResource::make($query));
    }

    public function update(PageRequest $request, Page $page)
    {
        $page->update($request->all());

        return $this->resolve(PageResource::make($page));
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return $this->resolve(PageResource::make($page));
    }

    public function reposition(RepositionPagesRequest $request)
    {
        foreach ($request->positioning as $position) {
            $pageId = hash_to_id(MorphKey::PAGE, $position['page_hash']);

            $page = Page::find($pageId);

            $page->update([
                'grid_x' => $position['grid_x'],
                'grid_y' => $position['grid_y'],
                'grid_width' => $position['grid_width'],
                'grid_height' => $position['grid_height'],
            ]);
        }


        return $this->resolve();
    }

    public function floatReposition(FloatRepositionRequest $request)
    {

        foreach ($request->positioning as $position) {
            $pageId = hash_to_id(MorphKey::PAGE, $position['hash']);

            $page = Page::find($pageId);

            $page->update([
                'float_top' => $position['top'],
                'float_left' => $position['left'],
                'float_z_index' => $position['zIndex'],
                'float_transform' => $position['transform'],
            ]);
        }


        return $this->resolve();
    }
}
