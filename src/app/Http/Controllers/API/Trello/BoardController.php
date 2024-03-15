<?php

namespace App\Http\Controllers\API\Trello;

use Illuminate\Http\Request;
use App\Services\TrelloService;
use App\Http\Controllers\Controller;
use App\Models\Path;

class BoardController extends Controller
{
    protected $trelloService;

    public function __construct(TrelloService $trelloService)
    {
        $this->trelloService = $trelloService;
    }

    public function getMemberships($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/boards/{$id}/memberships");
    }

    public function getBoard($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/boards/{$id}");
    }

    public function updateBoard(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/boards/{$id}", $data);
    }

    public function deleteBoard($id, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/boards/{$id}");
    }

    public function getCard($id, $idCard, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/boards/{$id}/cards/{$idCard}");
    }

    public function getCards($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/boards/{$id}/cards");
    }

    public function getFilteredCards($id, $filter, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/boards/{$id}/cards/{$filter}");
    }

    public function getLists($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/boards/{$id}/lists");
    }

    public function createList(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post("/boards/{$id}/lists", $data);
    }

    public function getFilteredLists($id, $filter, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/boards/{$id}/lists/{$filter}");
    }

    public function getMembers($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/boards/{$id}/members");
    }

    public function updateMembers(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/boards/{$id}/members", $data);
    }

    public function updateMember(Request $request, $id, $idMember, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/boards/{$id}/members/{$idMember}", $data);
    }

    public function removeMember($id, $idMember, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/boards/{$id}/members/{$idMember}");
    }

    public function updateMembership(Request $request, $id, $idMembership, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/boards/{$id}/memberships/{$idMembership}", $data);
    }

    public function getLabels($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/boards/{$id}/labels");
    }

    public function createBoard(Request $request, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post("/boards", $data);
    }
}
