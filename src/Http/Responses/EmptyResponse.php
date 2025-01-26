<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses;

class EmptyResponse
{
    public function __toString(): string
    {
        return '';
    }

}