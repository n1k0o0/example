<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Cache\PlayerRequest;
use App\Http\Requests\Dashboard\Cache\SchoolRequest;
use App\Http\Requests\Dashboard\Cache\StadiumRequest;
use App\Models\ArchiveTable;
use App\Models\LeagueRequest;
use App\Models\ScoreTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;

class CacheController extends Controller
{
    /**
     * @throws InvalidArgumentException|Throwable
     */
    public function player(PlayerRequest $request): JsonResponse
    {
        $players = Cache::get('players') ?? collect();
        $playerIndex = $players->search(fn($player) => $player['id'] === $request->id);

        if ($request->deleted) {
            $players->forget($playerIndex);
            Cache::forever('players', $players);
            Cache::store('redis')->forget("player_{$request->id}");

            return $this->respondEmpty();
        }

        if ($playerIndex) {
            $players = $players->replace([$playerIndex => $request->validated()]);
        } else {
            $players->push($request->validated());
        }

        Cache::store('redis')->set("player_{$request->id}", $request->validated());
        Cache::forever('players', $players);

        return $this->respondEmpty();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function school(SchoolRequest $request): JsonResponse
    {
        if ($request->deleted) {
            Cache::store('redis')->forget("school_{$request->id}");

            return $this->respondEmpty();
        }

        LeagueRequest::query()
            ->where('school_id', $request->id)
            ->update(['school_name' => $request->name]);

        ScoreTable::query()
            ->whereHas('leagueRequest', fn($q) => $q->where('school_id', $request->id))
            ->update(['team_name' => $request->name]);

        ArchiveTable::query()
            ->whereHas('leagueRequest', fn($q) => $q->where('school_id', $request->id))
            ->update(['team_name' => $request->name]);

        Cache::store('redis')->set("school_{$request->id}", $request->validated());

        return $this->respondEmpty();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function stadium(StadiumRequest $request): JsonResponse
    {
        if ($request->deleted) {
            Cache::store('redis')->forget("stadium_{$request->id}");

            return $this->respondEmpty();
        }

        Cache::store('redis')->set("stadium_{$request->id}", $request->validated());

        return $this->respondEmpty();
    }
}
