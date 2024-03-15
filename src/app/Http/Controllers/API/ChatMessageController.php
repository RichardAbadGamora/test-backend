<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatMessageResource;
use App\Http\Resources\Collections\ChatMessageCollection;
use App\Models\ChatMessage;
use App\Models\Page;
use App\Services\ChatMessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ChatMessageController extends Controller
{

    private $chatMessageService;

    public function __construct(ChatMessageService $chatMessageService)
    {
        $this->chatMessageService = $chatMessageService;
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $userId = user()->id;
        $pageId = $request->page_id;
        $session = $request->session;
        $content = $request->content;

        $this->chatMessageService->createChatMessage($userId, $pageId, $session, $content);

        $responseText = $this->chatMessageService->generateChatResponse($content);

        $chatMessage = $this->chatMessageService->createResponseChatMessage($userId, $pageId, $session, $responseText);

        return $this->resolve(ChatMessageResource::make($chatMessage));
    }

    public function history(Page $page)
    {
        $chatMessages = $page->chatMessages()
        ->orderBy('created_at', 'desc')
        ->where('type', 'prompt')
        ->get()
        ->unique('session');

        return $this->resolve(ChatMessageCollection::make($chatMessages));
    }

    public function conversation($session)
    {
        $chatMessages = ChatMessage::where('session', $session)
        ->get();

        return $this->resolve(ChatMessageCollection::make($chatMessages));
    }
}
