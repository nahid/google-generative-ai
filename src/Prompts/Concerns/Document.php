<?php

namespace Nahid\GoogleGenerativeAI\Prompts\Concerns;

use Nahid\GoogleGenerativeAI\Contracts\UploadableContract;
use Nahid\GoogleGenerativeAI\Prompts\DTOs\FileInfoDTO;
use Nahid\GoogleGenerativeAI\Prompts\Uploadable;

class Document implements UploadableContract
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
            'application/pdf',
            'application/x-javascript',
            'text/javascript',
            'application/x-python',
            'text/x-python',
            'text/plain',
            'text/html',
            'text/css',
            'text/md',
            'text/csv',
            'text/xml',
            'text/rtf',
        ];

        $mimeType = mime_content_type($path);

        if (! in_array($mimeType, $supportedMimeTypes)) {
            throw new \Exception('The file must be a valid document file.');
        }

        //$this->checkFileSize($path, 20);

        $name = basename($path);

        return FileInfoDTO::create($mimeType, $name);
    }
}