<?php

namespace Nahid\GoogleGenerativeAI\Prompts\DTOs;

class FileDataDTO
{
    public function __construct(
        private string $mimeType,
        private string $fileUri,
    )
    {
    }

    public static function create(string $mimeType, string $fileUri): self
    {
        return new self($mimeType, $fileUri);
    }

    public function toArray(): array
    {
        return [
            'mimeType' => $this->mimeType,
            'fileUri' => $this->fileUri,
        ];
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getFileUri(): string
    {
        return $this->fileUri;
    }

}