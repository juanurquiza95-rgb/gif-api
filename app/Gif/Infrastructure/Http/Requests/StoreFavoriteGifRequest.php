<?php

declare(strict_types=1);

namespace App\Gif\Infrastructure\Http\Requests;

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
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function toDto(): StoreFavoriteGifInput
    {
        return new StoreFavoriteGifInput(
            gifId: (string) $this->validated('gif_id'),
            alias: (string) $this->validated('alias'),
            userId: (int) $this->validated('user_id'),
        );
    }
}
