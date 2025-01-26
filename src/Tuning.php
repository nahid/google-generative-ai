<?php

namespace Nahid\GoogleGenerativeAI;

use GuzzleHttp\ClientInterface;
use Nahid\GoogleGenerativeAI\Enums\Http\Method;
use Nahid\GoogleGenerativeAI\Enums\Http\RequestType;
use Nahid\GoogleGenerativeAI\Http\Responses\EmptyResponse;
use Nahid\GoogleGenerativeAI\Http\Values\Payload;
use Nahid\GoogleGenerativeAI\Prompts\DTOs\CredentialsDTO;
use Psr\Http\Message\ResponseInterface;

class Tuning
{
    
    private array $payload = [
        'baseModel' => 'models/gemini-1.5-flash',
        'tuningTask' => [],
    ];

    public function __construct(public CredentialsDTO $creds)
    {

    }

    public function withBaseModel(string $model): static
    {
        if (!str_contains($model, 'models/')) {
            $model = 'models/' . $model;
        }

        $this->payload['baseModel'] = $model;

        return $this;
    }

    public function withInfo(string $displayName, ?string $description = null): static
    {
        $this->payload['displayName'] = $displayName;

        if ($description) {
            $this->payload['description'] = $description;
        }

        return $this;
    }

    public function withHyperParameters(float $learningRate, int $epochCount, int $batchSize): static
    {
        $data = [
            'learningRate' => $learningRate,
            'epochCount' => $epochCount,
            'batchSize' => $batchSize,
        ];

        $this->payload['tuningTask']['hyperparameters'] = $data;

        return $this;
    }

    public function withTrainingData(array $data): static
    {
        $this->payload['tuningTask']['trainingData']['examples']['examples'] = $data;

        return $this;
    }

    public function tune()
    {
//        dd($this->payload);
        $transporter = $this->creds->getTransporter();
        $baseUrl = $this->creds->getBaseUri()->new();

        $baseUrl->append($this->creds->getVersion())
            ->append('/tunedModels');

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