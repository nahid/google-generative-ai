<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses;

use Nahid\GoogleGenerativeAI\Enums\Response\FinishReason;

class CandidatesResponse
{

    public function __construct(
        public ContentResponse $content,
        public UsageMetadataResponse $usageMetadata,
        public string $modelVersion,
        public ?FinishReason $finishReason = null
    )
    {

    }

}