<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Trello\CardController;
use App\Http\Controllers\API\Trello\ListController;
use App\Http\Controllers\API\Trello\BatchController;
use App\Http\Controllers\API\Trello\BoardController;
use App\Http\Controllers\API\Trello\LabelController;
use App\Http\Controllers\API\Trello\MemberController;
use App\Http\Controllers\API\Trello\OrganizationController;
use App\Http\Controllers\API\Trello\TrelloController;

Route::group(['middleware' => ['auth:sanctum', 'hash-to-id', 'localized']], function () {
    Route::group(['prefix' => 'trello'], function () {
        Route::post('/token-member', [TrelloController::class, 'setTokenMember']);
        Route::get('/{user}/{path}', [TrelloController::class, 'index']);
        Route::post('/access-token', [TrelloController::class, 'setAuthToken']);

        Route::prefix('members')->group(function () {
            Route::get('/{id}/{path}', [MemberController::class, 'getMember']);
            Route::put('/{id}/{path}', [MemberController::class, 'updateMember']);
            Route::get('/{id}/actions/{path}', [MemberController::class, 'getMemberActions']);
            Route::get('/{id}/board-backgrounds/{path}', [MemberController::class, 'getMemberBoardBackgrounds']);
            Route::post('/{id}/board-backgrounds/{path}', [MemberController::class, 'createMemberBoardBackground']);
            Route::get('/{id}/board-backgrounds/{idBackground}/{path}', [MemberController::class, 'getMemberBoardBackground']);
            Route::put('/{id}/board-backgrounds/{idBackground}/{path}', [MemberController::class, 'updateMemberBoardBackground']);
            Route::delete('/{id}/board-backgrounds/{idBackground}/{path}', [MemberController::class, 'deleteMemberBoardBackground']);
            Route::get('/{id}/board-stars/{path}', [MemberController::class, 'getMemberBoardStars']);
            Route::post('/{id}/board-stars/{path}', [MemberController::class, 'createMemberBoardStar']);
            Route::get('/{id}/board-stars/{idStar}/{path}', [MemberController::class, 'getMemberBoardStar']);
            Route::put('/{id}/board-stars/{idStar}/{path}', [MemberController::class, 'updateMemberBoardStar']);
            Route::delete('/{id}/board-stars/{idStar}/{path}', [MemberController::class, 'deleteMemberBoardStar']);
            Route::get('/{id}/boards/{path}', [MemberController::class, 'getMemberBoards']);
            Route::get('/{id}/boards-invited/{path}', [MemberController::class, 'getMemberBoardsInvited']);
            Route::get('/{id}/cards/{path}', [MemberController::class, 'getMemberCards']);
            Route::get('/{id}/custom-board-backgrounds/{path}', [MemberController::class, 'getMemberCustomBoardBackgrounds']);
            Route::post('/{id}/custom-board-backgrounds/{path}', [MemberController::class, 'createMemberCustomBoardBackground']);
            Route::get('/{id}/custom-board-backgrounds/{idBackground}/{path}', [MemberController::class, 'getMemberCustomBoardBackground']);
            Route::put('/{id}/custom-board-backgrounds/{idBackground}/{path}', [MemberController::class, 'updateMemberCustomBoardBackground']);
            Route::delete('/{id}/custom-board-backgrounds/{idBackground}/{path}', [MemberController::class, 'deleteMemberCustomBoardBackground']);
            Route::get('/{id}/custom-emoji/{path}', [MemberController::class, 'getMemberCustomEmoji']);
            Route::post('/{id}/custom-emoji/{path}', [MemberController::class, 'createMemberCustomEmoji']);
            Route::get('/{id}/custom-emoji/{idEmoji}/{path}', [MemberController::class, 'getMemberCustomEmoji']);
            Route::get('/{id}/custom-stickers/{path}', [MemberController::class, 'getMemberCustomStickers']);
            Route::post('/{id}/custom-stickers/{path}', [MemberController::class, 'createMemberCustomSticker']);
            Route::get('/{id}/custom-stickers/{idSticker}/{path}', [MemberController::class, 'getMemberCustomSticker']);
            Route::delete('/{id}/custom-stickers/{idSticker}/{path}', [MemberController::class, 'deleteMemberCustomSticker']);
            Route::get('/{id}/notifications/{path}', [MemberController::class, 'getMemberNotifications']);
            Route::get('/{id}/organizations/{path}', [MemberController::class, 'getMemberOrganizations']);
            Route::get('/{id}/organizations-invited/{path}', [MemberController::class, 'getMemberOrganizationsInvited']);
            Route::get('/{id}/saved-searches/{path}', [MemberController::class, 'getMemberSavedSearches']);
            Route::post('/{id}/saved-searches/{path}', [MemberController::class, 'createMemberSavedSearch']);
            Route::get('/{id}/saved-searches/{idSearch}/{path}', [MemberController::class, 'getMemberSavedSearch']);
            Route::put('/{id}/saved-searches/{idSearch}/{path}', [MemberController::class, 'updateMemberSavedSearch']);
            Route::delete('/{id}/saved-searches/{idSearch}/{path}', [MemberController::class, 'deleteMemberSavedSearch']);
            Route::get('/{id}/tokens/{path}', [MemberController::class, 'getMemberTokens']);
            Route::post('/{id}/avatar/{path}', [MemberController::class, 'updateMemberAvatar']);
            Route::post('/{id}/one-time-messages-dismissed/{path}', [MemberController::class, 'dismissOneTimeMessages']);
            Route::get('/{id}/{field}/{path}', [MemberController::class, 'getMemberField']);
        });

        Route::prefix('boards')->group(function () {
            Route::get('{id}/memberships/{path}', [BoardController::class, 'getMemberships']);
            Route::get('{id}/{path}', [BoardController::class, 'getBoard']);
            Route::put('{id}/{path}', [BoardController::class, 'updateBoard']);
            Route::delete('{id}/{path}', [BoardController::class, 'deleteBoard']);
            Route::get('{id}/cards/{idCard}/{path}', [BoardController::class, 'getCard']);
            Route::get('{id}/cards/{path}', [BoardController::class, 'getCards']);
            Route::get('{id}/cards/{filter}/{path}', [BoardController::class, 'getFilteredCards']);
            Route::get('{id}/lists/{path}', [BoardController::class, 'getLists']);
            Route::post('{id}/lists/{path}', [BoardController::class, 'createList']);
            Route::get('{id}/lists/{filter}/{path}', [BoardController::class, 'getFilteredLists']);
            Route::get('{id}/members/{path}', [BoardController::class, 'getMembers']);
            Route::put('{id}/members/{path}', [BoardController::class, 'updateMembers']);
            Route::put('{id}/members/{idMember}/{path}', [BoardController::class, 'updateMember']);
            Route::delete('{id}/members/{idMember}/{path}', [BoardController::class, 'removeMember']);
            Route::put('{id}/memberships/{idMembership}/{path}', [BoardController::class, 'updateMembership']);
            Route::get('{id}/labels/{path}', [BoardController::class, 'getLabels']);
            Route::post('/{path}', [BoardController::class, 'createBoard']);
        });

        Route::prefix('/lists')->group(function () {
            Route::get('/{id}/{path}', [ListController::class, 'getList']);
            Route::put('/{id}/{path}', [ListController::class, 'updateList']);
            Route::post('//{path}', [ListController::class, 'createList']);
            Route::post('/{id}/archive-all-cards/{path}', [ListController::class, 'archiveAllCards']);
            Route::post('/{id}/move-all-cards/{path}', [ListController::class, 'moveAllCards']);
            Route::put('/{id}/closed/{path}', [ListController::class, 'closeList']);
            Route::put('/{id}/id-board/{path}', [ListController::class, 'changeBoard']);
            Route::get('/{id}/actions/{path}', [ListController::class, 'getActions']);
            Route::get('/{id}/board/{path}', [ListController::class, 'getBoard']);
            Route::get('/{id}/cards/{path}', [ListController::class, 'getCards']);
            Route::put('/{id}/{field}/{path}', [ListController::class, 'updateField']);
        });;

        Route::prefix('/cards')->group(function () {
            Route::post('//{path}', [CardController::class, 'createCard']);
            Route::get('/{id}/{path}', [CardController::class, 'getCard']);
            Route::put('/{id}/{path}', [CardController::class, 'updateCard']);
            Route::delete('/{id}/{path}', [CardController::class, 'deleteCard']);
            Route::get('/{id}/{field}/{path}', [CardController::class, 'getField']);
            Route::get('/{id}/actions/{path}', [CardController::class, 'getActions']);
            Route::get('/{id}/attachments/{path}', [CardController::class, 'getAttachments']);
            Route::post('/{id}/attachments/{path}', [CardController::class, 'createAttachment']);
            Route::get('/{id}/attachments/{idAttachment}/{path}', [CardController::class, 'getAttachment']);
            Route::delete('/{id}/attachments/{idAttachment}/{path}', [CardController::class, 'deleteAttachment']);
            Route::get('/{id}/board/{path}', [CardController::class, 'getBoard']);
            Route::get('/{id}/checkItemStates/{path}', [CardController::class, 'getCheckItemStates']);
            Route::get('/{id}/checklists/{path}', [CardController::class, 'getChecklists']);
            Route::post('/{id}/checklists/{path}', [CardController::class, 'createChecklist']);
            Route::get('/{id}/checkItem/{idCheckItem}/{path}', [CardController::class, 'getCheckItem']);
            Route::put('/{id}/checkItem/{idCheckItem}/{path}', [CardController::class, 'updateCheckItem']);
            Route::delete('/{id}/checkItem/{idCheckItem}/{path}', [CardController::class, 'deleteCheckItem']);
            Route::get('/{id}/list/{path}', [CardController::class, 'getList']);
            Route::get('/{id}/members/{path}', [CardController::class, 'getMembers']);
            Route::get('/{id}/members-voted/{path}', [CardController::class, 'getMembersVoted']);
            Route::post('/{id}/members-voted/{path}', [CardController::class, 'addMembersVoted']);
            Route::get('/{id}/plugin-data/{path}', [CardController::class, 'getPluginData']);
            Route::get('/{id}/stickers/{path}', [CardController::class, 'getStickers']);
            Route::post('/{id}/stickers/{path}', [CardController::class, 'createSticker']);
            Route::get('/{id}/stickers/{idSticker}/{path}', [CardController::class, 'getSticker']);
            Route::put('/{id}/stickers/{idSticker}/{path}', [CardController::class, 'updateSticker']);
            Route::delete('/{id}/stickers/{idSticker}/{path}', [CardController::class, 'deleteSticker']);
            Route::put('/{id}/actions/{idAction}/comments/{path}', [CardController::class, 'updateActionComment']);
            Route::delete('/{id}/actions/{idAction}/comments/{path}', [CardController::class, 'deleteActionComment']);
            Route::put('/{idCard}/custom-field/{idCustomField}/item/{path}', [CardController::class, 'updateCustomFieldItem']);
            Route::put('/{idCard}/custom-fields/{path}', [CardController::class, 'updateCustomFields']);
            Route::get('/{id}/custom-field-items/{path}', [CardController::class, 'getCustomFieldItems']);
            Route::post('/{id}/actions/comments/{path}', [CardController::class, 'createActionComment']);
            Route::post('/{id}/id-labels/{path}', [CardController::class, 'addIdLabel']);
            Route::post('/{id}/id-members/{path}', [CardController::class, 'addIdMember']);
            Route::post('/{id}/labels/{path}', [CardController::class, 'addLabel']);
            Route::post('/{id}/mark-associated-notifications-read/{path}', [CardController::class, 'markAssociatedNotificationsRead']);
            Route::delete('/{id}/id-labels/{idLabel}/{path}', [CardController::class, 'removeIdLabel']);
            Route::delete('/{id}/id-members/{idMember}/{path}', [CardController::class, 'removeIdMember']);
            Route::delete('/{id}/members-voted/{idMember}/{path}', [CardController::class, 'removeMemberVoted']);
            Route::put('/{idCard}/checklist/{idChecklist}/checkItem/{idCheckItem}/{path}', [CardController::class, 'updateCheckItemInChecklist']);
            Route::delete('/{id}/checklists/{idChecklist}/{path}', [CardController::class, 'deleteChecklist']);
        });


        Route::prefix('/organizations')->group(function () {
            Route::post('//{path}', [OrganizationController::class, 'create']);
            Route::get('/{id}/{path}', [OrganizationController::class, 'getById']);
            Route::put('/{id}/{path}', [OrganizationController::class, 'updateById']);
            Route::delete('/{id}/{path}', [OrganizationController::class, 'deleteById']);
            Route::get('/{id}/actions/{path}', [OrganizationController::class, 'getActions']);
            Route::get('/{id}/boards/{path}', [OrganizationController::class, 'getBoards']);
            Route::get('/{id}/exports/{path}', [OrganizationController::class, 'getExports']);
            Route::post('/{id}/exports/{path}', [OrganizationController::class, 'createExport']);
            Route::get('/{id}/members/{path}', [OrganizationController::class, 'getMembers']);
            Route::put('/{id}/members/{path}', [OrganizationController::class, 'updateMembers']);
            Route::get('/{id}/memberships/{path}', [OrganizationController::class, 'getMemberships']);
            Route::get('/{id}/memberships/{idMembership}/{path}', [OrganizationController::class, 'getMembership']);
            Route::get('/{id}/plugin-data/{path}', [OrganizationController::class, 'getPluginData']);
            Route::get('/{id}/tags/{path}', [OrganizationController::class, 'getTags']);
            Route::post('/{id}/tags/{path}', [OrganizationController::class, 'createTag']);
            Route::put('/{id}/members/{idMember}/{path}', [OrganizationController::class, 'updateMember']);
            Route::delete('/{id}/members/{idMember}/{path}', [OrganizationController::class, 'deleteMember']);
            Route::put('/{id}/members/{idMember}/deactivated/{path}', [OrganizationController::class, 'deactivateMember']);
            Route::post('/{id}/logo/{path}', [OrganizationController::class, 'uploadLogo']);
            Route::delete('/{id}/logo/{path}', [OrganizationController::class, 'deleteLogo']);
            Route::delete('/{id}/members/{idMember}/all/{path}', [OrganizationController::class, 'deleteAllMember']);
            Route::delete('/{id}/prefs/associated-domain/{path}', [OrganizationController::class, 'deleteAssociatedDomain']);
            Route::delete('/{id}/prefs/org-invite-restrict/{path}', [OrganizationController::class, 'deleteOrgInviteRestrict']);
            Route::delete('/{id}/tags/{idTag}/{path}', [OrganizationController::class, 'deleteTag']);
            Route::get('/{id}/new-billable-guests/{idBoard}/{path}', [OrganizationController::class, 'getNewBillableGuests']);
            Route::get('/{id}/{field}/{path}', [OrganizationController::class, 'getFieldById']);
        });

        Route::prefix('/labels')->group(function () {
            Route::get('/{id}/{path}', [LabelController::class, 'getLabel']);
            Route::put('/{id}/{path}', [LabelController::class, 'updateLabel']);
            Route::delete('/{id}/{path}', [LabelController::class, 'deleteLabel']);
            Route::post('//{path}', [LabelController::class, 'createLabel']);
            Route::put('/{id}/{field}/{path}', [LabelController::class, 'updateField']);
        });

        Route::get('/batch', [BatchController::class, 'getBatch']);
    });
});
