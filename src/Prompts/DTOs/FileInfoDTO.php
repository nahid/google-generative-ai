<?php

namespace Nahid\GoogleGenerativeAI\Prompts\DTOs;

class FileInfoDTO
{
    public function __construct(
        private readonly string $mimeType,
        private readonly string $baseName,
    )
    {
    }

    public static function create(string $mimeType, string $baseName): self
    {
        return new self($mimeType, $baseName);
    }

    public function toArray(): array
    {
        return [
            'mimeType' => $this->mimeType,
            'baseName' => $this->baseName,
        ];
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getBaseName(): string
    {
        return $this->baseName;
    }
}