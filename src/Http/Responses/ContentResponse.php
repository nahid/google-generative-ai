<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses;

use Nahid\GoogleGenerativeAI\Contracts\Arrayable;

class ContentResponse implements Arrayable
{

    /**
     * @var array<int, PartResponse>
     */
    public readonly array $parts;
    public readonly string $role;

    public function __construct(
        array         $content
    )
    {
        $parts = [];
        foreach ($content['parts'] as $p) {
            $parts[] = PartResponse::create($p['text']);
        }

        $this->parts = $parts;
        $this->role = $content['role'];
    }

    public static function create(
        array $content
    ): static
    {
        return new static($content);
    }

    public function toArray(): array
    {
        return [
            'role' => $this->role,
            'parts' => array_map(fn(PartResponse $part) => $part->toArray(), $this->parts)
        ];
    }
}