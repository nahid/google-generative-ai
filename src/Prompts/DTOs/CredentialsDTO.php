<?php

namespace Nahid\GoogleGenerativeAI\Prompts\DTOs;

use Nahid\GoogleGenerativeAI\Http\Transporter;
use Nahid\GoogleGenerativeAI\Http\Values\BaseUri;

class CredentialsDTO
{
    public function __construct(
        private readonly Transporter $transporter,
        private readonly BaseUri $baseUri,
        private readonly string $key,
        private readonly string $model,
        private readonly string $version
    )
    {
    }

    public static function create(
        Transporter $transporter,
        BaseUri $baseUri,
        string $key,
        string $model,
        string $version
    ): self
    {
        return new self($transporter, $baseUri, $key, $model, $version);
    }

    public function getTransporter(): Transporter
    {
        return $this->transporter;
    }

    public function getBaseUri(): BaseUri
    {
        return $this->baseUri;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

}