<?php

namespace App\Dto;

readonly class EpisodeViewDto
{
    private function __construct(
        private EpisodeDto $episode,
        private float $averageRating,
        private array $reviews,
    ) {
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data['episode'],
            (float) $data['averageRating'],
            $data['latestReviews']
        );
    }

    public function getEpisode(): EpisodeDto
    {
        return $this->episode;
    }

    public function getAverageRating(): float
    {
        return $this->averageRating;
    }

    public function getReviews(): array
    {
        return $this->reviews;
    }
}
