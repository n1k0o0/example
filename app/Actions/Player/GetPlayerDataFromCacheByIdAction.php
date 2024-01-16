<?php

namespace App\Actions\Player;

use App\Exceptions\BusinessLogicException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psr\SimpleCache\InvalidArgumentException;

class GetPlayerDataFromCacheByIdAction
{
    /**
     * @throws BusinessLogicException
     * @throws InvalidArgumentException
     */
    public function handle(int $id = null, $request = null)
    {
        if (!$id) {
            return null;
        }

        if (!Cache::store('redis')->has("player_$id")) {
            $response = Http::acceptJson()->get(config('app.root_url') . "/api/players/$id");


            if (!$response->ok()) {
                Log::critical(
                    "Ошибка при получении данных игрока - GetPlayerDataFromCacheByIdAction",
                    [$response->json()]
                );

                throw new BusinessLogicException('Ошибка при получении данных игрока');
            }

            $player = $response->json();

            Cache::store('redis')->set("player_{$player['id']}", $player);
        }

        $player = Cache::store('redis')->get("player_$id");

        if (!$player) {
            Log::critical(
                "Игрок не найден - GetPlayerDataFromCacheByIdAction",
                ['id' => $id]
            );
            throw new BusinessLogicException('Игрок не найден');
        }

        if ($request) {
            $player['requests'] = $request;
        }

        return $player;
    }
}
