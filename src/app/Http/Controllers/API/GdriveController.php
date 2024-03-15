<?php

namespace App\Http\Controllers\API;

use App\Models\Page;
use App\Models\Integration;
use Illuminate\Http\Request;
use App\Services\GdriveService;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\RenameDriveFileRequest;
use App\Http\Requests\API\ShareDriveRequest;

class GdriveController extends Controller
{
    protected $gdriveService;

    public function __construct()
    {
        $this->gdriveService = new GdriveService();
    }

    public function index(Integration $integration)
    {
        if ($integration) {
            return $this->resolve($integration);
        }
        return $this->reject($integration);
    }

    public function generateURL(Page $page, Request $request)
    {
        return $this->resolve($this->gdriveService->generateAuthenticationURL($page));
    }

    public function authenticate(Request $request)
    {
        $integration = $this->gdriveService->authenticate($request);

        return $this->resolve($integration);
    }

    public function listFiles(Integration $integration)
    {
        return $this->gdriveService->listFiles($integration);
    }

    public function getAllSharedFiles(Integration $integration)
    {
        return $this->gdriveService->getAllSharedFiles($integration);
    }

    public function getAllStarredFiles(Integration $integration)
    {
        return $this->gdriveService->getAllStarredFiles($integration);
    }

    public function openFile($fileId, Integration $integration)
    {
        return $this->gdriveService->openFile($fileId, $integration);
    }

    public function uploadFile(Request $request, Integration $integration)
    {
        return $this->gdriveService->uploadFile($request, $integration);
    }

    public function deleteFile($fileId, Integration $integration)
    {
        return $this->gdriveService->deleteFile($fileId, $integration);
    }

    public function createFolder(Request $request, Integration $integration)
    {
        return $this->gdriveService->createFolder($request, $integration);
    }

    public function getFileDetails(Request $request, Integration $integration)
    {
        return $this->gdriveService->getFileDetails($request, $integration);
    }

    public function renameFile(RenameDriveFileRequest $request, Integration $integration)
    {
        return $this->gdriveService->renameFile($request, $integration);
    }

    public function moveFileToFolder(Request $request, Integration $integration)
    {
        return $this->gdriveService->moveFileToFolder($request, $integration);
    }

    public function shareFile(ShareDriveRequest $request, Integration $integration)
    {
        return $this->gdriveService->shareFile($request, $integration);
    }

    public function addToStarred(Request $request, Integration $integration)
    {
        return $this->gdriveService->addToStarred($request, $integration);
    }

    public function removeFromStarred(Request $request, Integration $integration)
    {
        return $this->gdriveService->removeFromStarred($request, $integration);
    }
}
