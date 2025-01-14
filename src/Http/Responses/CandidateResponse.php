<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses;

use Nahid\GoogleGenerativeAI\Contracts\Arrayable;
use Nahid\GoogleGenerativeAI\Enums\Response\FinishReason;

class CandidateResponse implements Arrayable
{

    public function __construct(
        public readonly ContentResponse $content,
        public readonly ?FinishReason $finishReason = null,
    )
    {

    }


    public static function create(
        ContentResponse $content,
        ?string $finishReason = null,
    ): static
    {
        $finishReason = FinishReason::tryFrom($finishReason);
        return new static($content, $finishReason);
    }


    public function toArray(): array
    {
        return [
            'content' => $this->content->toArray(),
            'finishReason' => $this->finishReason->value,
        ];
    }
}