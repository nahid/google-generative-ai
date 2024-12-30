<?php

namespace Nahid\GoogleGenerativeAI;

use Exception;
use Http\Discovery\Psr18Client;
use Http\Discovery\Psr18ClientDiscovery;
use GuzzleHttp\Client as GuzzleClient;
use Nahid\GoogleGenerativeAI\Enums\Http\ContentType;
use Nahid\GoogleGenerativeAI\Enums\Http\RequestType;
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
    private string $model = 'models/gemini-1.5-flash';
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

    public function withHeaders(array $headers): static
    {
        $this->headers = $headers;
        
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

    public function upload(string $file): ResponseInterface
    {
        $url = $this->resumableUpload($file);

        if (! file_exists($file)) {
            throw new Exception('The file does not exist.');
        }

        $mime = mime_content_type($file);

        $contents = file_get_contents($file);

        $payload = $this->transporter->getPayload();
        $baseUrl = BaseUri::from($url);
        $payload->withBaseUri($baseUrl);
        $payload->headers()
            ->add('X-Goog-Upload-Offset', 0)
            ->add('X-Goog-Upload-Command', 'upload, finalize')
            ->add('Content-Length', strlen($contents));
        $payload->withBody($contents);

        dd($this->transporter);


        return $this->transporter->requestObject();
    }

    private function resumableUpload(string $file): string
    {
        if (! file_exists($file)) {
            throw new Exception('The file does not exist.');
        }

        $mime = mime_content_type($file);


        $payload = $this->transporter->getPayload();
        $payload->headers()->add('Content-Type', $mime)
            ->add('X-Goog-Upload-Protocol', 'resumable')
            ->add('X-Goog-Upload-Command', 'start')
            ->add('X-Goog-Upload-Header-Content-Type', $mime)
            ->add('Content-Type', 'application/json');
        $payload->withBody([
            'file' => [
                'display_name' => basename($file),
            ]
        ]);

        $request = $payload->toRequest(RequestType::RESUMABLE_UPLOAD);
        $resp = $this->transporter->send(fn () => $this->httpClient->sendRequest($request));

        return $resp->getHeader('X-Goog-Upload-Control-URL')[0];
    }

    public function prompt(): Prompt
    {
        return new Prompt($this->transporter, BaseUri::from($this->baseUri), $this->apiKey, $this->model, $this->version);
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