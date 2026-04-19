<?php

declare(strict_types=1);

namespace App\Gif\Infrastructure\Laravel;

use App\Gif\Domain\Port\FavoriteGifRepositoryInterface;
use App\Gif\Domain\Port\GifProviderInterface;
use App\Gif\Infrastructure\Giphy\Client\GiphyHttpClient;
use App\Gif\Infrastructure\Giphy\GiphyGifProvider;
use App\Gif\Infrastructure\Persistence\Eloquent\EloquentFavoriteGifRepository;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\ServiceProvider;

final class GifServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GiphyHttpClient::class, function ($app): GiphyHttpClient {
            $config = $app['config']->get('services.giphy');

            return new GiphyHttpClient(
                http: $app->make(Factory::class),
                baseUrl: (string) ($config['base_url'] ?? ''),
                apiKey: (string) ($config['api_key'] ?? ''),
                timeoutSeconds: (int) ($config['timeout'] ?? 10),
            );
        });

        $this->app->bind(GifProviderInterface::class, GiphyGifProvider::class);
        $this->app->bind(FavoriteGifRepositoryInterface::class, EloquentFavoriteGifRepository::class);
    }
}
