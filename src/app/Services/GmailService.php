<?php

namespace App\Services;

use App\Enums\IntegrationType;
use App\Models\Integration;
use App\Services\Integrations\IntegrationFactory;
use App\Models\User;
use App\Models\Page;
use Illuminate\Support\Carbon;
use Carbon\CarbonInterface;

class GmailService
{
    protected $client;

    public function __construct()
    {
        $this->client = new \Google\Client();
        $this->client->setClientId(config('services.gmail.client_id'));
        $this->client->setClientSecret(config('services.gmail.client_secret'));
        $this->client->addScope(\Google\Service\Gmail::MAIL_GOOGLE_COM);
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');
    }

    public function updateOrCreateToken(array $data = [])
    {
        return Integration::updateOrCreate([
            'user_id' => $data['user_id'],
            'path_id' => $data['path_id'],
            'name' => $data['name'],
            'type' => $data['type'],
        ], $data);
    }

    public function getData(array $data = [])
    {
        return Integration::where('user_id', $data['user_id'])
            ->where('path_id', $data['path_id'])
            ->where('type', IntegrationType::GMAIL)
            ->first();
    }

    public function generateGmailAuthenticationURL(Page $page)
    {
        $params = base64_encode(json_encode([
            'page' => $page->hash,
        ]));
        $this->client->setState($params);
        $redirect_uri = config('app.web_app_url').'/gmail/authenticate';
        $this->client->setRedirectUri($redirect_uri);
        $this->client->addScope(\Google\Service\Gmail::MAIL_GOOGLE_COM);
        return $this->client->createAuthUrl();
    }

    public function authenticate($request)
    {
        $stateParams = json_decode(base64_decode($request['state']), true);
        $page = Page::find((new Page())->idFromHash($stateParams['page']));


        $redirect_uri = config('app.web_app_url').'/gmail/authenticate';
        $this->client->setRedirectUri($redirect_uri);
        $service = new \Google\Service\Gmail($this->client);
        $response = $this->client->fetchAccessTokenWithAuthCode($request['code']);
        $refreshToken = $this->client->getRefreshToken();
        $gmailService = new GmailService();
        return $gmailService->storeAccessKey([
            'name' => $service->users->getProfile('me')->getEmailAddress(),
            'page_id' => $page->id,
            'type' => IntegrationType::GMAIL,
            'access_token' => $response['access_token'],
            'refresh_token' => $refreshToken,
            'expires_in' => $response['expires_in']
        ]);
    }

    public function disconnect(Page $page)
    {
        $integration = Integration::find($page->integration_id);
        $page->integration_id = null;
        $page->save();
        //Check if other Page is using the same integration
        if (!Page::where('integration_id', $integration->id)->exists()) {
            $integration->delete();
        }
        return $page;
    }

    public function storeAccessKey(array $data)
    {
        $page = Page::find($data['page_id']);
        $user = User::find($page->user_id);

        $item['access_token'] = $data['access_token'];
        $item['refresh_token'] = $data['refresh_token']?? null;
        $item['user_id'] = $user->id;
        $item['path_id'] = $page->path_id;
        $item['name'] = $user->firstname . "'s Access Token";
        $item['type'] = IntegrationType::GMAIL;
        $integration = IntegrationFactory::create(IntegrationType::GMAIL)->updateOrCreateToken($item);

        if ($page) {
            $page->integration_id = $integration->id;
            $page->save();
        }
        return IntegrationFactory::create(IntegrationType::GMAIL)->updateOrCreateToken($item);
    }

    /**
     * @param Integration $integration
     * @return Integration
     *  */
    public function getInbox(Integration $integration, $request)
    {
        $request['limit'] = $request['limit']?? 10;
        $this->client->setAccessToken($integration->access_token);
        if ($this->client->isAccessTokenExpired() && $integration->refresh_token) {
            $response = $this->client->fetchAccessTokenWithRefreshToken($integration->refresh_token);
            $integration->access_token = $response['access_token'];
            $integration->refresh_token = $response['refresh_token'];
            $integration->save();
        }
        $service = new \Google\Service\Gmail($this->client);
        $params = [];
        $params['maxResults'] = $request['limit'];
        $params['labelIds'] = ['INBOX'];
        if (isset($request['filter-email'])) {
            $params['q'] = "from:".implode(" OR ", $request['filter-email']);
        }
        if (isset($request['page_token'])) {
            $params['pageToken'] = $request['page_token'];
        }
        $results = $service->users_messages->listUsersMessages('me', $params);
        $inboxList = [];
        foreach ($results->getMessages() as $message) {
            $messageId = $message->getId();
            $message = $service->users_messages->get('me', $messageId, ['format' => 'full']);
            $headers = collect($message->getPayload()->getHeaders())->pluck('value', 'name')->only(['Subject', 'From']);
            $headersFrom = preg_match('/^(.*?)\s*<([^>]+)>$/', $headers['From'], $matches)? ['name' => trim($matches[1]), 'email' => trim($matches[2])]: null;
            if ($headersFrom === null) {
                $headersFrom = ['name' => null, 'email' => $headers['From']];
            }
            $snippet = $message->getSnippet();
            $inboxList[] = [
                'snippet' => html_entity_decode($snippet),
                'messageId' => $messageId,
                'relative_time' => (Carbon::createFromTimestamp($message->getInternalDate() / 1000))->diffForHumans(Carbon::now(), CarbonInterface::DIFF_RELATIVE_TO_NOW),
                'headers' => $headers,
                'from_email' => $headersFrom? $headersFrom['email']: null,
                'from_name' => $headersFrom? $headersFrom['name']: null,
            ];
        }
        return collect([
            'item' => $inboxList,
            'next_page_token' => $results->getNextPageToken(),
            'total' => $results->getResultSizeEstimate(),
            'result' => $results
        ]);
    }

    public function viewInboxMessage(Integration $integration, String $messageID, $request)
    {
        $this->client->setAccessToken($integration->access_token);
        $service = new \Google\Service\Gmail($this->client);

        $message = $service->users_messages->get('me', $messageID, ['format' => 'full']);
        $headers = collect($message->getPayload()->getHeaders())->pluck('value', 'name')->only(['Subject', 'From', 'Date']);
        $parts = $message->getPayload()->getParts();
        if (empty($parts)) {
            $body = base64_decode(strtr($message->getPayload()->body->data, '-_', '+/'));
        } else {
            if (array_key_exists(1, $parts)) {
                $body = base64_decode(strtr($parts[1]->body->data, '-_', '+/'));
            } else {
                //Scrub parts for text/html
                foreach (collect($parts[0]->parts) as $part) {
                    if ($part->mimeType == 'text/html') {
                        $body = base64_decode(strtr($part->body->data, '-_', '+/'));
                        break;
                    }
                }
            }
        }

        return [
            'body' => $body,
            'headers' => $headers,
            'messageID' => $messageID
        ];
    }

    public function deleteInboxMessage(Integration $integration, String $messageID, $request)
    {
        $this->client->setAccessToken($integration->access_token);
        $service = new \Google\Service\Gmail($this->client);
        //Moved to Trash and not delete
        $message = $service->users_messages->trash('me', $messageID);
        return [
            'message' => 'Message Deleted'
        ];
    }

    public function archiveInboxMessage(Integration $integration, String $messageID, $request)
    {
        $this->client->setAccessToken($integration->access_token);
        $service = new \Google\Service\Gmail($this->client);
        $batchModifyMessagesRequest = new \Google\Service\Gmail\ModifyMessageRequest();
        $batchModifyMessagesRequest->setRemoveLabelIds(['INBOX']);

        //Archieve Message
        $message = $service->users_messages->modify('me', $messageID, $batchModifyMessagesRequest);
        return [
            'message' => 'Message has been archived'
        ];
    }

    public function bulkDeleteInboxMessage(Integration $integration, array $messageIDs)
    {
        $this->client->setAccessToken($integration->access_token);
        $service = new \Google\Service\Gmail($this->client);
        foreach ($messageIDs as $messageID) {
            $service->users_messages->trash('me', $messageID);
        }
        return [
            'message' => 'Message has been deleted'
        ];
    }

    public function bulkArchiveInboxMessage(Integration $integration, array $messageIDs)
    {
        $this->client->setAccessToken($integration->access_token);
        $service = new \Google\Service\Gmail($this->client);
        $batchModifyMessagesRequest = new \Google\Service\Gmail\BatchModifyMessagesRequest();
        $batchModifyMessagesRequest->setRemoveLabelIds(['INBOX']);
        $batchModifyMessagesRequest->setIds($messageIDs);
        $message = $service->users_messages->batchModify('me', $batchModifyMessagesRequest);
        return [
            'message' => 'Message has been archived'
        ];
    }

    public function replyToMessage(Integration $integration, String $messageID, array $request)
    {
        $this->client->setAccessToken($integration->access_token);
        $service = new \Google\Service\Gmail($this->client);
        $message = $service->users_messages->get('me', $messageID);
        $headers = collect($message->getPayload()->getHeaders())->pluck('value', 'name')->only(['Subject', 'From', 'Date']);
        $headersFrom = preg_match('/^(.*?)\s*<([^>]+)>$/', $headers['From'], $matches)? ['name' => trim($matches[1]), 'email' => trim($matches[2])]: null;
        if ($headersFrom === null) {
            $headersFrom = ['name' => null, 'email' => $headers['From']];
        }

        $subject = "Re: " . $headers['Subject'];
        $toEmail = $headersFrom['email']; // You might need to extract the actual email address here
        $replyMessage = $request['message'];

        $mimeMessage = "To: $toEmail\r\n";
        $mimeMessage .= "Subject: $subject\r\n";
        $mimeMessage .= "MIME-Version: 1.0\r\n";
        $mimeMessage .= "Content-Type: text/plain; charset=utf-8\r\n";
        $mimeMessage .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $mimeMessage .= quoted_printable_encode($replyMessage);
        $encodedMessage = rtrim(strtr(base64_encode($mimeMessage), '+/', '-_'), '=');
        // Send the reply
        $rawMessage = new \Google\Service\Gmail\Message();
        $rawMessage->setRaw($encodedMessage);

        $service->users_messages->send('me', $rawMessage);

        return [
            'message' => 'Message has been sent'
        ];
    }
}
