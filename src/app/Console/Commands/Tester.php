<?php

namespace App\Console\Commands;

use App\Services\TrelloService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Tester extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tester';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // curl 'https://api.trello.com/1/members/me/boards?key={yourKey}&token={yourToken}'
        $params = [
            'key' => config('trello.api_key'),
            'token' => config('trello.token'),
            'fields' => ['name', 'url'],
        ];
        $response = Http::get(TrelloService::TRELLO_URL . '/members/me/boards', $params);

        dd($response->json());
        return 0;
    }
}
