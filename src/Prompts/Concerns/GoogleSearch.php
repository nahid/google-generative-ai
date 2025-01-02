<?php

namespace Nahid\GoogleGenerativeAI\Prompts\Concerns;

use Nahid\GoogleGenerativeAI\Contracts\Arrayable;

class GoogleSearch implements Arrayable
{
    private array $tools = [];
    public function __construct(string $mode, float|int $threshold)
    {
        $this->tools = [
            'mode' => $mode,
            'dynamic_threshold' => $threshold,
        ];
    }

    public function toArray(): array
    {
        return [
            'google_search_retrieval' => [
                'dynamic_retrieval_config' => $this->tools,
            ],
        ];
    }
}