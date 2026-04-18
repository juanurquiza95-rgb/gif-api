<?php

declare(strict_types=1);

namespace App\Gif\Application\UseCase\GetGifById;

use App\Gif\Application\DTO\GetGifByIdInput;
use App\Gif\Application\DTO\GifData;
use App\Gif\Domain\Port\GifProviderInterface;
use InvalidArgumentException;

final class GetGifByIdUseCase
{
    public function __construct(
        private GifProviderInterface $gifProvider,
    ) {
    }

    public function __invoke(GetGifByIdInput $input): GifData
    {
        $id = trim($input->id);

        if ($id === '') {
            throw new InvalidArgumentException('The GIF id cannot be empty.');
        }

        return GifData::fromDomain(
            $this->gifProvider->findById($id)
        );
    }
}
