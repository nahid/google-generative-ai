<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses;

class UsageMetadataResponse
{
    public function __construct(
        public int $promptTokenCount,
        public int $totalTokenCount,
        public ?int $candidateTokenCount = null,
    )
    {

    }

}