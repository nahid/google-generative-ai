<?php

namespace Nahid\GoogleGenerativeAI\Prompts\Http\Values;

use Http\Discovery\Psr17Factory;
use Http\Message\MultipartStream\MultipartStreamBuilder;
use Nahid\GoogleGenerativeAI\Prompts\Enums\Http\ContentType;
use Nahid\GoogleGenerativeAI\Prompts\Enums\Http\Method;
use Nahid\GoogleGenerativeAI\Prompts\Enums\Http\RequestType;
use Psr\Http\Message\RequestInterface;

class Payload
{

    private BaseUri $baseUri;

    private string $version = '';

    private string $model = '';


    public function __construct(
        ContentType $contentType = ContentType::JSON,
        private Method $method = Method::POST,
        private ?Headers $headers = null,
        private ?Parameters  $parameters = null,
        private ?QueryParams $queryParams = null,
        private mixed  $body = [],

    )
    {
        $this->headers = $headers ?? Headers::create();
        $this->parameters = $parameters ?? Parameters::create();
        $this->queryParams = $queryParams ?? QueryParams::create();

        $this->headers->add('Content-Type', $contentType->value);
    }

    public function toRequest(RequestType $type = RequestType::CONTENT): RequestInterface
    {
        $psr17Factory = new Psr17Factory();
        $body = '';

        if ($type === RequestType::CONTENT) {
            $this->baseUri->append($this->version)
                ->append($this->model)
                ->append(':generateContent');
        }

        if ($type === RequestType::STREAM) {
            $this->queryParams()->add('alt', 'sse');
            $this->baseUri->append($this->version)
                ->append($this->model)
                ->append(':streamGenerateContent');
        }

        /*if ($type === RequestType::UPLOAD) {
            $this->baseUri->append('upload')
                ->append($this->version)
                ->append('files');
        }*/

        if ($type === RequestType::RESUMABLE_UPLOAD) {
            $this->withContentType(ContentType::JSON->value);
            $this->baseUri->append('upload')
                ->append($this->version)
                ->append('files');
        }


        if ($this->method === Method::POST) {
            if ($type === RequestType::UPLOAD && $this->getContentType() === ContentType::MULTIPART->value) {
                $streamBuilder = new MultipartStreamBuilder($psr17Factory);

                $parameters = $this->parameters;

                foreach ($parameters as $key => $value) {
                    if (is_int($value) || is_float($value) || is_bool($value)) {
                        $value = (string) $value;
                    }

                    if (is_array($value)) {
                        foreach ($value as $nestedValue) {
                            $streamBuilder->addResource($key.'[]', $nestedValue);
                        }

                        continue;
                    }

                    $streamBuilder->addResource($key, $value);
                }

                $body = $streamBuilder->build();

                $this->withContentType(ContentType::MULTIPART->value, '; boundary='.$streamBuilder->getBoundary());

            } else if($type === RequestType::RESUMABLE_UPLOAD && $this->getContentType() === ContentType::JSON) {
                $content = '';
                if (is_array($this->body)) {
                    $content = json_encode($this->body, JSON_THROW_ON_ERROR);
                } else {
                    $content = json_encode($this->parameters->toArray(), JSON_THROW_ON_ERROR);
                }
                $body = $psr17Factory->createStream($content);
            } else if ($type === RequestType::UPLOAD && $this->getContentType() !== ContentType::MULTIPART->value) {
                $body = $this->body;
            } else {
               $this->withContentType(ContentType::JSON->value);
               $body = $psr17Factory->createStream(json_encode($this->parameters, JSON_THROW_ON_ERROR));
            }

        }

        $query = '';
        $queryArr = $this->queryParams->toArray();

        if (!empty($queryArr)) {
            $query = '?' . http_build_query($queryArr);
        }

        $request = $psr17Factory->createRequest($this->getMethod()->value, $this->baseUri->toString() . $query);

        if ($this->method !== Method::GET) {
            $request = $request->withBody($body);
        }


        foreach ($this->headers->toArray() as $key => $value) {
            $request = $request->withHeader($key, (string) $value);
        }


        return $request;
    }

    public function withBaseUri(BaseUri $uri): self
    {
        $this->baseUri = $uri;

        return $this;
    }

    public function withModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function withVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function withApiKey(string $key): self
    {
        $this->queryParams()->add('key', $key);

        return $this;
    }

    public function withContentType(string $contentType, string $suffix = ''): self
    {
        $this->headers = $this->headers->add('Content-Type', $contentType . $suffix);

        return $this;
    }

    public function getBaseUri(): BaseUri
    {
        return $this->baseUri;
    }

    public function getMethod(): Method
    {
        return $this->method;
    }

    public function getContentType(): string
    {
        return $this->headers->get('Content-Type');
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

    public function getBody(): mixed
    {
        return $this->body;
    }

    public function withBody(mixed $body): self
    {
        $this->body = $body;

        return $this;
    }

    public static function create(ContentType $contentType = ContentType::JSON, ?Headers $headers = null, ?Parameters $parameters = null, ?QueryParams $queryParams = null, mixed $body = []): static
    {
        $method = Method::POST;
        return new self($contentType, $method, $headers, $parameters, $queryParams, $body);
    }

    /**
     * @return Headers
     */
    public function setUri(BaseUri $uri): self
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