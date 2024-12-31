<?php

namespace Nahid\GoogleGenerativeAI\Prompts;

use Exception;
use GuzzleHttp\ClientInterface;
use Http\Discovery\Psr17Factory;
use Nahid\GoogleGenerativeAI\Enums\Http\RequestType;
use Nahid\GoogleGenerativeAI\Http\Values\BaseUri;
use Nahid\GoogleGenerativeAI\Http\Values\Payload;
use Nahid\GoogleGenerativeAI\Prompts\DTOs\CredentialsDTO;
use Nahid\GoogleGenerativeAI\Prompts\DTOs\FileDataDTO;

trait Uploadable
{
    public function __construct(
        private readonly CredentialsDTO $creds,
    )
    {
    }

    public function upload(string $path): FileDataDTO
    {
        $fileInfo = $this->validate($path);

        $url = $this->resumableUpload($fileInfo->getMimeType(), $fileInfo->getBaseName());

        // $url = 'https://generativelanguage.googleapis.com/upload/v1beta/files/?key=AIzaSyA8TNQGf4Gw1TvYcee7eJQ0qi-2ZpdSsOY&upload_id=AFiumC5VrQI8HfjObGK23B8b0Njle_2LxBNGdT2UqxPUwRHuFjcwMRsRqK5f8xaJrc5uCiBw_Sw34VSpeeN5zRNdu3DTV1-MbUY_oZ9uJ-blfzU&upload_protocol=resumable';

        $contents = file_get_contents($path);
        $psr17Factory = new Psr17Factory();

        $transporter = $this->creds->getTransporter();
        $baseUrl = BaseUri::from($url);
        $payload = Payload::create()
            ->withBaseUri($baseUrl)
            ->withContentType($fileInfo->getMimeType())
            ->withBody($psr17Factory->createStreamFromFile($path));

        $payload->headers()
            ->add('X-Goog-Upload-Offset', 0)
            ->add('X-Goog-Upload-Command', 'upload, finalize')
            ->add('Content-Length', strlen($contents));

        $response = $transporter->requestObject($payload);

        $data = json_decode($response->getBody()->getContents(), true);

        return FileDataDTO::create($fileInfo->getMimeType(), $data['file']['uri']);

    }

    public function resumableUpload(string $mime, string $baseName): string
    {


        $transporter = $this->creds->getTransporter();
        $payload = Payload::create()
            ->withBaseUri($this->creds->getBaseUri()->new())
            ->withModel($this->creds->getModel())
            ->withVersion($this->creds->getVersion())
            ->withApiKey($this->creds->getKey());


        $payload->headers()->add('Content-Type', $mime)
            ->add('X-Goog-Upload-Protocol', 'resumable')
            ->add('X-Goog-Upload-Command', 'start')
            ->add('X-Goog-Upload-Header-Content-Type', $mime)
            ->add('Content-Type', 'application/json');
        $payload->withBody([
            'file' => [
                'display_name' => $baseName,
            ]
        ]);

        $request = $payload->toRequest(RequestType::RESUMABLE_UPLOAD);
        $resp = $transporter->send(fn (ClientInterface $client) => $client->sendRequest($request));

        return $resp->getHeader('X-Goog-Upload-Control-URL')[0];
    }

    public function validate(string $path): FileDataDTO
    {
        throw new Exception('Method did not implemented yet');
    }

}