<?php

namespace Nahid\GoogleGenerativeAI\Prompts\Http\Values;

final class Parameters
{
    public function __construct(
        private readonly array $parameters = []
    ) {
        // ..
    }
    public static function create(): self
    {
        return new self();
    }

    public function add(string $key, string $value): self
    {
        return new self([
            ... $this->parameters,
            $key => $value,
        ]);
    }
    
    public function toArray(): array
    {
        return $this->parameters;
    }
}