<?php

namespace Nahid\GoogleGenerativeAI;

use GuzzleHttp\ClientInterface;
use Nahid\GoogleGenerativeAI\Enums\Http\Method;
use Nahid\GoogleGenerativeAI\Enums\Http\RequestType;
use Nahid\GoogleGenerativeAI\Http\Responses\EmptyResponse;
use Nahid\GoogleGenerativeAI\Http\Values\Payload;
use Nahid\GoogleGenerativeAI\Prompts\DTOs\CredentialsDTO;
use Psr\Http\Message\ResponseInterface;

class CacheContents
{
    
    private array $payload = [
        'model' => 'models/gemini-1.5-flash-001',
        'contents' => [],
        'systemInstruction'=> [],
        'ttl' => '300s',
    ];

    public function __construct(public CredentialsDTO $creds)
    {

    }
    public function withFile(string $path): static
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException('File not found');
        }

        $supportedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/heic',
            'image/heif',
            'text/plain',
            'application/pdf',
            'application/x-javascript',
            'text/javascript',
            'application/json',
            'text/xml',
            'application/x-python',
            'text/python',
            'text/html',
            'text/css',
            'text/csv',
            'text/md',
            'text/rtf',
        ];
        $mime = mime_content_type($path);
        if (!in_array($mime, $supportedMimeTypes)) {
            throw new \InvalidArgumentException('Invalid file type');
        }

        $content['parts'][] = [
            'inline_data' => [
                'mime_type' => $mime,
                'data' => base64_encode(file_get_contents($path)),
            ]
        ];
        $content['role'] = 'user';

        $this->payload['contents'][] = $content;

        return $this;
    }

    public function withSystemInstruction(string $text): static
    {
        $this->payload['systemInstruction'] = [
            'parts' => [
                [
                    'text' => $text
                ]
            ],
        ];

        return $this;
    }



    public function withModel(string $model): static
    {
        $this->payload['model'] = 'models/' . $model;

        return $this;
    }

    public function withTtl(string $ttl): static
    {
        $this->payload['ttl'] = $ttl;

        return $this;
    }

    public function create()
    {
//        dd($this->payload);
        $transporter = $this->creds->getTransporter();
        $baseUrl = $this->creds->getBaseUri()->new();

        $baseUrl->append($this->creds->getVersion())
            ->append('/cachedContents');

        $payload = Payload::create()
            ->withBaseUri($baseUrl)
            ->withApiKey($this->creds->getKey())
            ->withBody($this->payload)
            ->withMethod(Method::POST);


        $request = $payload->toRequest(RequestType::API);

        return $transporter->send(fn (ClientInterface $client) => $client->sendRequest($request));

    }

    public function list(int $pageSize = 20, ?string $pageToken = null): ResponseInterface
    {
        $transporter = $this->creds->getTransporter();
        $baseUrl = $this->creds->getBaseUri()->new();

        $baseUrl->append($this->creds->getVersion())
            ->append('/cachedContents');

        $payload = Payload::create()
            ->withBaseUri($baseUrl)
            ->withApiKey($this->creds->getKey())
            ->withMethod(Method::GET);

        if ($pageSize) {
            $payload->queryParams()->add('pageSize', $pageSize);
        }

        if ($pageToken) {
            $payload->queryParams()->add('pageToken', $pageToken);
        }

        $request = $payload->toRequest(RequestType::API);

        return $transporter->send(fn (ClientInterface $client) => $client->sendRequest($request));
    }

    public function get(string $name): ResponseInterface
    {
        if (str_contains($name, 'cachedContents/')) {
            $name = str_replace('cachedContents/', '', $name);
        }

        $transporter = $this->creds->getTransporter();
        $baseUrl = $this->creds->getBaseUri()->new();

        $baseUrl->append($this->creds->getVersion())
            ->append('/cachedContents/' . $name);


        $payload = Payload::create()
            ->withBaseUri($baseUrl)
            ->withApiKey($this->creds->getKey())
            ->withMethod(Method::GET);

        $request = $payload->toRequest(RequestType::API);

        return $transporter->send(fn (ClientInterface $client) => $client->sendRequest($request));
    }

    public function updateTTL(string $name, string $ttl): ResponseInterface
    {
        if (str_contains($name, 'cachedContents/')) {
            $name = str_replace('cachedContents/', '', $name);
        }

        $transporter = $this->creds->getTransporter();
        $baseUrl = $this->creds->getBaseUri()->new();

        $baseUrl->append($this->creds->getVersion())
            ->append('/cachedContents/' . $name);

        $body = [
            'ttl' => $ttl
        ];

        $payload = Payload::create()
            ->withBaseUri($baseUrl)
            ->withApiKey($this->creds->getKey())
            ->withBody($body)
            ->withMethod(Method::PATCH);


        $request = $payload->toRequest(RequestType::API);

        return $transporter->send(fn (ClientInterface $client) => $client->sendRequest($request));
    }


    public function delete(string $name): EmptyResponse
    {
        if (str_contains($name, 'cachedContents/')) {
            $name = str_replace('cachedContents/', '', $name);
        }

        $transporter = $this->creds->getTransporter();
        $baseUrl = $this->creds->getBaseUri()->new();

        $baseUrl->append($this->creds->getVersion())
            ->append('/cachedContents/' . $name);

        $payload = Payload::create()
            ->withBaseUri($baseUrl)
            ->withApiKey($this->creds->getKey())
            ->withMethod(Method::DELETE);


        $request = $payload->toRequest(RequestType::API);

        $resp = $transporter->send(fn (ClientInterface $client) => $client->sendRequest($request));

        if ($resp->getStatusCode() !== 200) {
            throw new \Exception('Failed to delete cached content');
        }

        return new EmptyResponse();
    }



}