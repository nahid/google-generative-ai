<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses;

use Nahid\GoogleGenerativeAI\Contracts\Arrayable;
use Nahid\GoogleGenerativeAI\Enums\Response\FinishReason;

class ResponseChunk implements Arrayable
{

    /**
     * @var array<int, CandidateResponse>
     */
    protected array $candidates;

    /**
     * ResponseChunk constructor.
     * @param array $candidates
     * @param UsageMetadataResponse $usageMetadata
     * @param string $modelVersion
     * @param FinishReason|null $finishReason
     */
    public function __construct(
        array $candidates,
        public readonly UsageMetadataResponse $usageMetadata,
        public readonly string                $modelVersion,
        public ?FinishReason                  $finishReason = null
    )
    {
        foreach ($candidates as $candidate) {
            $this->candidates[] = CandidateResponse::create(ContentResponse::create($candidate['content']));
        }

    }


    public static function create(
        array     $candidates,
        UsageMetadataResponse $usageMetadata,
        string                $modelVersion,
        ?FinishReason         $finishReason = null,
    ): static
    {
        return new static($candidates, $usageMetadata, $modelVersion, $finishReason);
    }

    /**
     * @return array<int, CandidateResponse>
     */
    public function getCandidates(): array
    {
        return $this->candidates;
    }

    public function toArray(): array
    {
        return [
            'candidates' => array_map(fn(CandidateResponse $candidate) => $candidate->toArray(), $this->candidates),
            'usageMetadata' => $this->usageMetadata->toArray(),
            'modelVersion' => $this->modelVersion,
            'finishReason' => $this->finishReason?->value,
        ];
    }
}