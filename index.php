<?php

require_once __DIR__ . '/vendor/autoload.php';

use Nahid\GoogleGenerativeAI\GoogleGenAI;

$client = GoogleGenAI::client('AIzaSyA8TNQGf4Gw1TvYcee7eJQ0qi-2ZpdSsOY')->make();

dd($client->text()->generate('Hello, how are you?'));
