<?php

namespace App\Dto;

readonly class CreateReviewDto
{
    private function __construct(
        private string $episodeId,
        private string $author,
        private string $content,
    ) {
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data['episodeId'],
            $data['author'],
            $data['content'],
        );
    }

    public function getEpisodeId(): string
    {
        return $this->episodeId;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
