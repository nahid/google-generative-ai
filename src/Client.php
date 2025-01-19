<?php

namespace Nahid\GoogleGenerativeAI;

use Closure;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Http\Discovery\Psr18Client;
use Http\Discovery\Psr18ClientDiscovery;
use Nahid\GoogleGenerativeAI\Enums\Http\RequestType;
use Nahid\GoogleGenerativeAI\Http\Transporter;
use Nahid\GoogleGenerativeAI\Http\Values\BaseUri;
use Nahid\GoogleGenerativeAI\Prompts\DTOs\CredentialsDTO;
use Nahid\GoogleGenerativeAI\Prompts\Prompt;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Client
{
    private ?ClientInterface $httpClient = null;

    private ?Transporter $transporter = null;
    private ?string $apiKey = null;

    private ?string $baseUri =  null;
    private ?Closure $streamHandler = null;
    private string $model = 'gemini-1.5-flash';
    private string $version = 'v1beta';

    public function __construct(string $apiKey = null)
    {
        $this->apiKey = $apiKey;

    }

    public function withApiKey(string $apiKey): static
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function withBaseUri(string $baseUri): static
    {
        $this->baseUri = $baseUri;

        return $this;
    }

    public function withVersion(string $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function withHttpClient(ClientInterface $httpClient): static
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    public function withStreamHandler(Closure $streamHandler): static
    {
        $this->streamHandler = $streamHandler;

        return $this;
    }


    public function withModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function transporter(): ?Transporter
    {
        return $this->transporter;
    }

    public function make(): static
    {

        if ($this->baseUri === null) {
            $this->baseUri = 'https://generativelanguage.googleapis.com/';
        }

        if ($this->httpClient === null) {
            $this->httpClient = Psr18ClientDiscovery::find();
        }

        $this->transporter = new Transporter($this->httpClient, $this->makeStreamHandler($this->httpClient));

        return $this;
    }


    public function prompt(array $config = []): Prompt
    {
        return new Prompt(
            CredentialsDTO::create($this->transporter, BaseUri::from($this->baseUri), $this->apiKey, $this->model, $this->version),
            $config);
    }

    public function api(): Api
    {
        return new Api(
            CredentialsDTO::create($this->transporter, BaseUri::from($this->baseUri), $this->apiKey, $this->model, $this->version)
        );
    }



    private function  makeStreamHandler(ClientInterface $client): Closure
    {
        if (! is_null($this->streamHandler)) {
            return $this->streamHandler;
        }

        if ($client instanceof GuzzleClient) {
            return fn (RequestInterface $request): ResponseInterface => $client->send($request, ['stream' => true]);
        }

        if ($client instanceof Psr18Client) { // @phpstan-ignore-line
            return fn (RequestInterface $request): ResponseInterface => $client->sendRequest($request); // @phpstan-ignore-line
        }

        return function (RequestInterface $_): never {
            throw new Exception('To use stream requests you must provide an stream handler closure via the OpenAI factory.');
        };
    }
}