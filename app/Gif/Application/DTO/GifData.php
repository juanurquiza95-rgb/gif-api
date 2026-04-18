<?php

declare(strict_types=1);

namespace App\Gif\Application\DTO;

use App\Gif\Domain\Entity\Gif;

final class GifData
{
    public function __construct(
        public string $id,
        public string $title,
        public string $url,
        public string $previewUrl,
        public ?string $username,
        public ?string $source,
    ) {
    }

    public static function fromDomain(Gif $gif): self
    {
        return new self(
            id: $gif->id,
            title: $gif->title,
            url: $gif->url,
            previewUrl: $gif->previewUrl,
            username: $gif->username,
            source: $gif->source,
        );
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->url,
            'preview_url' => $this->previewUrl,
            'username' => $this->username,
            'source' => $this->source,
        ];
    }
}
