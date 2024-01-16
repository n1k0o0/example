<?php

namespace App\Services;

use App\DTO\Game\GetGamesDto;
use App\DTO\Group\IndexGroupsDto;
use App\DTO\League\CreateLeagueDto;
use App\DTO\League\UpdateLeagueDto;
use App\DTO\League\UpdateLeagueSettingsDto;
use App\Enums\Game\GameStatusEnum;
use App\Enums\League\LeagueStatusEnum;
use App\Exceptions\BusinessLogicException;
use App\Http\Resources\Game\GameResource;
use App\Http\Resources\ScoreTable\ScoreTableResource;
use App\Models\ArchiveTable;
use App\Models\Group;
use App\Models\League;
use App\Models\ScoreTable;
use App\Repositories\GameRepository;
use App\Repositories\LeagueRepository;
use App\Repositories\PlayerRepository;
use App\Repositories\ScoreTableRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Throwable;

class LeagueService
{
    private array $alphabet = [
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
    ];

    /**
     * @param ScoreTableRepository $scoreTableRepository
     * @param LeagueRepository $leagueRepository
     * @param GameRepository $gameRepository
     * @param PlayerRepository $playerRepository
     */
    public function __construct
    (
        private readonly ScoreTableRepository $scoreTableRepository,
        private readonly LeagueRepository $leagueRepository,
        private readonly GameRepository $gameRepository,
        private readonly PlayerRepository $playerRepository,
    ) {}

    /**
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     * @throws Throwable
     */
    public function create(CreateLeagueDto $data): void
    {
        DB::beginTransaction();
        try {
            $league = League::query()->create($data->except('status')->toArray());
            if ($data->avatar_upload) {
                $league->addMedia($data->avatar_upload)
                    ->toMediaCollection(League::AVATAR_MEDIA_COLLECTION);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * @throws BusinessLogicException
     * @throws Throwable
     */
    public function update(League $league, UpdateLeagueDto $data): void
    {
        DB::beginTransaction();
        try {
            if ($league->isArchived()) {
                throw new BusinessLogicException('Нельзя редактировать архивный турнир');
            }

            if ($data->status === LeagueStatusEnum::NOT_STARTED->value) {
                if ($league->games()->exists()) {
                    throw new BusinessLogicException('В этой лиге есть игры !');
                }

                ScoreTable::query()->where('league_id', $league->id)->delete();
            }

            if ($data->status === LeagueStatusEnum::CURRENT->value && !$league->isCurrent()) {
                if (!$league->groups()->exists()) {
                    throw new BusinessLogicException('Сначала создайте группы !');
                }

                foreach ($league->leagueRequests()->accepted()->get() as $leagueRequest) {
                    //SomeCode
                }
            }

            if ($data->status === LeagueStatusEnum::ARCHIVED->value && !$league->isArchived()) {
                //SomeCode

            }

            $league->update($data->all());

            if ($data->avatar_upload) {
                $league->addMedia($data->avatar_upload)
                    ->toMediaCollection(League::AVATAR_MEDIA_COLLECTION);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * @throws BusinessLogicException
     * @throws Throwable
     */
    public function updateSettings(League $league, UpdateLeagueSettingsDto $data): void
    {
        if (!$league->isNotStarted()) {
            throw new BusinessLogicException('Нельзя измять группы для текущих/архивных турниров');
        }

        DB::beginTransaction();
        try {
            if ($data->groups && $league->groups !== $data->groups) {
                $league->groups()->delete();
                $this->createGroups($data, $league);
            } elseif (!$data->groups) {
                $league->groups()->delete();
            }

            $league->update(['groups' => $data->groups]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * @throws UnknownProperties
     * @throws BusinessLogicException
     */
    public function card(League $league): object
    {
        $schedule = $this->gameRepository->getGames(
            new GetGamesDto([
                'league_id' => $league->id,
                'playoff' => 1,
                'statuses' => [GameStatusEnum::NOT_STARTED->value, GameStatusEnum::STARTED->value]
            ]),
            limit: 4,
            orderDirection: 'desc',
            orderBy: 'status'
        );
        $resultsGroup = $this->gameRepository->getResults(
            new GetGamesDto([
                'league_id' => $league->id,
                'playoff' => 1
            ]),
            limit: 4
        );

        $resultsPlayoff = collect(
            GameResource::collection(
                $this->gameRepository->getGames(
                    new GetGamesDto([
                        'league_id' => $league->id,
                        'playoff' => 2
                    ]),
                    orderDirection: 'desc'
                )
            )
        )
            ->groupBy(function ($item) {
                return $item["start_place"] . "/" . $item["round"];
            })
            ->sortKeys()
            ->map(function ($games, $key) {
                [$place, $round] = explode('/', $key);


                if ((int)$round === 1) {
                    $arr = [];

                    foreach ($games->sortBy('place_from') as $game) {
                        $arr[] = [
                            'round' => $round,
                            'place' => $game['place_from'] . '-' . $game['place_to'],
                            'games' => [$game]
                        ];
                    }
                    return $arr;
                }

                return (object)[
                    'round' => $round,
                    'place' => $place,
                    'games' => $games
                ];
            })
            ->values()
            ->flatten(1)
            ->sortBy([
                function ($a, $b) {
                    $as = json_decode(json_encode($a, JSON_THROW_ON_ERROR | true), true, 512, JSON_THROW_ON_ERROR);
                    $bs = json_decode(json_encode($b, JSON_THROW_ON_ERROR | true), true, 512, JSON_THROW_ON_ERROR);

                    [, $maxA] = explode('-', $as['place']);
                    [, $maxB] = explode('-', $bs['place']);
                    return $maxA <=> $maxB;
                },
                function ($a, $b) {
                    $as = json_decode(json_encode($a, JSON_THROW_ON_ERROR | true), true, 512, JSON_THROW_ON_ERROR);
                    $bs = json_decode(json_encode($b, JSON_THROW_ON_ERROR | true), true, 512, JSON_THROW_ON_ERROR);

                    [$minA,] = explode('-', $as['place']);
                    [$minB,] = explode('-', $bs['place']);
                    return $minB <=> $minA;
                }
            ])
            ->values();

        $bestPlayers = $this->playerRepository->getBestPlayers(
            ['league_id' => $league->id],
            6
        );
        $playersOfTheMatch = $this->playerRepository->getPlayersOfMatch(
            ['league_id' => $league->id],
            limit: 6
        );

        $scoreTable =
            collect(
                ScoreTableResource::collection(
                    $this->scoreTableRepository->getResultsTable(
                        leagueId: $league->id,
                    )
                )
            )
                ->groupBy('group.name')->sortKeys()->map(function ($teams, $key) {
                    return [
                        'group' => $key,
                        'teams' => $teams,
                    ];
                })->values();


        $resultsTable = $this->leagueRepository->getResults($league);

        return (object)[
            'schedule' => $schedule,
            'resultsGroup' => $resultsGroup,
            'resultsPlayoff' => $resultsPlayoff,
            'resultsTable' => $resultsTable,
            'bestPlayers' => $bestPlayers,
            'scoreTable' => $scoreTable,
            'playersOfTheMatch' => $playersOfTheMatch
        ];
    }

    /**
     * @param UpdateLeagueSettingsDto $data
     * @param League $league
     * @return void
     */
    public function createGroups(UpdateLeagueSettingsDto $data, League $league): void
    {
        for ($i = 0; $i < $data->groups; $i++) {
            Group::query()->create([
                'league_id' => $league->id,
                'name' => $this->alphabet[$i]
            ]);
        }
    }

    public function getGroups(IndexGroupsDto $data): Collection|array
    {
        return Group::query()
            ->with('leagueRequests')
            ->when($data->league_id, fn($q) => $q->where('league_id', $data->league_id))
            ->when(
                $data->league_request_id,
                fn($q) => $q->whereHas('league.leagueRequests', fn($q) => $q->where('id', $data->league_request_id))
            )
            ->get();
    }

    public function getResults(League $league): array
    {
        return $this->leagueRepository->getResults($league);
    }
}
