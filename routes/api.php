<?php

use App\Http\Controllers\Dashboard\CacheController;
use App\Http\Controllers\Dashboard\GameController;
use App\Http\Controllers\Dashboard\LeagueController;
use App\Http\Controllers\Dashboard\LeagueRequestController;
use App\Http\Controllers\Dashboard\TeamRequestController;
use App\Http\Controllers\User\PlayerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('champions-league')->as('champions_league')->group(function () {
    /**********************     Dashboard    **********************/

    Route::prefix('dashboard')->as('dashboard')->group(function () {
        /******     Jury    ***********/
        Route::prefix('games')->middleware('auth.jwt:jury')->as('jury')->group(function () {
            Route::prefix('{game}')->group(function () {
                Route::prefix('status')->group(function () {
                    Route::put('/', [GameController::class, 'updateStatus'])->name('updateStatus');
                });

                Route::prefix('pauses')->group(function () {
                    Route::post('start', [GameController::class, 'startPause'])->name('startPause');
                    Route::post('finish', [GameController::class, 'finishPause'])->name('finishPause');
                });


                Route::put('delete_goal_jury', [GameController::class, 'deleteGoalJury'])->name('deleteGoalJury');
                Route::put('add_goal_jury', [GameController::class, 'addGoalJury'])->name('addGoalJury');
            });
            Route::get('schedule', [GameController::class, 'getSchedule'])->name('getSchedule');
        });

        /******     admin    ***********/
        Route::middleware('auth.jwt:admin')->as('admin')->group(function () {
            Route::get('leagues/groups', [LeagueController::class, 'groups']);

            Route::prefix('leagues')->as('leagues.')->group(function () {
                Route::prefix('{league}')->group(function () {
                    Route::put('settings', [LeagueController::class, 'updateSettings'])->name('updateSettings');
                });
            });
            Route::apiResource('leagues', LeagueController::class);

            Route::get('league-requests/{leagueRequest}/players', [LeagueRequestController::class, 'availablePlayers']
            )->name('availablePlayers');
            Route::apiResource('league-requests', LeagueRequestController::class)->except('index');


            Route::prefix('games')->as('games.')->group(function () {
                Route::get('results', [GameController::class, 'getResults'])->name('getResults');
                Route::post('playoff', [GameController::class, 'storePlayoff'])->name('storePlayoff');


                Route::prefix('{game}')->group(function () {
                    Route::put('statistics', [GameController::class, 'updateStatistics'])->name('statistics');

                    Route::put('delete_goal_admin', [GameController::class, 'deleteGoalAdmin'])->name(
                        'delete_goal_admin'
                    );
                    Route::put('add_goal_admin', [GameController::class, 'addGoalAdmin'])->name('add_goal_admin');

                    Route::put('player-of-the-match', [GameController::class, 'updatePlayerOfTheMatch'])->name(
                        'updatePlayerOfTheMatch'
                    );
                });
            });


            Route::apiResource('games', GameController::class);


            Route::apiResource('team-requests', TeamRequestController::class);
        });

        Route::apiResource('league-requests', LeagueRequestController::class)->only('index');
        Route::apiResource('games', GameController::class)->only('show');

        Route::prefix('leagues')->as('leagues.')->group(function () {
            Route::prefix('{league}')->group(function () {
                Route::get('results', [LeagueController::class, 'results'])->name('results');
            });
        });
    });

    /**********************     User    **********************/

    Route::prefix('leagues')->as('leagues.')->group(function () {
        Route::prefix('{league}')->group(function () {
            Route::get('card', [\App\Http\Controllers\User\LeagueController::class, 'leagueCard'])->name('card');
            Route::get('results', [\App\Http\Controllers\User\LeagueController::class, 'results'])->name('results');
        });
    });

    Route::apiResource('leagues', \App\Http\Controllers\User\LeagueController::class)->only(
        'index',
        'show'
    )->middleware('user');

    Route::prefix('games')->as('games.')->group(function () {
        Route::get('schedule', [\App\Http\Controllers\User\GameController::class, 'getSchedule'])->name('getSchedule');
        Route::get('results', [\App\Http\Controllers\User\GameController::class, 'getResults'])->name('getResults');
    });

    Route::apiResource('games', \App\Http\Controllers\User\GameController::class)->only('index', 'show');

    Route::get('players/{id}/card', [PlayerController::class, 'getPlayerCard'])->name(
        'getPlayerCard'
    );
    Route::get('players/best', [PlayerController::class, 'getBestPlayers'])->name(
        'getBestPlayers'
    );
    Route::get('players/players-of-the-match', [PlayerController::class, 'getPlayersOfMatch']
    )->name(
        'getPlayersOfMatch'
    );


    Route::get(
        'league-requests/{leagueRequest}/results_table',
        [\App\Http\Controllers\User\LeagueRequestController::class, 'getResultTable']
    );

    Route::get('team-requests/{team}', [\App\Http\Controllers\User\TeamRequestController::class, 'show']);
    Route::apiResource('team-requests', \App\Http\Controllers\User\TeamRequestController::class)->only('index');

    Route::apiResource('league-requests', \App\Http\Controllers\User\LeagueRequestController::class)->only('show');

    Route::middleware('auth.jwt:user')->group(function () {
        Route::get('players/available-for-request', [PlayerController::class, 'getAvailablePlayersForRequest'])->name(
            'getAvailablePlayersForRequest'
        );

        Route::apiResource('league-requests', \App\Http\Controllers\User\LeagueRequestController::class)->except(
            'show'
        );

        Route::delete(
            'team-requests',
            [\App\Http\Controllers\User\TeamRequestController::class, 'deleteRequestByPlayerAndTeamId']
        )->name(
            'deleteRequestByPlayerAndTeamId'
        );
        Route::apiResource('team-requests', \App\Http\Controllers\User\TeamRequestController::class)->except(
            'show',
            'index'
        );
    });


    Route::middleware('service')->prefix('service')->group(function () {
        Route::post('schools', [CacheController::class, 'school'])->name('cache.school');
        Route::post('players', [CacheController::class, 'player'])->name('cache.player');
        Route::post('stadiums', [CacheController::class, 'stadium'])->name('cache.stadium');
    });
});



