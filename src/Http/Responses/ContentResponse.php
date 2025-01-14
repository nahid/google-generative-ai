<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses;

use Nahid\GoogleGenerativeAI\Contracts\Arrayable;

class ContentResponse implements Arrayable
{

    /**
     * @var array<int, PartResponse>
     */
    protected array $parts = [];
    public readonly string $role;

    public function __construct(
        array         $content
    )
    {
        foreach ($content['parts'] as $p) {
            $this->parts[] = PartResponse::create($p['text']);
        }

        $this->role = $content['role'];
    }

    public static function create(
        array $content
    ): static
    {
        return new static($content);
    }

    /**
     * @return array<int, PartResponse>
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    public function toArray(): array
    {
        return [
            'role' => $this->role,
            'parts' => array_map(fn(PartResponse $part) => $part->toArray(), $this->parts)
        ];
    }
}