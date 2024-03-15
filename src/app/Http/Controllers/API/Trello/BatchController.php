<?php

namespace App\Http\Controllers\API\Trello;

use Illuminate\Http\Request;
use App\Services\TrelloService;
use App\Http\Controllers\Controller;
use App\Models\Path;

class BatchController extends Controller
{
    protected $trelloService;

    public function __construct(TrelloService $trelloService)
    {
        $this->trelloService = $trelloService;
    }

    public function getBatch(Request $request, Path $path)
    {
        $urls = [];
        $data = [
            'urls' => $urls,
        ];

        return $this->trelloService->get('/batch', $data);
    }
}
