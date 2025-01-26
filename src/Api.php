<?php

namespace Nahid\GoogleGenerativeAI;

use GuzzleHttp\ClientInterface;
use Nahid\GoogleGenerativeAI\Enums\Http\Method;
use Nahid\GoogleGenerativeAI\Enums\Http\RequestType;
use Nahid\GoogleGenerativeAI\Http\Responses\Api\CacheContentTokenCount;
use Nahid\GoogleGenerativeAI\Http\Responses\Api\Model;
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

    /**
     * @param string $model
     * @return ?Model
     * @throws \JsonException
     */
    public function getModel(string $model): ?Model
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

        $resp = $transporter->send(fn (ClientInterface $client) => $client->sendRequest($request));

        if ($resp->getStatusCode() !== 200) {
            return null;
        }

        $data = json_decode($resp->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return Model::create($data);
    }

    /**
     * @param int $pageSize
     * @param string|null $pageToken
     * @return ?array<int, Model>
     * @throws \JsonException
     */
    public function getModels(int $pageSize = 20, ?string $pageToken = null): ?array
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

        $resp = $transporter->send(fn (ClientInterface $client) => $client->sendRequest($request));

        if ($resp->getStatusCode() !== 200) {
            return null;
        }

        $data = json_decode($resp->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return array_map(fn ($model) => Model::create($model), $data['models']);

    }

    public function countTokens(string $text, ?string $model = null): ?CacheContentTokenCount
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

        $resp = $transporter->send(fn (ClientInterface $client) => $client->sendRequest($request));

        if ($resp->getStatusCode() !== 200) {
            return null;
        }

        $data = json_decode($resp->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return CacheContentTokenCount::create($data);
    }


    public function cacheContents(): CacheContents
    {
        return new CacheContents($this->creds);
    }

}