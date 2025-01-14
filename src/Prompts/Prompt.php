<?php

namespace Nahid\GoogleGenerativeAI\Prompts;

use Exception;
use Nahid\GoogleGenerativeAI\Enums\RunType;
use Nahid\GoogleGenerativeAI\Http\Responses\Response;
use Nahid\GoogleGenerativeAI\Http\Responses\ResponseChunk;
use Nahid\GoogleGenerativeAI\Http\Responses\ResponseStream;
use Nahid\GoogleGenerativeAI\Http\Values\Payload;
use Nahid\GoogleGenerativeAI\Prompts\Concerns\Audio;
use Nahid\GoogleGenerativeAI\Prompts\Concerns\Document;
use Nahid\GoogleGenerativeAI\Prompts\Concerns\Functions\Func;
use Nahid\GoogleGenerativeAI\Prompts\Concerns\GoogleSearch;
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
    /**
     * @var true
     */
    private bool $generateCode = false;

    private array $tools = [];
    private array $functionDeclarations = [];

    private ?RunType $run = null;

    private array $generationConfig = [
        'max_output_tokens' => 2048,
        'temperature' => 0.5,
    ];

    public function __construct(
     private readonly CredentialsDTO $creds,
        array $generationConfig = []
    )
    {
        if (!empty($generationConfig)) {
            $this->generationConfig = $generationConfig;
        }
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
        $video = new Video($this->creds);

        $this->files[] = $video->upload($path);

        return $this;
    }
    public function withDocument(string $path): self
    {
        $document = new Document($this->creds);

        $this->files[] = $document->upload($path);

        return $this;
    }

    public function withFunctions(Func ...$fn): self
    {
        if (!is_null($this->run) && $this->run !== RunType::FUNCTION) {
            throw new Exception('Cannot add functions with other generation types');
        }

        $this->run = RunType::FUNCTION;

        $this->tools[] = [
            'function_declarations' => array_map(fn(Func $func) => $func->toArray(), $fn),
        ];

        return $this;
    }

    /**
     * @param string $mode
     * @param float $threshold
     * @return $this
     * @throws Exception
     */
    public function wantsGoogleSearch(string $mode = 'MODE_DYNAMIC', float $threshold = 1): self
    {
        if (!is_null($this->run) && $this->run !== RunType::GOOGLE_SEARCH) {
            throw new Exception('Cannot add google search with other generation types');
        }

        $google = new GoogleSearch($mode, $threshold);

        $this->tools[] = $google->toArray();

        return $this;
    }

    public function code(): self
    {
        if (!is_null($this->run) && $this->run !== RunType::CODE) {
            throw new Exception('Cannot add code with other generation types');
        }

        $this->run = RunType::CODE;

        $this->tools[] = [
            'code_execution' => new \stdClass(),
        ];

        return $this;
    }


    private function getFileDataParams(): array
    {
        return array_map(fn(FileDataDTO $file) => ['file_data' => $file->toArray()], $this->files);
    }

    public function generate(string $prompt): Response
    {
        $transporter = $this->creds->getTransporter();


        $response =  $transporter->requestContent($this->promptGenerate($prompt));

        if ($response->getStatusCode() !== 200) {
            throw new Exception('Failed to generate content');
        }

        return new Response(json_decode($response->getBody()->getContents(), true));
    }

    public function stream(string $prompt): ResponseStream
    {
        $transporter = $this->creds->getTransporter();


        $response = $transporter->requestStream($this->promptGenerate($prompt));

        return new ResponseStream($response);
    }

    protected function promptGenerate(string $prompt): Payload
    {
        $payloadSchema = [
            'contents' => [
                'parts' => [
                    [
                        'text' => $prompt
                    ],
                    ... $this->getFileDataParams(),
                ]
            ],
            'generation_config' => $this->generationConfig,
        ];

        if (!empty($this->tools)) {
            $payloadSchema['tools'] = $this->tools;
        }

//        dd($payloadSchema);

        return Payload::create()
            ->withBaseUri($this->creds->getBaseUri()->new())
            ->withModel($this->creds->getModel())
            ->withVersion($this->creds->getVersion())
            ->withApiKey($this->creds->getKey())
            ->withBody($payloadSchema);
    }

}