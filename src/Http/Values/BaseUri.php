<?php

namespace Nahid\GoogleGenerativeAI\Http\Values;

class BaseUri
{


    public function __construct(private string $baseUri)
    {
    }
    /**
     * Creates a new Base URI value object.
     */
    public static function from(string $baseUri): self
    {
        return new self($baseUri);
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        foreach (['http://', 'https://'] as $protocol) {
            if (str_starts_with($this->baseUri, $protocol)) {
                return "{$this->trim()}/";
            }
        }

        return "https://{$this->trim()}/";
    }

    public function append(string $path): string
    {
        $path = ltrim(rtrim($path, '/'), '/');
        return "{$this->toString()}{$path}";
    }

    private function trim(): string
    {
        return rtrim($this->baseUri, '/');
    }
}