<?php

namespace Nahid\GoogleGenerativeAI\Prompts\Concerns;

use Nahid\GoogleGenerativeAI\Contracts\UploadableContract;
use Nahid\GoogleGenerativeAI\Prompts\DTOs\FileInfoDTO;
use Nahid\GoogleGenerativeAI\Prompts\Uploadable;

class Audio implements UploadableContract
{
    use Uploadable;

    /**
     * Validate the image file.
     *
     * @override
     */
    public function validate(string $path): FileInfoDTO
    {
        $supportedMimeTypes = [
            'audio/wav',
            'audio/mp3',
            'audio/aiff',
            'audio/flac',
            'audio/ogg',
            'audio/aac',
        ];

        $mimeType = mime_content_type($path);

        if (! in_array($mimeType, $supportedMimeTypes)) {
            throw new \Exception('The file must be a valid audio file.');
        }

        $name = basename($path);

        return FileInfoDTO::create($mimeType, $name);
    }
}