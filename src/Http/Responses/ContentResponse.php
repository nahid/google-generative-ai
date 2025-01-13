<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses;

class ContentResponse
{
    public function __construct(
        public PartResponse $part,
        public string $role,
    )
    {

    }

}