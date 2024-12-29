<?php

namespace Nahid\GoogleGenerativeAI;

use Exception;
use Http\Discovery\Psr18Client;
use Http\Discovery\Psr18ClientDiscovery;
use GuzzleHttp\Client as GuzzleClient;
use Nahid\GoogleGenerativeAI\Enums\Http\ContentType;
use Nahid\GoogleGenerativeAI\Http\Values\BaseUri;
use Nahid\GoogleGenerativeAI\Http\Values\Headers;
use Nahid\GoogleGenerativeAI\Http\Values\Payload;
use Nahid\GoogleGenerativeAI\Http\Transporter;
use \Closure;
use Nahid\GoogleGenerativeAI\Http\Values\QueryParams;
use Nahid\GoogleGenerativeAI\Resources\Text;
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
    private array $headers = [];

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

    public function withHeaders(array $headers): static
    {
        $this->headers = $headers;
        
        return $this;
    }

    public function make(): static
    {

        if ($this->baseUri === null) {
            $this->baseUri = 'https://generativelanguage.googleapis.com/v1beta/';
        }


        if ($this->httpClient === null) {
            $this->httpClient = Psr18ClientDiscovery::find();
        }

        $baseUri = BaseUri::from($this->baseUri);
        $headers = Headers::create($this->headers);
        $queryParams = QueryParams::create(['key' => $this->apiKey]);
        $payload = new Payload($baseUri, headers: $headers, queryParams: $queryParams);


        $this->transporter = new Transporter($this->httpClient, $payload, $this->makeStreamHandler($this->httpClient));

        return $this;
    }

    public function text(): Text
    {
        return new Text($this->transporter);
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