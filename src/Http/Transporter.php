<?php

namespace Nahid\GoogleGenerativeAI\Http;

use Closure;
use Http\Discovery\Psr17Factory;
use Nahid\GoogleGenerativeAI\Enums\Http\Method;
use Nahid\GoogleGenerativeAI\Http\Values\BaseUri;
use Nahid\GoogleGenerativeAI\Http\Values\Payload;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Transporter
{

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly Payload $payload,
        private readonly Closure $streamHandler,
    )
    {
        // ..
    }


    public function requestContent(): ResponseInterface
    {
        $request = $this->payload->toRequest('models/gemini-1.5-flash:generateContent');

        return $this->request(function () use ($request) {
            return $this->httpClient->sendRequest($request);
        });
    }

    public function request(Closure $callable): ResponseInterface
    {
        try {
            return $callable($this->payload);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function makeRequest(Payload $payload): RequestInterface
    {
        $psr17Factory = new Psr17Factory();

        $request = $psr17Factory->createRequest($payload->method()->value, $payload->getBaseUri());

        foreach ($payload->headers()->toArray() as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        if ($payload->method() === Method::POST) {
            $request = $request->withBody($psr17Factory->createStream(json_encode($payload->getBody())));
        }

        $queryParams = '';

        if (!empty($payload->queryParams()->toArray())) {
            $queryParams = http_build_query($payload->queryParams()->toArray());
        }


        $request = $request->withUri($request->getUri()->withQuery($queryParams));




        return $request;

    }

    public function getPayload(): Payload
    {
        return $this->payload;
    }

}