<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nahid\GoogleGenerativeAI\Enums\Response\SchemaDataType;
use Nahid\GoogleGenerativeAI\GoogleGenAI;
use Nahid\GoogleGenerativeAI\Prompts\Concerns\Functions\Func;
use Nahid\GoogleGenerativeAI\Prompts\Concerns\Functions\Parameter;



$client = GoogleGenAI::client(getenv('GEMINI_API_KEY'))
    ->make();

$resp = $client->api()->cachedContents();

dd($resp->getBody()->getContents());

try {
    $resp = $client->prompt([
        'max_output_tokens' => 10000,
        'response_mime_type' => 'application/json',
        'response_schema' => [
            'type' => SchemaDataType::ARRAY,
            'items' => [
                'type' => SchemaDataType::OBJECT,
                'properties' => [
                    'movie_name' => [
                        'type' => SchemaDataType::STRING
                    ],
                    'year' => [
                        'type' => SchemaDataType::INTEGER
                    ]
                ]
            ]
        ]
    ])
        ->generate("List of top 10 action movies with year");
} catch (Exception $e) {
    dd($e->getMessage());
}

/*foreach ($resp as $chunk) {
    foreach ($chunk->candidates as $candidate) {
        echo $candidate->content->parts[0]->text . PHP_EOL;
    }
}

die();*/

//->withAudio('/Users/nahid/Downloads/record.ogg')

dd($resp->candidates[0]->content->parts[0]->text);

foreach ($resp as $chunk) {
    foreach ($chunk->getCandidates() as $candidate) {
        echo $candidate->content->getParts()[0]->text . PHP_EOL;
    }
}

/*->withFunctions(
    Func::create('topSellingProducts', 'List of top selling products')
        ->addParameter(Parameter::create('limit', 'number', 'Limit of products to return', true))
        ->addParameter(Parameter::create('category', 'string', 'Category of products to return', false)),
    Func::create('getProduct', 'Get a product by title')
        ->addParameter(Parameter::create('title', 'string', 'Product title', true)),
    Func::create('offerProducts', 'List of offered products')
        ->addParameter(Parameter::create('percents', 'number', 'Discount percents', true))
        ->addParameter(Parameter::create('limit', 'number', 'Limit of products to return'))
)*/
