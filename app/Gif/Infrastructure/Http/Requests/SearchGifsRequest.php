<?php

declare(strict_types=1);

namespace App\Gif\Infrastructure\Http\Requests;

use App\Gif\Application\DTO\SearchGifsInput;
use Illuminate\Foundation\Http\FormRequest;

final class SearchGifsRequest extends FormRequest
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
            'query' => ['required', 'string', 'min:1', 'max:255'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'offset' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function toDto(): SearchGifsInput
    {
        return new SearchGifsInput(
            query: $this->input('query'),
            limit: $this->integer('limit', 25),
            offset: $this->integer('offset', 0),
        );
    }
}
