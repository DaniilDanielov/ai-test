<?php

namespace App\Dto;

readonly class EpisodeDto
{
    //todo подумать насчет валидации и получения некорректных значений
    private function __construct(
        private int    $id,
        private string $name,
        private string $airDate,
        private string $episodeString,
        private array  $characters,
        private int    $season,
        private int    $episodeNumber
    )
    {
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['air_date'],
            $data['episode'],
            $data['characters'],
            $data['season'],
            $data['episode_number'],
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAirDate(): string
    {
        return $this->airDate;
    }

    public function getCharacters(): array
    {
        return $this->characters;
    }

    public function getSeason(): int
    {
        return $this->season;
    }

    public function getEpisodeNumber(): int
    {
        return $this->episodeNumber;
    }

    public function getEpisodeString(): string
    {
        return $this->episodeString;
    }
}
