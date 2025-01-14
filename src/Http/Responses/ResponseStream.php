<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses;

use Generator;
use ErrorException;
use Nahid\GoogleGenerativeAI\Contracts\ResponseStreamContract;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Traversable;

/**
 * @implements Traversable<int, ResponseChunk>
 */
class ResponseStream implements ResponseStreamContract
{
    public function __construct(protected readonly ResponseInterface $response)
    {

    }

    public static function create(ResponseInterface $response): static
    {
        return new static($response);
    }


    public function getIterator(): Generator
    {
        while (! $this->response->getBody()->eof()) {
            $line = $this->readLine($this->response->getBody());


            if (! str_starts_with($line, 'data:')) {
                continue;
            }

            $data = trim(substr($line, strlen('data:')));

            if ($data === '[DONE]') {
                break;
            }

            /** @var array{error?: array{message: string|array<int, string>, type: string, code: string}} $response */
            $response = json_decode($data, true, flags: JSON_THROW_ON_ERROR);

            if (isset($response['error'])) {
                throw new ErrorException($response['error'], $this->response->getStatusCode());
            }

            yield ResponseChunk::create(
                $response['candidates'],
                UsageMetadataResponse::create(
                    $response['usageMetadata']['promptTokenCount'],
                    $response['usageMetadata']['totalTokenCount'],
                    $response['usageMetadata']['candidateTokenCount'] ?? null
                ),
                $response['modelVersion'],
            );
        }
    }

    /**
     * Read a line from the stream.
     */
    private function readLine(StreamInterface $stream): string
    {
        $buffer = '';

        while (! $stream->eof()) {
            if ('' === ($byte = $stream->read(1))) {
                return $buffer;
            }
            $buffer .= $byte;
            if ($byte === "\n") {
                break;
            }
        }

        return $buffer;
    }
}