<?php

namespace App\Http\Controllers\API\Trello;

use Illuminate\Http\Request;
use App\Services\TrelloService;
use App\Http\Controllers\Controller;
use App\Models\Path;

class MemberController extends Controller
{
    protected $trelloService;

    public function __construct(TrelloService $trelloService)
    {
        $this->trelloService = $trelloService;
    }

    public function getMember($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}");
    }

    public function updateMember(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/members/{$id}", $data);
    }

    public function getMemberField($id, $field, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/{$field}");
    }

    public function getMemberActions($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/actions");
    }

    public function getMemberBoardBackgrounds($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/board-backgrounds");
    }

    public function createMemberBoardBackground(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post("/members/{$id}/board-backgrounds", $data);
    }

    public function getMemberBoardBackground($id, $idBackground, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/board-backgrounds/{$idBackground}");
    }

    public function updateMemberBoardBackground(Request $request, $id, $idBackground, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/members/{$id}/board-backgrounds/{$idBackground}", $data);
    }

    public function deleteMemberBoardBackground($id, $idBackground, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/members/{$id}/board-backgrounds/{$idBackground}");
    }

    public function getMemberBoardStars($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/boardStars");
    }

    public function createMemberBoardStar(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post("/members/{$id}/boardStars", $data);
    }

    public function getMemberBoardStar($id, $idStar, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/boardStars/{$idStar}");
    }

    public function updateMemberBoardStar(Request $request, $id, $idStar, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/members/{$id}/boardStars/{$idStar}", $data);
    }

    public function deleteMemberBoardStar($id, $idStar, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/members/{$id}/boardStars/{$idStar}");
    }

    public function getMemberBoards($id, Path $path)
    {
        return $this->trelloService->setPath($path)->setPath($path)->get("/members/{$id}/boards", ['filter' => 'open']);
    }

    public function getMemberBoardsInvited($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/boardsInvited");
    }

    public function getMemberCards($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/cards");
    }

    public function getMemberCustomBoardBackgrounds($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/customBoardBackgrounds");
    }

    public function createMemberCustomBoardBackground(Request $request, $id, Path $path)
    {
        $data = $request->all(); // Custom board background data from request
        return $this->trelloService->setPath($path)->post("/members/{$id}/customBoardBackgrounds", $data);
    }

    public function getMemberCustomBoardBackground($id, $idBackground, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/customBoardBackgrounds/{$idBackground}");
    }

    public function updateMemberCustomBoardBackground(Request $request, $id, $idBackground, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/members/{$id}/customBoardBackgrounds/{$idBackground}", $data);
    }

    public function deleteMemberCustomBoardBackground($id, $idBackground, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/members/{$id}/customBoardBackgrounds/{$idBackground}");
    }

    public function getMemberCustomEmoji($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/customEmoji");
    }

    public function createMemberCustomEmoji(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post("/members/{$id}/customEmoji", $data);
    }

    public function getMemberCustomStickers($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/customStickers");
    }

    public function createMemberCustomSticker(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post("/members/{$id}/customStickers", $data);
    }

    public function getMemberNotifications($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/notifications");
    }

    public function getMemberOrganizations($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/organizations");
    }

    public function getMemberOrganizationsInvited($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/organizationsInvited");
    }

    public function getMemberSavedSearches($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/savedSearches");
    }

    public function createMemberSavedSearch(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post("/members/{$id}/savedSearches", $data);
    }

    public function getMemberSavedSearch($id, $idSearch, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/savedSearches/{$idSearch}");
    }

    public function updateMemberSavedSearch(Request $request, $id, $idSearch, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/members/{$id}/savedSearches/{$idSearch}", $data);
    }

    public function deleteMemberSavedSearch($id, $idSearch, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/members/{$id}/savedSearches/{$idSearch}");
    }

    public function getMemberTokens($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/members/{$id}/tokens");
    }

    public function updateMemberAvatar(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post("/members/{$id}/avatar", $data);
    }

    public function dismissOneTimeMessages(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post("/members/{$id}/oneTimeMessagesDismissed", $data);
    }
}
