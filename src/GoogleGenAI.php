<?php

declare(strict_types=1);

namespace Nahid\GoogleGenerativeAI;

use Nahid\GoogleGenerativeAI\Client;

class GoogleGenAI
{
    public static function client(string $apiKey): Client
    {
        $client = new Client($apiKey);


        return $client;
        
    }

}