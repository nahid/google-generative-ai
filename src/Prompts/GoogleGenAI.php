<?php

declare(strict_types=1);

namespace Nahid\GoogleGenerativeAI\Prompts;

class GoogleGenAI
{
    public static function client(string $apiKey): Client
    {
        $client = new Client($apiKey);


        return $client;
        
    }

}