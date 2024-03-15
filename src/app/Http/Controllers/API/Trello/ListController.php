<?php

namespace App\Http\Controllers\API\Trello;

use Illuminate\Http\Request;
use App\Services\TrelloService;
use App\Http\Controllers\Controller;
use App\Models\Path;

class ListController extends Controller
{
    protected $trelloService;

    public function __construct(TrelloService $trelloService)
    {
        $this->trelloService = $trelloService;
    }

    public function getList($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/lists/{$id}");
    }

    public function updateList(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/lists/{$id}", $data);
    }

    public function createList(Request $request, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post('/lists', $data);
    }

    public function archiveAllCards($id, Path $path)
    {
        return $this->trelloService->setPath($path)->post("/lists/{$id}/archiveAllCards");
    }

    public function moveAllCards($id, Path $path)
    {
        return $this->trelloService->setPath($path)->post("/lists/{$id}/moveAllCards");
    }

    public function closeList(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/lists/{$id}/closed", $data);
    }

    public function changeBoard(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/lists/{$id}/idBoard", $data);
    }

    public function updateField(Request $request, $id, $field, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/lists/{$id}/{$field}", $data);
    }

    public function getActions($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/lists/{$id}/actions");
    }

    public function getBoard($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/lists/{$id}/board");
    }

    public function getCards($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/lists/{$id}/cards");
    }
}
