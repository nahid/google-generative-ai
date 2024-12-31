<?php

namespace Nahid\GoogleGenerativeAI\Prompts\Enums\Http;

enum ContentType: string
{
    case JSON = 'application/json';
    case MULTIPART = 'multipart/form-data';
    case TEXT_PLAIN = 'text/plain';
}