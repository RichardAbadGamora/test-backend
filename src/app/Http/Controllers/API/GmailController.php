<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\PathService;
use Google\Client;
use Google\Service\Gmail;

use App\Services\GmailService;
use App\Enums\IntegrationType;
use App\Services\Integrations\IntegrationFactory;
use Illuminate\Support\Facades\Auth;
use App\Models\Integration;
use App\Models\User;
use App\Models\Page;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\Collections\GmailCollection;
use App\Http\Resources\GmailResource;

class GmailController extends Controller
{
    protected $gmailService;

    public function __construct()
    {
        $this->gmailService = new GmailService();
    }

    public function show(Page $page)
    {
        return $this->resolve($page);
    }

    public function generateURL(Page $page, Request $request)
    {
        return $this->resolve($this->gmailService->generateGmailAuthenticationURL($page));
    }

    public function authenticate(Request $request)
    {
        $integration = $this->gmailService->authenticate($request->all());
        return $this->resolve($integration);
    }

    public function disconnectIntegration(Page $page)
    {
        $integration = $this->gmailService->disconnect($page);
        return $this->resolve($integration);
    }

    public function getInbox(Integration $integration, Request $request)
    {
        $request = $request->all();
        if (isset($request['type'])) {
            if ($request['type'] == 'thread') {
                $request = array_merge($request, [
                    'filter-email' => $integration->path->users->pluck('email')->toArray()
                ]);
            }
        }
        $inboxes = $this->gmailService->getInbox($integration, $request);
        return $this->resolve(GmailResource::make($inboxes));
    }

    public function viewInboxMessage(Integration $integration, String $messageID, Request $request)
    {
        $inboxes = $this->gmailService->viewInboxMessage($integration, $messageID, $request->all());
        return $this->resolve($inboxes);
    }

    public function deleteInboxMessage(Integration $integration, String $messageID, Request $request)
    {
        $inboxes = $this->gmailService->deleteInboxMessage($integration, $messageID, $request->all());
        return $this->resolve($inboxes);
    }

    public function archiveInboxMessage(Integration $integration, String $messageID, Request $request)
    {
        $inboxes = $this->gmailService->archiveInboxMessage($integration, $messageID, $request->all());
        return $this->resolve($inboxes);
    }

    public function bulkDeleteInboxMessage(Integration $integration, Request $request)
    {
        // return $this->resolve("DELETE");
        $inboxes = $this->gmailService->bulkDeleteInboxMessage($integration, $request->all()['messageIDs']);
        return $this->resolve($inboxes);
    }

    public function bulkArchiveInboxMessage(Integration $integration, Request $request)
    {
        $inboxes = $this->gmailService->bulkArchiveInboxMessage($integration, $request->all()['messageIDs']);
        return $this->resolve($inboxes);
    }

    public function replyToMessage(Integration $integration, String $messageID, Request $request)
    {
        $inboxes = $this->gmailService->replyToMessage($integration, $messageID, $request->all());
        return $this->resolve($inboxes);
    }
}
