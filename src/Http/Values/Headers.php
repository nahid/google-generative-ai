<?php

namespace Nahid\GoogleGenerativeAI\Prompts\Http\Values;

use Nahid\GoogleGenerativeAI\Prompts\Enums\Http\ContentType;

final class Headers
{
    public function __construct(
        private array $headers = []
    ) {
        // ..
    }
    public static function create(array $headers = []): self
    {
        return new self($headers);
    }

    public function withContentType(ContentType $contentType, string $suffix = ''): self
    {
        return new self([
            ... $this->headers,
            'Content-Type' => $contentType->value . $suffix,
        ]);
    }



    public function add(string $key, string $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }

    public function get(string $key): string
    {
        return $this->headers[$key] ?? '';
    }
    public function toArray(): array
    {
        return $this->headers;
    }
}