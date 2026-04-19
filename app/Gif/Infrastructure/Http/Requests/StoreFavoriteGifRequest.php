<?php

declare(strict_types=1);

namespace App\Gif\Infrastructure\Http\Requests;

use App\Auth\Domain\Exception\UnauthenticatedException;
use App\Gif\Application\DTO\StoreFavoriteGifInput;
use Illuminate\Foundation\Http\FormRequest;

final class StoreFavoriteGifRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'gif_id' => ['required', 'string', 'min:1', 'max:100'],
            'alias' => ['required', 'string', 'min:1', 'max:255'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function toDto(): StoreFavoriteGifInput
    {
        $authenticatedUserId = (int) $this->attributes->get('auth_user_id');
        $requestUserId = $this->validated('user_id');

        if ($requestUserId !== null && (int) $requestUserId !== $authenticatedUserId) {
            throw new UnauthenticatedException();
        }

        return new StoreFavoriteGifInput(
            gifId: (string) $this->validated('gif_id'),
            alias: (string) $this->validated('alias'),
            userId: $authenticatedUserId,
        );
    }
}
