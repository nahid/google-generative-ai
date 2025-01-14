<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses;

use Nahid\GoogleGenerativeAI\Contracts\Arrayable;

class PartResponse implements Arrayable
{
    public function __construct(
        public readonly string $text,
    )
    {

    }

    public static function create(
        string $text,
    ): static
    {
        return new static($text);
    }

    public function toArray(): array
    {
        return [
            'text' => $this->text,
        ];
    }
}