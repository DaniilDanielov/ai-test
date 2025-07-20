<?php

namespace App\Repository\Contracts;

use App\Dto\EpisodeViewDto;

interface EpisodeRepositoryInterface
{
    /**
     * Получение данных о всех эпизодах
     *
     * @param int $page
     * @return array
     */
    public function findAllEpisodesByPage(int $page = 1): array;

    /**
     * Получение данных об эпизоде
     *
     * @param int $id
     * @return array|null
     */
    public function getEpisodeData(int $id): ?EpisodeViewDto;
}
