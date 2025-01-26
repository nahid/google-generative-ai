<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses\Api;

class Model
{
    public function __construct(
        public readonly string $name,
        public readonly string $version,
        public readonly string $displayName,
        public readonly string $description,
        public readonly int $inputTokenLimit,
        public readonly int $outputTokenLimit,
        public readonly float $temperature,
        public readonly float $topP,
        public readonly float $topK,
        public readonly float $maxTemperature,
        /**
         * @var array<int, string>
         */
        public readonly array $supportedGenerationMethods
    )
    {

    }

    public static function create(
        array $data
    ): static
    {

        return new static(
            $data['name'],
            $data['version'],
            $data['displayName'],
            $data['description'],
            $data['inputTokenLimit'],
            $data['outputTokenLimit'],
            $data['temperature'] ?? 0.0,
            $data['topP'] ?? 0.0,
            $data['topK'] ?? 0.0,
            $data['maxTemperature'] ?? 0.0,
            $data['supportedGenerationMethods'] ?? []
        );
    }

}