<?php

namespace Nahid\GoogleGenerativeAI;

use Exception;
use GuzzleHttp\ClientInterface;
use Http\Discovery\Psr17Factory;
use Nahid\GoogleGenerativeAI\Enums\Http\ContentType;
use Nahid\GoogleGenerativeAI\Enums\Http\RequestType;
use Nahid\GoogleGenerativeAI\Http\Transporter;
use Nahid\GoogleGenerativeAI\Http\Values\BaseUri;
use Nahid\GoogleGenerativeAI\Http\Values\Payload;

class Prompt
{
    private array $files = [];
    public function __construct(
        private readonly Transporter $transporter,
        private readonly BaseUri $baseUri,
        private readonly string $key,
        private readonly string $model,
        private readonly string $version
    )
    {

    }

    public function withImage(string $path): self
    {
        $url = $this->resumableUpload($path);

        // $url = 'https://generativelanguage.googleapis.com/upload/v1beta/files/?key=AIzaSyA8TNQGf4Gw1TvYcee7eJQ0qi-2ZpdSsOY&upload_id=AFiumC5VrQI8HfjObGK23B8b0Njle_2LxBNGdT2UqxPUwRHuFjcwMRsRqK5f8xaJrc5uCiBw_Sw34VSpeeN5zRNdu3DTV1-MbUY_oZ9uJ-blfzU&upload_protocol=resumable';

        $contents = file_get_contents($path);
        $mime = mime_content_type($path);
        $psr17Factory = new Psr17Factory();

        $transporter = $this->transporter;
        $baseUrl = BaseUri::from($url);
        $payload = Payload::create()
            ->withBaseUri($baseUrl)
            ->withContentType($mime)
            ->withBody($psr17Factory->createStreamFromFile($path));

        $payload->headers()
            ->add('X-Goog-Upload-Offset', 0)
            ->add('X-Goog-Upload-Command', 'upload, finalize')
            ->add('Content-Length', strlen($contents));

        $response = $transporter->requestObject($payload);

        $data = json_decode($response->getBody()->getContents(), true);


        $this->files[] = [
            'file_data' => [
                'mime_type' => $mime,
                'file_uri' =>  $data['file']['uri'],
            ]
        ];

        return $this;

    }

    public function generate(string $prompt)
    {
        $transporter = $this->transporter;

        $payload = Payload::create()
            ->withBaseUri($this->baseUri->new())
            ->withModel($this->model)
            ->withVersion($this->version)
            ->withApiKey($this->key)
            ->withBody([
                'contents' => [
                    'parts' => [
                        [
                            'text' => $prompt
                        ],
                        ... $this->files
                    ]
                ]
            ]);


        return $transporter->requestContent($payload);
    }

    public function stream(string $prompt)
    {
        $transporter = $this->transporter;

        $payload = Payload::create()
            ->withBaseUri($this->baseUri->new())
            ->withModel($this->model)
            ->withVersion($this->version)
            ->withApiKey($this->key)
            ->withBody([
                'contents' => [
                    'parts' => [
                        [
                            'text' => $prompt
                        ]
                    ]
                ]
            ]);

        return $transporter->requestStream($payload);
    }

    private function resumableUpload(string $file): string
    {
        if (! file_exists($file)) {
            throw new Exception('The file does not exist.');
        }

        $mime = mime_content_type($file);

        $transporter = $this->transporter;
        $payload = Payload::create()
            ->withBaseUri($this->baseUri->new())
            ->withModel($this->model)
            ->withVersion($this->version)
            ->withApiKey($this->key);


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
        $resp = $transporter->send(fn (ClientInterface $client) => $client->sendRequest($request));

        return $resp->getHeader('X-Goog-Upload-Control-URL')[0];
    }
}