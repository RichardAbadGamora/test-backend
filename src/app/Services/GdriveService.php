<?php

namespace App\Services;

use Google\Client;
use App\Models\Page;
use App\Models\User;
use Google\Service\Drive;
use App\Models\Integration;
use Illuminate\Http\Request;
use App\Enums\IntegrationType;
use Google\Service\Drive\Permission;
use App\Services\Integrations\IntegrationFactory;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Log;

class GdriveService
{
    protected $client;

    protected $drive;

    public function __construct()
    {
        $this->client = new Client();

        $this->client->setAuthConfig(base_path() . '/config.json');
        $this->client->addScope(Drive::DRIVE);

        $this->drive = new Drive($this->client);
    }

    private function callWithRetry(callable $callable, $maxRetries = 3)
    {
        $retryCount = 0;

        while ($retryCount < $maxRetries) {
            try {
                return $callable();
            } catch (\Exception $e) {
                Log::error("Error: " . $e->getMessage());

                $retryCount++;

                Log::error("Retry count: " . $retryCount);
                usleep(200);
            }
        }

        Log::error("Max retries reached, unable to complete the operation.");
        return null;
    }

    public function getFilesByFolderName(Request $request, Integration $integration)
    {
        $query = [
            'q' => "name = '$request->folder_name' and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
        ];

        return $this->getFiles($integration, $query);
    }

    public function getFolderId($folderName)
    {
        $params = [
            'q' => "name = '$folderName' and mimeType = 'application/vnd.google-apps.folder'",
            'fields' => 'files(id)',
        ];

        $results = $this->drive->files->listFiles($params);
        $folders = $results->getFiles();

        if (count($folders) > 0) {
            return $folders[0]->getId();
        }

        return null;
    }

    public function getFiles(Integration $integration, $query = null)
    {
        return $this->callWithRetry(function () use ($integration, $query) {
            $integration = $this->refreshTokenIfExpired($integration);

            $this->client->setAccessToken($integration->access_token);

            $params = [
                'fields' => 'files(' . implode(',', config('gdrive.file_fields')) . ')',
            ];

            if ($query) {
                $params['q'] = $query;
            }
            $results = $this->drive->files->listFiles($params);

            $files = $results->getFiles();

            return $files;
        });
    }

    private function refreshTokenIfExpired(Integration $integration)
    {
        if ($this->client->isAccessTokenExpired()) {
            $response = $this->client->fetchAccessTokenWithRefreshToken($integration->refresh_token);
            $integration->access_token = $response['access_token'];
            $integration->refresh_token = $response['refresh_token'];
            $integration->save();

            $integration->refresh();
        }

        return $integration;
    }

    public function listFiles(Integration $integration)
    {
        $query = "'root' in parents";

        return $this->getFiles($integration, $query);
    }

    public function getAllSharedFiles(Integration $integration)
    {
        $query = "sharedWithMe=true";

        return $this->getFiles($integration, $query);
    }

    public function getAllStarredFiles(Integration $integration)
    {
        $query = 'starred=true';

        return $this->getFiles($integration, $query);
    }

    public function openFile($fileId, Integration $integration)
    {
        return $this->callWithRetry(function () use ($integration, $fileId) {
            $integration = $this->refreshTokenIfExpired($integration);

            $this->client->setAccessToken($integration->access_token);

            $file = $this->drive->files->get($fileId);

            return $file;
        });
    }

    public function uploadFile(Request $request, Integration $integration)
    {
        return $this->callWithRetry(function () use ($integration, $request) {
            $integration = $this->refreshTokenIfExpired($integration);

            $this->client->setAccessToken($integration->access_token);

            $driveFile = [
                'name' => $request->file('file')->getClientOriginalName(),
            ];

            if ($request->has('folder')) {
                $driveFile['parents'] = (array)$request->folder;
            }

            $fileMetadata = new DriveFile($driveFile);

            $content = file_get_contents($request->file('file')->getPathname());
            $file = $this->drive->files->create($fileMetadata, [
                'data' => $content,
                'uploadType' => 'multipart',
            ]);

            return 'File uploaded successfully!';
        });
    }

    public function copyFileLink(Request $request, Integration $integration)
    {
        $integration = $this->refreshTokenIfExpired($integration);

        $this->client->setAccessToken($integration->access_token);

        $file = $this->drive->files->get($request->id, ['fields' => 'webViewLink']);

        $fileLink = $file->getWebViewLink();

        return $fileLink;
    }

    public function moveFileToFolder(Request $request, Integration $integration)
    {
        return $this->callWithRetry(function () use ($integration, $request) {
            $integration = $this->refreshTokenIfExpired($integration);

            $this->client->setAccessToken($integration->access_token);

            $fileId = $request->id;

            $folderId = $this->getFolderId($request->destination);

            $file = $this->drive->files->get($fileId);

            $newFile = new DriveFile();
            $newFile->setName($file->getName());
            $previousParents = $file->getParents() ? implode(',', $file->getParents()) : null;

            $this->drive->files->update($fileId, $newFile, [
                'addParents' => $folderId,
                'removeParents' => $previousParents,
            ]);

            return 'File moved successfully!';
        });
    }

    public function shareFile(Request $request, Integration $integration)
    {
        return $this->callWithRetry(function () use ($integration, $request) {
            $integration = $this->refreshTokenIfExpired($integration);

            $this->client->setAccessToken($integration->access_token);

            $fileId = $request->id;

            // Email addresses of users you want to share the file with (comma-separated)
            $userEmails = implode(',', $request->emails);

            // Create a permission object for the users
            $userPermission = new Permission([
                'type' => 'user',
                'role' => 'reader', // Adjust the role (reader, writer, commenter, etc.)
                'emailAddress' => $userEmails,
            ]);

            // Create a permission object for public access (optional)
            $publicPermission = new Permission([
                'type' => 'anyone',
                'role' => 'reader', // Adjust the role for public access
            ]);

            // Add the new permissions to the file
            $this->drive->permissions->create($fileId, $userPermission);
            $this->drive->permissions->create($fileId, $publicPermission);

            return 'File shared successfully!';
        });
    }

    public function addToStarred(Request $request, Integration $integration)
    {
        return $this->callWithRetry(function () use ($integration, $request) {
            $integration = $this->refreshTokenIfExpired($integration);

            $this->client->setAccessToken($integration->access_token);

            $file = $this->drive->files->get($request->id);

            $newFile = new DriveFile();
            $newFile->setName($file->getName());

            $newFile->setStarred(true);
            $updatedFile = $this->drive->files->update($request->id, $newFile);

            return 'File starred successfully!';
        });
    }

    public function removeFromStarred(Request $request, Integration $integration)
    {
        return $this->callWithRetry(function () use ($integration, $request) {
            $integration = $this->refreshTokenIfExpired($integration);

            $this->client->setAccessToken($integration->access_token);

            $file = $this->drive->files->get($request->id);

            $newFile = new DriveFile();
            $newFile->setName($file->getName());

            $newFile->setStarred(false);
            $updatedFile = $this->drive->files->update($request->id, $newFile);

            return 'File remove from starred successfully!';
        });
    }

    public function getFileDetails(Request $request, Integration $integration)
    {
        $integration = $this->refreshTokenIfExpired($integration);

        $this->client->setAccessToken($integration->access_token);

        $fileId = $request->id;

        try {
            $file = $this->drive->files->get($fileId);

            return $file;
        } catch (\Exception $e) {
            return 'An error occurred while retrieving file details: ' . $e->getMessage();
        }
    }

    public function renameFile(Request $request, Integration $integration)
    {
        return $this->callWithRetry(function () use ($integration, $request) {
            $integration = $this->refreshTokenIfExpired($integration);
            $this->client->setAccessToken($integration->access_token);

            $file = new DriveFile();
            $file->setName($request->name);

            $file = $this->drive->files->update($request->id, $file);

            return 'File renamed successfully!';
        });
    }

    public function deleteFile($fileId, Integration $integration)
    {
        return $this->callWithRetry(function () use ($integration, $fileId) {
            $integration = $this->refreshTokenIfExpired($integration);

            $this->client->setAccessToken($integration->access_token);

            $file = $this->drive->files->delete($fileId);

            return "File deleted successfully!";
        });
    }

    public function createFolder(Request $request, Integration $integration)
    {
        return $this->callWithRetry(function () use ($integration, $request) {
            $integration = $this->refreshTokenIfExpired($integration);

            $this->client->setAccessToken($integration->access_token);

            $folderMetadata = new DriveFile([
                'name' => $request->folder_name,
                'mimeType' => 'application/vnd.google-apps.folder',
            ]);

            $folder = $this->drive->files->create($folderMetadata);

            return $folder;
        });
    }

    public function isDeletable($fileId, Integration $integration)
    {
        $this->client->setAccessToken($integration->access_token);

        try {
            $file = $this->drive->files->get($fileId, ['fields' => 'id,permissions']);

            // Check if the authenticated user has the necessary permissions to delete
            foreach ($file->getPermissions() as $permission) {
                if ($permission->getRole() === 'owner' || $permission->getRole() === 'writer') {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function isDeletableUsingCapabilities($fileId, Integration $integration)
    {
        $this->client->setAccessToken($integration->access_token);

        try {
            $file = $this->drive->files->get($fileId, [
                'fields' => 'capabilities/trashed',
            ]);

            // Check if the 'trashed' capability is set to false, indicating it's not in the trash
            if ($file->getCapabilities()->getTrashed() === false) {
                return true;
            }

            return false; // File or folder is already in the trash
        } catch (\Exception $e) {
            return false;
        }
    }

    public function canUserManageFile($fileId, Integration $integration)
    {
        $this->client->setAccessToken($integration->access_token);

        try {
            $file = $this->drive->files->get($fileId, ['fields' => 'id,permissions']);
            $permissions = $file->getPermissions();

            foreach ($permissions as $permission) {
                if ($this->isUserManagerPermission($permission)) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function fileExists($fileId, Integration $integration)
    {
        $this->client->setAccessToken($integration->access_token);

        try {
            $this->drive->files->get($fileId);
            return true;
        } catch (\Google\Service\Exception $e) {
            if ($e->getCode() === 404) {
                return false;
            } else {
                throw $e;
            }
        }
    }

    public function restoreFileFromTrash($fileId, Integration $integration)
    {
        $this->client->setAccessToken($integration->access_token);

        try {
            $file = $this->drive->files->get($fileId);

            if ($file->getTrashed()) {
                $file->setTrashed(false);
                $this->drive->files->update($fileId, $file);

                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFilesInTrash(Integration $integration)
    {
        $this->client->setAccessToken($integration->access_token);

        try {
            $query = "trashed=true";
            $params = [
                'q' => $query,
                'fields' => 'files(' . implode(',', config('gdrive.file_fields')) . ')',
            ];

            $results = $this->drive->files->listFiles($params);
            $filesInTrash = $results->getFiles();

            return $filesInTrash;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function isUserManagerPermission(Permission $permission)
    {
        $role = $permission->getRole();
        return $role === 'owner' || $role === 'writer';
    }

    public function canUserRenameFile($fileId, Integration $integration)
    {
        return $this->canUserManageFile($fileId, $integration);
    }

    public function canUserShareFile($fileId, Integration $integration)
    {
        return $this->canUserManageFile($fileId, $integration);
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
            ->where('type', IntegrationType::GDRIVE)
            ->first();
    }

    public function generateAuthenticationURL(Page $page)
    {
        $params = base64_encode(json_encode([
            'page' => $page->hash,
        ]));
        $this->client->setState($params);
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');
        $redirect_uri = config('app.web_app_url') . '/gdrive/callback';
        $this->client->setRedirectUri($redirect_uri);


        return $this->client->createAuthUrl();
    }

    public function authenticate($request)
    {
        $stateParams = json_decode(base64_decode($request->state), true);
        $page = Page::find((new Page())->idFromHash($stateParams['page']));

        $this->client->addScope(Drive::DRIVE);
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');
        $redirect_uri = config('app.web_app_url') . '/gdrive/callback';
        $this->client->setRedirectUri($redirect_uri);
        $response = $this->client->fetchAccessTokenWithAuthCode($request->code);
        $refreshToken = $this->client->getRefreshToken();

        return $this->storeAccessKey([
            'name' => 'Drive',
            'page_id' => $page->id,
            'type' => IntegrationType::GDRIVE,
            'access_token' => $response['access_token'],
            'refresh_token' => $refreshToken,
            'expires_in' => $response['expires_in']
        ]);
    }

    public function storeAccessKey(array $data)
    {
        $page = Page::find($data['page_id']);
        $user = User::find($page->user_id);

        $item['access_token'] = $data['access_token'];
        $item['refresh_token'] = $data['refresh_token'] ?? null;
        $item['user_id'] = $user->id;
        $item['path_id'] = $page->path_id;
        $item['name'] = $user->firstname . "'s Access Token";
        $item['type'] = IntegrationType::GDRIVE;
        $integration = IntegrationFactory::create(IntegrationType::GDRIVE)->updateOrCreateToken($item);

        if ($page) {
            $page->integration_id = $integration->id;
            $page->save();
        }

        return IntegrationFactory::create(IntegrationType::GDRIVE)->updateOrCreateToken($item);
    }
}
