<?php

namespace Nahid\GoogleGenerativeAI\Http;

use Closure;
use Http\Discovery\Psr17Factory;
use Nahid\GoogleGenerativeAI\Enums\Http\Method;
use Nahid\GoogleGenerativeAI\Enums\Http\RequestType;
use Nahid\GoogleGenerativeAI\Http\Values\BaseUri;
use Nahid\GoogleGenerativeAI\Http\Values\Payload;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Transporter
{

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly Closure $streamHandler,
    )
    {
        // ..
    }


    public function requestContent(Payload $payload): ResponseInterface
    {
        $request = $payload->toRequest();

        return $this->send(function () use ($request) {
            return $this->httpClient->sendRequest($request);
        });
    }


    public function requestFile(): ResponseInterface
    {
        $request = $this->payload->toRequest(RequestType::FILE);

        return $this->send(function () use ($request) {
            return $this->httpClient->sendRequest($request);
        });
    }

    public function requestObject(Payload $payload): ResponseInterface
    {
        $request = $payload->toRequest(RequestType::UPLOAD);

        return $this->send(function (ClientInterface $client) use ($request) {
            return $client->sendRequest($request);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function requestStream(Payload $payload): ResponseInterface
    {
        $request = $payload->toRequest(RequestType::STREAM);

        return $this->send(fn (ClientInterface $client) => ($this->streamHandler)($request));
    }

    public function send(Closure $callable): ResponseInterface
    {
        try {
            return $callable($this->httpClient);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getPayload(): Payload
    {
        return $this->payload;
    }

}