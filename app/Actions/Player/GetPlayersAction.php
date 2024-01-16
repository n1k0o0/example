<?php

namespace App\Actions\Player;

use App\Exceptions\BusinessLogicException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetPlayersAction
{
    public function handle(string $title): Collection|array|null
    {
        return Cache::rememberForever('players', static function () use ($title) {
            $page = 1;
            $limit = 500;
            $players = [];

            start:

            $response = Http::acceptJson()
                ->withHeaders([
                    'api-key' => config('services.external_api_key')
                ])
                ->get(config('app.root_url') . '/api/service/players', ['limit' => $limit, 'page' => $page]);

            if (!$response->ok()) {
                Log::critical("Ошибка при получении данных игрока - $title", [$response->json()]);

                throw new BusinessLogicException('Ошибка при получении данных игрока');
            }

            $lastPage = $response->json('meta')['last_page'];

            $players = array_merge($players, $response->json('data'));

            if ($lastPage > $page) {
                ++$page;
                goto start;
            }

            return collect($players);
        });
    }
}
