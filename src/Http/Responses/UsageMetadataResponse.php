<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses;

use Nahid\GoogleGenerativeAI\Contracts\Arrayable;

class UsageMetadataResponse implements Arrayable
{
    public function __construct(
        public int $promptTokenCount,
        public int $totalTokenCount,
        public ?int $candidateTokenCount = null,
    )
    {

    }

    public static function create(
        int $promptTokenCount,
        int $totalTokenCount,
        ?int $candidateTokenCount = null,
    ): static
    {
        return new static($promptTokenCount, $totalTokenCount, $candidateTokenCount);
    }


    public function toArray(): array
    {
        return [
            'promptTokenCount' => $this->promptTokenCount,
            'totalTokenCount' => $this->totalTokenCount,
            'candidatesTokenCount' => $this->candidateTokenCount,
        ];
    }
}