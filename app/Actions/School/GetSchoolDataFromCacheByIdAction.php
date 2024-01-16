<?php

namespace App\Actions\School;

use App\Exceptions\BusinessLogicException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psr\SimpleCache\InvalidArgumentException;

class GetSchoolDataFromCacheByIdAction
{

    /**
     * @throws BusinessLogicException
     * @throws InvalidArgumentException
     */
    public function handle(int $id)
    {
        if (!Cache::store('redis')->has("schoolsById")) {
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

        return Cache::store('redis')->get("school_$id");
    }
}
