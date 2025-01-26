<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses\Api;

class CacheContentTokenCount
{
    public function __construct(
        public readonly int $totalTokens,
        public readonly int $totalCachedTokens = 0,
    )
    {
    }

    public static function create(
        array $data
    ): static
    {
        return new static(
            $data['totalTokens'],
            $data['totalCachedTokens'] ?? 0,
        );
    }
}