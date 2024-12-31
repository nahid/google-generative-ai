<?php

namespace Nahid\GoogleGenerativeAI\Prompts\Enums\Http;

enum RequestType
{
    case CONTENT;
    case STREAM;
    case FILE;

    case UPLOAD;

    case RESUMABLE_UPLOAD;
}
