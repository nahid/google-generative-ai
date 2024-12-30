<?php

namespace Nahid\GoogleGenerativeAI\Resources;

use Nahid\GoogleGenerativeAI\Enums\Http\ContentType;
use Nahid\GoogleGenerativeAI\Http\Values\Payload;
use Nahid\GoogleGenerativeAI\Http\Transporter;

class Text
{
    public function __construct(private readonly Transporter $transporter)
    {

    }

    /**
     * @throws \Exception
     */
    public function generate(string $prompt)
    {
        $payload = $this->transporter->getPayload()->withBody([
            'contents' => [
                'parts' => [
                    [
                        'text' => $prompt
                    ]
                ]
            ]
        ]);

        $payload->headers()->withContentType(ContentType::JSON);

        return $this->transporter->requestContent();
    }


    /**
     * @throws \Exception
     */
    public function stream(string $prompt)
    {
        $payload = $this->transporter->getPayload()->withBody([
            'contents' => [
                'parts' => [
                    [
                        'text' => $prompt
                    ]
                ]
            ]
        ]);

        $payload->headers()->withContentType(ContentType::JSON);

        return $this->transporter->requestStream();
    }

}