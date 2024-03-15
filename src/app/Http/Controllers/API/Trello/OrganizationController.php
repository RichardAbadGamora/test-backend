<?php

namespace App\Http\Controllers\API\Trello;

use Illuminate\Http\Request;
use App\Services\TrelloService;
use App\Http\Controllers\Controller;
use App\Models\Path;
use Error;

class OrganizationController extends Controller
{
    protected $trelloService;

    public function __construct(TrelloService $trelloService)
    {
        $this->trelloService = $trelloService;
    }

    public function createOrganization(Request $request, Path $path)
    {
        $data = $request->all(); // Organization data from request
        return $this->trelloService->setPath($path)->post("/organizations", $data);
    }

    public function getOrganization($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/organizations/{$id}");
    }

    public function updateOrganization(Request $request, $id, Path $path)
    {
        $data = $request->all(); // Update data from request
        return $this->trelloService->setPath($path)->put("/organizations/{$id}", $data);
    }

    public function deleteOrganization($id, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/organizations/{$id}");
    }

    public function getField($id, $field, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/organizations/{$id}/{$field}");
    }

    public function getActions($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/organizations/{$id}/actions");
    }

    public function getBoards($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/organizations/{$id}/boards");
    }

    public function getExports($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/organizations/{$id}/exports");
    }

    public function createExport(Request $request, $id, Path $path)
    {
        $data = $request->all(); // Export data from request
        return $this->trelloService->setPath($path)->post("/organizations/{$id}/exports", $data);
    }

    public function getMembers($id, Path $path)
    {
        $response = $this->trelloService->setPath($path)->get("/organizations/{$id}/members");

        if ($response->successful()) {
            $members = collect($response->json());

            $batchedRoutes = [];
            $batchSize = 10;

            foreach ($members->chunk($batchSize) as $chunkedMembers) {
                $routes = [];
                foreach ($chunkedMembers as $member) {
                    $routes[] = "/members/" . $member['id'];
                }

                $batchedRoutes[] = implode(',', $routes);
            }

            $dataContainers = [];

            foreach ($batchedRoutes as $batchRoutes) {
                $params['urls'] = $batchRoutes;
                $batchResponse = $this->trelloService->setPath($path)->get("/batch", $params);
                if ($batchResponse->successful()) {
                    $batchData = $batchResponse->json();
                    foreach ($batchData as $entry) {
                        if (isset($entry[200])) {
                            $dataContainers[] = $entry[200];
                        }
                    }
                } else {
                    // Handle error if needed
                }
            }

            return $dataContainers;
        }

        throw new Error("Unable to get organization members at this time.");
    }

    public function updateMembers(Request $request, $id, Path $path)
    {
        $data = $request->all(); // Update data from request
        return $this->trelloService->setPath($path)->put("/organizations/{$id}/members", $data);
    }

    public function getMemberships($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/organizations/{$id}/memberships");
    }

    public function getMembership($id, $idMembership, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/organizations/{$id}/memberships/{$idMembership}");
    }

    public function getPluginData($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/organizations/{$id}/pluginData");
    }

    public function getTags($id, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/organizations/{$id}/tags");
    }

    public function createTag(Request $request, $id, Path $path)
    {
        $data = $request->all(); // Tag data from request
        return $this->trelloService->setPath($path)->post("/organizations/{$id}/tags", $data);
    }

    public function updateMember(Request $request, $id, $idMember, Path $path)
    {
        $data = $request->all(); // Update data from request
        return $this->trelloService->setPath($path)->put("/organizations/{$id}/members/{$idMember}", $data);
    }

    public function deactivateMember($id, $idMember, Path $path)
    {
        return $this->trelloService->setPath($path)->put("/organizations/{$id}/members/{$idMember}/deactivated");
    }

    public function uploadLogo(Request $request, $id, Path $path)
    {
        // Handle logo upload here
    }

    public function deleteLogo($id, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/organizations/{$id}/logo");
    }

    public function removeAllMembers($id, $idMember, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/organizations/{$id}/members/{$idMember}/all");
    }

    public function deleteAssociatedDomain($id, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/organizations/{$id}/prefs/associatedDomain");
    }

    public function deleteOrgInviteRestrict($id, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/organizations/{$id}/prefs/orgInviteRestrict");
    }

    public function deleteTag($id, $idTag, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/organizations/{$id}/tags/{$idTag}");
    }

    public function getNewBillableGuests($id, $idBoard, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/organizations/{$id}/newBillableGuests/{$idBoard}");
    }
}
