<?php

namespace Nahid\GoogleGenerativeAI;

use GuzzleHttp\ClientInterface;
use Nahid\GoogleGenerativeAI\Enums\Http\Method;
use Nahid\GoogleGenerativeAI\Enums\Http\RequestType;
use Nahid\GoogleGenerativeAI\Http\Values\Payload;
use Nahid\GoogleGenerativeAI\Prompts\DTOs\CredentialsDTO;
use Psr\Http\Message\ResponseInterface;

class Api
{

    protected CredentialsDTO $creds;

    public function __construct(CredentialsDTO $credentials)
    {
        $this->creds = $credentials;
    }

    public function getModel(string $model): ResponseInterface
    {
        $transporter = $this->creds->getTransporter();
        $baseUrl = $this->creds->getBaseUri()->new();

        $baseUrl->append($this->creds->getVersion())
            ->append('/models/' . $model);

        $payload = Payload::create()
            ->withBaseUri($baseUrl)
            ->withMethod(Method::GET)
            ->withApiKey($this->creds->getKey());

        $request = $payload->toRequest(RequestType::API);

        return $transporter->send(fn (ClientInterface $client) => $client->sendRequest($request));

    }
    public function getModels(int $pageSize = 20, ?string $pageToken = null): ResponseInterface
    {
        $transporter = $this->creds->getTransporter();
        $baseUrl = $this->creds->getBaseUri()->new();

        $baseUrl->append($this->creds->getVersion())
            ->append('/models');

        $payload = Payload::create()
            ->withBaseUri($baseUrl)
            ->withMethod(Method::GET)
            ->withApiKey($this->creds->getKey());

        if ($pageSize) {
            $payload->queryParams()->add('pageSize', $pageSize);
        }

        if ($pageToken) {
            $payload->queryParams()->add('pageToken', $pageToken);
        }

        $request = $payload->toRequest(RequestType::API);

        return $transporter->send(fn (ClientInterface $client) => $client->sendRequest($request));

    }

    public function countTokens(string $text, ?string $model = null): ResponseInterface
    {
        $transporter = $this->creds->getTransporter();
        $baseUrl = $this->creds->getBaseUri()->new();
        $model = $model ?? $this->creds->getModel();

        $baseUrl->append($this->creds->getVersion())
            ->append('/models/' . $model . ':countTokens');

        $payload = Payload::create()
            ->withBaseUri($baseUrl)
            ->withApiKey($this->creds->getKey())
            ->withBody([
                'contents' => [
                    'parts' => [
                        [
                            'text' => $text
                        ]
                    ]
                ]
            ]);

        $request = $payload->toRequest(RequestType::API);

        return $transporter->send(fn (ClientInterface $client) => $client->sendRequest($request));

    }


    public function cachedContents(int $pageSize = 20, ?string $pageToken = null): ResponseInterface
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

}