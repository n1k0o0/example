<?php
/** @noinspection PhpUnusedParameterInspection */

namespace App\Policies;

use App\Models\League;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class LeaguePolicy
{
    /**
     * Determine whether the user can delete the model.
     *
     * @param Model|null $user
     * @param League $league
     * @return Response|bool
     */
    public function delete(?Model $user, League $league): Response|bool
    {
        return $league->isNotStarted()
            ? Response::allow()
            : Response::deny('Нельзя удалять начатые/завершенные турниры');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param Model|null $user
     * @param League $league
     * @return Response|bool
     */
    public function createRequest(?Model $user, League $league): Response|bool
    {
        return $league->isNotStarted()
            ? Response::allow()
            : Response::deny("Для подачи заявки турнир должен быть в статусе 'Планируется'");
    }

    public function createGame(?Model $user, League $league): Response
    {
        return $league->games()->whereNotNull('round')->exists()
            ? Response::deny('Во время плейоффах нельзя создавать матчей')
            : Response::allow();
    }

}
