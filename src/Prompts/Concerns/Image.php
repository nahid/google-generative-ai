<?php

namespace Nahid\GoogleGenerativeAI\Prompts\Concerns;

use Nahid\GoogleGenerativeAI\Contracts\UploadableContract;
use Nahid\GoogleGenerativeAI\Prompts\DTOs\FileInfoDTO;
use Nahid\GoogleGenerativeAI\Prompts\Uploadable;

class Image implements UploadableContract
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
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/heic',
            'image/heif',
        ];

        $mimeType = mime_content_type($path);

        if (! in_array($mimeType, $supportedMimeTypes)) {
            throw new \Exception('The file must be a valid image file.');
        }

        $name = basename($path);

        return FileInfoDTO::create($mimeType, $name);
    }
}