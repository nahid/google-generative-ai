<?php

namespace Nahid\GoogleGenerativeAI\Prompts\Concerns\Functions;

class Parameter
{
    public function __construct(
        private readonly string $name,
        private readonly string $type,
        private readonly string $description,
        private readonly bool $required = false,
    )
    {

    }

    public static function create(string $name, string $type, string $description, bool $required = false): self
    {
        return new self($name, $type, $description, $required);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
            'required' => $this->required,
        ];
    }

    public function getProps(): array
    {
        return [
            'type' => $this->type,
            'description' => $this->description,
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}