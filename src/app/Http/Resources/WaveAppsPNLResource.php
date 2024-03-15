<?php

namespace App\Http\Resources;

use App\Enums\MorphKey;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WaveAppsPNLResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {

        //Return Structure
        $summary = [
            'report' => 'profit-and-loss-report',
            'header' => [
                'income' => [
                    'name' => 'Income',
                    'balance' => 0,
                ],
                'cost-of-goods-sold' => [
                    'name' => 'Cost of Goods Sold',
                    'balance' => 0.00,
                ],
                'operating-expenses' => [
                    'name' => 'Operating Expenses',
                    'balance' => 0.00,
                ],
                'net-profit' => [
                    'name' => 'Net Profit',
                    'balance' => 0.00,
                ]
            ],
            'summary' => [
                'category' => [
                    'income' => [
                        'name' => 'Income',
                        'balance' => 0,
                        'accounts' => [],
                        'percentage' => 0
                    ],
                    'cost-of-goods-sold' => [
                        'name' => 'Cost of Goods Sold',
                        'balance' => 0,
                        'accounts' => [],
                        'percentage' => 0
                    ],
                    'gross-profit' => [
                        'name' => 'Gross Profit',
                        'balance' => 0,
                        'percentage' => 0
                    ],
                    'operating-expenses' => [
                        'name' => 'Operating Expenses',
                        'balance' => 0,
                        'accounts' => [],
                        'percentage' => 0
                    ],
                    'net-profit' => [
                        'name' => 'Net Profit',
                        'balance' => 0,
                        'percentage' => 0
                    ],
                ]
            ],
            'details' => [

            ]
        ];

        foreach ($this->resource['INCOME'] as $account) {
            if ($account['balance'] > 0) {
                $summary['header']['income']['balance'] += $account['balance'];
                $summary['summary']['category']['income']['balance'] += $account['balance'];
                array_push(
                    $summary['summary']['category']['income']['accounts'],
                    [
                        'name' => $account['name'],
                        'balance' => number_format($account['balance'], 2)
                    ]
                );
            }
        }

        foreach ($this->resource['EXPENSE'] as $account) {
            if ($account['balance'] > 0) {
                $summary['header']['operating-expenses']['balance'] += $account['balance'];
                $summary['summary']['category']['operating-expenses']['balance'] += $account['balance'];
                array_push(
                    $summary['summary']['category']['operating-expenses']['accounts'],
                    [
                        'name' => $account['name'],
                        'balance' => number_format($account['balance'], 2)
                    ]
                );
            }
        }

        $summary['summary']['category']['gross-profit']['balance'] = number_format(
            $summary['summary']['category']['income']['balance'] - $summary['summary']['category']['cost-of-goods-sold']['balance'],
            2
        );
        $summary['summary']['category']['gross-profit']['percentage'] =
            ($summary['summary']['category']['gross-profit']['balance'] / $summary['summary']['category']['income']['balance'])*100;

        $summary['summary']['category']['net-profit']['balance'] = number_format(
            $summary['summary']['category']['gross-profit']['balance'] - $summary['summary']['category']['operating-expenses']['balance'],
            2
        );
        $summary['summary']['category']['net-profit']['percentage'] =
            ($summary['summary']['category']['net-profit']['balance'] / $summary['summary']['category']['income']['balance'])*100;

        $summary['summary']['category'] = array_map(function ($account) {
            $account['balance'] = number_format($account['balance'], 2);
            return $account;
        }, $summary['summary']['category']);

        $summary['header'] = array_map(function ($account) {
            $account['balance'] = number_format($account['balance'], 2);
            return $account;
        }, $summary['summary']['category']);
        return $summary;
    }
}
