<?php

namespace Nahid\GoogleGenerativeAI\Prompts\Concerns;

use Nahid\GoogleGenerativeAI\Contracts\UploadableContract;
use Nahid\GoogleGenerativeAI\Prompts\DTOs\FileInfoDTO;
use Nahid\GoogleGenerativeAI\Prompts\Uploadable;

class Video implements UploadableContract
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
            'video/mp4',
            'video/mpeg',
            'video/mov',
            'video/avi',
            'video/x-flv',
            'video/mpg',
            'video/webm',
            'video/wmv',
            'video/3gpp',
        ];

        $mimeType = mime_content_type($path);

        if (! in_array($mimeType, $supportedMimeTypes)) {
            throw new \Exception('The file must be a valid video file.');
        }

        $name = basename($path);

        return FileInfoDTO::create($mimeType, $name);
    }
}