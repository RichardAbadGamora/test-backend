<?php

use App\Enums\IntegrationType;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GmailController;
use App\Http\Controllers\MojoAuthController;
use App\Models\Integration;
use App\Services\Integrations\IntegrationFactory;
use Google\Client;
use Illuminate\Support\Facades\Route;
use App\Models\User;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return view('welcome');
});

Route::get('/trello-callback', function () {
    $url = request()->fullUrl(); // Get the current URL with all query parameters

    // Parse the URL
    $parts = parse_url($url);

    dd($parts);

    // Get the token value from the URL fragment
    $fragment = $parts['fragment'];
    parse_str($fragment, $params);
    $token = isset($params['token']) ? $params['token'] : null;
    dd($token);
});

//https://trello.com/1/authorize?expiration=never&name=MyPersonalToken&scope=read,write,account&response_type=token&key=ea9c22d3d873ec8c558fff977c202999&return_url=http://localhost:8000/trello-callback

Route::get('/magic-link/validate', [MojoAuthController::class, 'validateMagicLink']);

Route::get('/waveapps', function (){

    $endpoint = "https://gql.waveapps.com/graphql/public";//this is provided by graphcms
    $authToken = "hUuU2pgUIcr49SEd5Txs6RDO4eTuIn";//this is provided by graphcms
    $qry = 'query ($businessId: ID!, $page: Int!, $pageSize: Int!) {
        business(id: $businessId) {
          id
          accounts(page: $page, pageSize: $pageSize, types: [INCOME]) {
            pageInfo {
              currentPage
              totalPages
              totalCount
            }
            edges {
              node {
                id
                name
                description
                displayId
                balance
                type {
                  name
                  value
                }
                subtype {
                  name
                  value
                }
                normalBalanceType
                isArchived
              }
            }
          }
        }
      }';
    $variables = [
        'businessId' => 'QnVzaW5lc3M6NDg1ZjQxYTktYmIyYi00NmM5LTgzOTktMDU4NTMyYjg0YmNm',
        'page' => 1,
        'pageSize' => 10
    ];

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: Bearer '.$authToken;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["query" => $qry, 'variables' => $variables]));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    return $result;

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
});

