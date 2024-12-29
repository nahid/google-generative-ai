<?php

namespace Nahid\GoogleGenerativeAI\Http\Values;

use Http\Discovery\Psr17Factory;
use Nahid\GoogleGenerativeAI\Enums\Http\Method;
use Psr\Http\Message\RequestInterface;

class Payload
{




    public function __construct(
        private BaseUri $baseUri,
        private Method $method = Method::POST,
        private ?Headers $headers = null,
        private ?Parameters  $parameters = null,
        private ?QueryParams $queryParams = null,
        private array  $body = [],

    )
    {
        $this->headers = $headers ?? Headers::create();
        $this->parameters = $parameters ?? Parameters::create();
        $this->queryParams = $queryParams ?? QueryParams::create();
    }

    public function toRequest(string $requestUri): RequestInterface
    {
        $psr17Factory = new Psr17Factory();
        $url = $this->baseUri->append($requestUri);


        $request = $psr17Factory->createRequest($this->method()->value, $url);

        foreach ($this->headers()->toArray() as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        if ($this->method() === Method::POST) {
            $request = $request->withBody($psr17Factory->createStream(json_encode($this->getBody())));
        }

        $queryParams = '';

        if (!empty($this->queryParams()->toArray())) {
            $queryParams = http_build_query($this->queryParams()->toArray());
        }


        $request = $request->withUri($request->getUri()->withQuery($queryParams));




        return $request;
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    public function method(): Method
    {
        return $this->method;
    }

    public function headers(): Headers
    {
        return $this->headers;
    }

    public function parameters(): Parameters
    {
        return $this->parameters;
    }

    public function queryParams(): QueryParams
    {
        return $this->queryParams;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function setBody(array $body): self
    {
        $this->body = $body;

        return $this;
    }

    public static function create(BaseUri $baseUri, array $headers = [], array $parameters = [], array $body = []): static
    {
        $method = Method::POST;
        return new self($baseUri, $method, $headers, $parameters, $body);
    }

    /**
     * @return Headers
     */
    public function setUri(string $uri): self
    {
        $this->baseUri = $uri;

        return $this;
    }

    public function replace(Payload $payload)
    {
        $this->headers = $payload->headers();
        $this->parameters = $payload->parameters();
        $this->queryParams = $payload->queryParams();
        $this->body = $payload->getBody();
    }
}