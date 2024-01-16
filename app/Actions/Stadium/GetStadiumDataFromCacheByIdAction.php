<?php

namespace App\Actions\Stadium;

use App\Exceptions\BusinessLogicException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psr\SimpleCache\InvalidArgumentException;

class GetStadiumDataFromCacheByIdAction
{

    /**
     * @throws BusinessLogicException
     * @throws InvalidArgumentException
     */
    public function handle(int $id)
    {
        if (!Cache::store('redis')->has("stadiumsById")) {
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

        return Cache::store('redis')->get("stadium_$id");
    }
}
