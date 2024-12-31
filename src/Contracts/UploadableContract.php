<?php

namespace Nahid\GoogleGenerativeAI\Contracts;

use Nahid\GoogleGenerativeAI\Prompts\DTOs\FileDataDTO;
use Nahid\GoogleGenerativeAI\Prompts\DTOs\FileInfoDTO;

interface UploadableContract
{
    public function resumableUpload(string $mime, string $baseName): string;
    public function upload(string $path): FileDataDTO;
    public function validate(string $path): FileInfoDTO;

}