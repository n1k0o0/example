<?php

namespace App\Jobs;

use App\Exceptions\BusinessLogicException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psr\SimpleCache\InvalidArgumentException;

class CacheSchools implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->onQueue('cache');
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws BusinessLogicException
     * @throws InvalidArgumentException
     */
    public function handle(): void
    {
        $response = Http::acceptJson()
            ->withHeaders([
                'api-key' => config('services.external_api_key')
            ])
            ->get(config('app.root_url') . '/api/service/schools');

        if (!$response->ok()) {
            Log::critical(
                'Ошибка при получении данных Школы',
                [$response->json(), config('app.root_url') . '/service/schools']
            );

            throw new BusinessLogicException('Ошибка при получении данных Школы');
        }

        $schools = $response->json()['data'];

        foreach ($schools as $school) {
            Cache::store('redis')->set("school_{$school['id']}", $school);
        }

        Cache::store('redis')->set("schoolsById", true);
    }
}
