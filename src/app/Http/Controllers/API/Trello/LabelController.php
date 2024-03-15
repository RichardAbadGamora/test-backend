<?php

namespace App\Http\Controllers\API\Trello;

use App\Models\Path;
use Illuminate\Http\Request;
use App\Services\TrelloService;
use App\Http\Controllers\Controller;

class LabelController extends Controller
{
    protected $trelloService;

    public function __construct(TrelloService $trelloService)
    {
        $this->trelloService = $trelloService;
    }

    public function getLabel($id, $idLabel, Path $path)
    {
        return $this->trelloService->setPath($path)->get("/labels/{$idLabel}");
    }

    public function updateLabel(Request $request, $id, $idLabel, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/labels/{$idLabel}", $data);
    }

    public function deleteLabel($id, $idLabel, Path $path)
    {
        return $this->trelloService->setPath($path)->delete("/labels/{$idLabel}");
    }

    public function updateLabelField(Request $request, $id, $idLabel, $field, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->put("/labels/{$idLabel}/{$field}", $data);
    }

    public function createLabel(Request $request, Path $path)
    {
        $data = $request->all();
        return $this->trelloService->setPath($path)->post("/labels", $data);
    }
}
