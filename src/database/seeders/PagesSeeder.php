<?php

namespace Database\Seeders;

use App\Models\Path;
use App\Services\PathService;
use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (config('app.env') === 'local') {
            $paths = Path::all();

            foreach ($paths as $path) {
                if ($path->pages->isEmpty()) {
                    app(PathService::class)->seedDefaultPages($path);
                }
            }
        }
    }
}
