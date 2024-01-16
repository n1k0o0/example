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

class CacheStadiums implements ShouldQueue
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
            ->get(config('app.root_url') . '/api/service/stadiums');

        if (!$response->ok()) {
            Log::critical('Ошибка при получении данных стадиона', [$response->json()]);

            throw new BusinessLogicException('Ошибка при получении данных стадиона');
        }

        $stadiums = $response->json()['data'];

        foreach ($stadiums as $stadium) {
            Cache::store('redis')->set("stadium_{$stadium['id']}", $stadium);
        }

        Cache::store('redis')->set("stadiumsById", true);
    }
}
