<?php

namespace Nahid\GoogleGenerativeAI\Prompts;

use Exception;
use GuzzleHttp\ClientInterface;
use Http\Discovery\Psr17Factory;
use Nahid\GoogleGenerativeAI\Enums\Http\RequestType;
use Nahid\GoogleGenerativeAI\Http\Values\BaseUri;
use Nahid\GoogleGenerativeAI\Http\Values\Payload;
use Nahid\GoogleGenerativeAI\Prompts\Concerns\Audio;
use Nahid\GoogleGenerativeAI\Prompts\Concerns\Image;
use Nahid\GoogleGenerativeAI\Prompts\Concerns\Video;
use Nahid\GoogleGenerativeAI\Prompts\DTOs\CredentialsDTO;
use Nahid\GoogleGenerativeAI\Prompts\DTOs\FileDataDTO;

class Prompt
{
    /**
     * @var array<int, FileDataDTO>
     */
    private array $files = [];
    public function __construct(
     private readonly CredentialsDTO $creds
    )
    {

    }

    public function withImage(string $path): self
    {
        $image = new Image($this->creds);

        $this->files[] = $image->upload($path);


        return $this;

    }

    public function withAudio(string $path): self
    {
        $audio = new Audio($this->creds);

        $this->files[] = $audio->upload($path);

        return $this;
    }

    public function withVideo(string $path): self
    {
        $audio = new Video($this->creds);

        $this->files[] = $audio->upload($path);

        return $this;
    }

    private function getFileDataParams(): array
    {
        return array_map(fn(FileDataDTO $file) => ['file_data' => $file->toArray()], $this->files);
    }

    public function generate(string $prompt)
    {
        $transporter = $this->creds->getTransporter();

        $payload = Payload::create()
            ->withBaseUri($this->creds->getBaseUri()->new())
            ->withModel($this->creds->getModel())
            ->withVersion($this->creds->getVersion())
            ->withApiKey($this->creds->getKey())
            ->withBody([
                'contents' => [
                    'parts' => [
                        [
                            'text' => $prompt
                        ],
                        ... $this->getFileDataParams(),
                    ]
                ]
            ]);


        return $transporter->requestContent($payload);
    }

    public function stream(string $prompt)
    {
        $transporter = $this->creds->getTransporter();

        $payload = Payload::create()
            ->withBaseUri($this->creds->getBaseUri()->new())
            ->withModel($this->creds->getModel())
            ->withVersion($this->creds->getVersion())
            ->withApiKey($this->creds->getKey())
            ->withBody([
                'contents' => [
                    'parts' => [
                        [
                            'text' => $prompt
                        ]
                    ]
                ]
            ]);

        return $transporter->requestStream($payload);
    }

}