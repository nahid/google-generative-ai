<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses;

use ArrayAccess;
use Countable;
use Nahid\GoogleGenerativeAI\Contracts\Arrayable;
use Traversable;

/**
 * @implements IterableArray<ResponseChunk>
 * @implements ArrayAccess<int, ResponseChunk>
*  @implements Traversable<int, ResponseChunk>
 */
class Response implements Arrayable
{
    /**
     * @var array<int, CandidateResponse>
     */
    protected array $candidates = [];

    protected UsageMetadataResponse $usageMetadata;

    protected string $modelVersion;

    public function __construct(array $data)
    {
        foreach ($data['candidates'] as $candidate) {
            $this->candidates[] = CandidateResponse::create(ContentResponse::create($candidate['content']), $candidate['finishReason'] ?? null);
        }

        $this->usageMetadata = UsageMetadataResponse::create(
            $data['usageMetadata']['promptTokenCount'],
            $data['usageMetadata']['totalTokenCount'],
            $data['usageMetadata']['candidatesTokenCount'] ?? null
        );

        $this->modelVersion = $data['modelVersion'];

/*        foreach ($data as $chunk) {
            $this->chunk[] = ResponseChunk::create(
                $chunk['candidates'],
                UsageMetadataResponse::create(
                    $chunk['usageMetadata']['promptTokenCount'],
                    $chunk['usageMetadata']['totalTokenCount'],
                    $chunk['usageMetadata']['candidateTokenCount'] ?? null
                ),

                $chunk['modelVersion'],
                $chunk['finishReason'] ?? null
            );

        }*/
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