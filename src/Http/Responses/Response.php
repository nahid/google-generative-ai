<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses;

use Nahid\GoogleGenerativeAI\Contracts\Arrayable;

class Response implements Arrayable
{
    /**
     * @var array<int, CandidateResponse>
     */
    public readonly array $candidates;

    public readonly UsageMetadataResponse $usageMetadata;

    public readonly string $modelVersion;

    public function __construct(array $data)
    {
        $candidates = [];
        foreach ($data['candidates'] as $candidate) {
            $candidates[] = CandidateResponse::create(ContentResponse::create($candidate['content']), $candidate['finishReason'] ?? null);
        }

        $this->candidates = $candidates;
        $this->usageMetadata = UsageMetadataResponse::create(
            $data['usageMetadata']['promptTokenCount'],
            $data['usageMetadata']['totalTokenCount'],
            $data['usageMetadata']['candidatesTokenCount'] ?? null
        );

        $this->modelVersion = $data['modelVersion'];
    }

    public static function create(array $data): static
    {
        return new static($data);
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