<?php

namespace Nahid\GoogleGenerativeAI\Http\Responses;

/**
 * Trait IterableArray
 * @package Nahid\GoogleGenerativeAI\Http\Responses
 *
 * @template D
 */
trait IterableArray
{
    /**
     * @var array<int, D>
     */
    protected array $data = [];


    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     * @return D
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }
}