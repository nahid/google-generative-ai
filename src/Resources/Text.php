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
        $payload = $this->transporter->getPayload()->setBody([
            'contents' => [
                'parts' => [
                    [
                        'text' => $prompt
                    ]
                ]
            ]
        ]);

        $payload->headers()->withContentType(ContentType::JSON);
        $response = $this->transporter->requestContent();

        dd($response->getStatusCode(), $response->getBody()->getContents());
    }

}