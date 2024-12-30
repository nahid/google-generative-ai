<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Nahid\GoogleGenerativeAI\GoogleGenAI;

$client = GoogleGenAI::client('api key')->make();

dd($client->text()->generate('Hello, how are you?'));
