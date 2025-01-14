<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses;

use Nahid\GoogleGenerativeAI\Contracts\Arrayable;

class ResponseChunk implements Arrayable
{
    /**
     * @var array<int, CandidateResponse>
     */
    public readonly array $candidates;

    /**
     * ResponseChunk constructor.
     * @param array $candidates
     * @param UsageMetadataResponse $usageMetadata
     * @param string $modelVersion
     */
    public function __construct(
        array $candidates,
        public readonly UsageMetadataResponse $usageMetadata,
        public readonly string                $modelVersion,
    )
    {
        $candidatesData = [];
        foreach ($candidates as $candidate) {
            $candidatesData[] = CandidateResponse::create(ContentResponse::create($candidate['content']));
        }

        $this->candidates = $candidatesData;

    }


    public static function create(
        array     $candidates,
        UsageMetadataResponse $usageMetadata,
        string                $modelVersion,
    ): static
    {
        return new static($candidates, $usageMetadata, $modelVersion);
    }


    public function toArray(): array
    {
        return [
            'candidates' => array_map(fn(CandidateResponse $candidate) => $candidate->toArray(), $this->candidates),
            'usageMetadata' => $this->usageMetadata->toArray(),
            'modelVersion' => $this->modelVersion,
        ];
    }
}