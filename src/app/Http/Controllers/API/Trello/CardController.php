<?php

namespace App\Http\Controllers\API\Trello;

use Illuminate\Http\Request;
use App\Services\TrelloService;
use App\Http\Controllers\Controller;
use App\Models\Path;

class CardController extends Controller
{
    protected $trelloService;

    public function __construct(TrelloService $trelloService)
    {
        $this->trelloService = $trelloService;
    }

    public function createCard(Request $request, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post('/cards', $data);
    }

    public function getCard($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/cards/{$id}");
    }

    public function updateCard(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/cards/{$id}", $data);
    }

    public function deleteCard($id, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/cards/{$id}");
    }

    public function getField($id, $field, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/cards/{$id}/{$field}");
    }

    public function getActions($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/cards/{$id}/actions");
    }

    public function getAttachments($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/cards/{$id}/attachments");
    }

    public function createAttachment(Request $request, $id, Path $path)
    {
        $data = $request->all(); // Attachment data from request
        return $this->trelloService->setPath($path)->post("/cards/{$id}/attachments", $data);
    }

    public function getAttachment($id, $idAttachment, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/cards/{$id}/attachments/{$idAttachment}");
    }

    public function deleteAttachment($id, $idAttachment, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/cards/{$id}/attachments/{$idAttachment}");
    }

    public function getBoard($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/cards/{$id}/board");
    }

    public function getCheckItemStates($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/cards/{$id}/checkItemStates");
    }

    public function getChecklists($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/cards/{$id}/checklists");
    }

    public function createChecklist(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post("/cards/{$id}/checklists", $data);
    }

    public function getCheckItem($id, $idCheckItem, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/cards/{$id}/checkItem/{$idCheckItem}");
    }

    public function updateCheckItem(Request $request, $id, $idCheckItem, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/cards/{$id}/checkItem/{$idCheckItem}", $data);
    }

    public function deleteCheckItem($id, $idCheckItem, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/cards/{$id}/checkItem/{$idCheckItem}");
    }

    public function getList($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/cards/{$id}/list");
    }

    public function getMembers($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/cards/{$id}/members");
    }
    public function getMembersVoted($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/cards/{$id}/membersVoted");
    }

    public function addMembersVoted(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post("/cards/{$id}/membersVoted", $data);
    }

    public function getPluginData($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/cards/{$id}/pluginData");
    }

    public function getStickers($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/cards/{$id}/stickers");
    }

    public function createSticker(Request $request, $id, Path $path)
    {
        $data = $request->all(); // Sticker data from request
        return $this->trelloService->setPath($path)->post("/cards/{$id}/stickers", $data);
    }

    public function getSticker($id, $idSticker, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/cards/{$id}/stickers/{$idSticker}");
    }

    public function updateSticker(Request $request, $id, $idSticker, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/cards/{$id}/stickers/{$idSticker}", $data);
    }

    public function deleteSticker($id, $idSticker, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/cards/{$id}/stickers/{$idSticker}");
    }

    public function updateActionComment(Request $request, $id, $idAction, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/cards/{$id}/actions/{$idAction}/comments", $data);
    }

    public function deleteActionComment($id, $idAction, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/cards/{$id}/actions/{$idAction}/comments");
    }

    public function updateCustomFieldItem(Request $request, $idCard, $idCustomField, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/cards/{$idCard}/customField/{$idCustomField}/item", $data);
    }

    public function updateCustomFields(Request $request, $idCard, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/cards/{$idCard}/custoFields", $data);
    }

    public function getCustomFieldItems($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/cards/{$id}/customFieldItems");
    }

    public function createActionComment(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post("/cards/{$id}/actions/comments", $data);
    }

    public function addIdLabel(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post("/cards/{$id}/idLabels", $data);
    }

    public function addIdMember(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post("/cards/{$id}/idMembers", $data);
    }

    public function addLabel(Request $request, $id, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post("/cards/{$id}/labels", $data);
    }

    public function markAssociatedNotificationsRead($id, Path $path)
    {
        return $this->trelloService->setPath($path)->post("/cards/{$id}/markAssociatedNotificationsRead");
    }

    public function removeIdLabel($id, $idLabel, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/cards/{$id}/idLabels/{$idLabel}");
    }

    public function removeIdMember($id, $idMember, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/cards/{$id}/idMembers/{$idMember}");
    }

    public function removeMemberVoted($id, $idMember, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/cards/{$id}/membersVoted/{$idMember}");
    }

    public function updateCheckItemInChecklist(Request $request, $idCard, $idChecklist, $idCheckItem, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/cards/{$idCard}/checklist/{$idChecklist}/checkItem/{$idCheckItem}", $data);
    }

    public function deleteChecklist($id, $idChecklist, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/cards/{$id}/checklists/{$idChecklist}");
    }
}
