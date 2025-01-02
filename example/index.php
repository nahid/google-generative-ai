<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nahid\GoogleGenerativeAI\GoogleGenAI;
use Nahid\GoogleGenerativeAI\Prompts\Concerns\Functions\Func;
use Nahid\GoogleGenerativeAI\Prompts\Concerns\Functions\Parameter;



$client = GoogleGenAI::client(getenv('GEMINI_API_KEY'))
    ->make();

try {
    $resp = $client->prompt()
        ->generate("Best shopify product reviews app");
} catch (Exception $e) {
    dd($e->getMessage());
}


//->withAudio('/Users/nahid/Downloads/record.ogg')

dd($resp->getBody()->getContents());

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
