<?php

namespace Nahid\GoogleGenerativeAI\Http\Values;

use Nahid\GoogleGenerativeAI\Enums\Http\ContentType;

final class QueryParams
{
    public function __construct(
        private array $queryParams = []
    ) {
        // ..
    }
    public static function create(array $queryParams = []): self
    {
        return new self($queryParams);
    }

    public function withApiKey(string $key): self
    {
        return new self([
            ... $this->queryParams,
            'key' => $key,
        ]);
    }

    public function addApiKey(string $key): self
    {
        $this->add('key', $key);

        return $this;

    }

    public function add(string $key, string $value): self
    {
        $this->queryParams[$key] = $value;

        return $this;
    }
    
    public function toArray(): array
    {
        return $this->queryParams;
    }
}