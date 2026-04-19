<?php

declare(strict_types=1);

namespace App\Gif\Infrastructure\Http\Controllers;

use App\Gif\Application\UseCase\GetGifById\GetGifByIdUseCase;
use App\Gif\Application\UseCase\SearchGifs\SearchGifsUseCase;
use App\Gif\Application\DTO\GetGifByIdInput;
use App\Gif\Application\UseCase\StoreFavoriteGif\StoreFavoriteGifUseCase;
use App\Gif\Infrastructure\Http\Requests\SearchGifsRequest;
use App\Gif\Infrastructure\Http\Requests\StoreFavoriteGifRequest;
use Illuminate\Http\JsonResponse;

final class GifController
{
    public function __construct(
        private SearchGifsUseCase $searchGifsUseCase,
        private GetGifByIdUseCase $getGifByIdUseCase,
        private StoreFavoriteGifUseCase $storeFavoriteGifUseCase,
    ) {
    }

    public function search(SearchGifsRequest $request): JsonResponse
    {
        $response = ($this->searchGifsUseCase)($request->toDto());

        return response()->json($response->toArray());
    }

    public function show(string $id): JsonResponse
    {
        $response = ($this->getGifByIdUseCase)(
            new GetGifByIdInput($id)
        );

        return response()->json([
            'data' => $response->toArray(),
        ]);
    }

    public function storeFavorite(StoreFavoriteGifRequest $request): JsonResponse
    {
        $response = ($this->storeFavoriteGifUseCase)($request->toDto());

        return response()->json($response->toArray(), 201);
    }
}
