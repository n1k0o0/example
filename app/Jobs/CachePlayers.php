<?php

namespace App\Jobs;

use App\Actions\Player\GetPlayersAction;
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

class CachePlayers implements ShouldQueue
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
     * @throws InvalidArgumentException|BusinessLogicException
     */
    public function handle(): void
    {
        (new GetPlayersAction())->handle('CachePlayersJob');

        $response = Http::acceptJson()->get(config('app.root_url') . '/api/players');

        if (!$response->ok()) {
            Log::critical(
                "Ошибка при получении данных игрока - GetPlayerDataFromCacheByIdAction",
                [$response->json()]
            );

            throw new BusinessLogicException('Ошибка при получении данных игрока');
        }
        $players = $response->json();

        Cache::put('players', collect($players));

        foreach ($players as $player) {
            Cache::store('redis')->set("player_{$player['id']}", $player);
        }
    }
}
