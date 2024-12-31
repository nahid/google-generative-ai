<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nahid\GoogleGenerativeAI\Prompts\GoogleGenAI;

$client = GoogleGenAI::client('AIzaSyA8TNQGf4Gw1TvYcee7eJQ0qi-2ZpdSsOY')
    ->make();

$resp = $client->prompt()
    ->withImage('/Users/nahid/Desktop/moon_expectation.jpeg')
    ->generate("Describe the image");

dd($resp->getBody()->getContents());

$resp = $client->upload('/Users/nahid/Desktop/Screenshot 2024-12-08 at 1.29.41 PM.png');

dd($resp->getBody()->getContents());

$response = $client->text()->stream('What do you think about the future of AI in software development?');

$stream = $response->getBody();

// Check if the stream is readable
if ($stream->isReadable()) {
    while (!$stream->eof()) {
        echo $stream->read(1024); // Read 1024 bytes at a time
    }
}

