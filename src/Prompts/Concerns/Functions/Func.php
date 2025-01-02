<?php

namespace Nahid\GoogleGenerativeAI\Prompts\Concerns\Functions;

class Func
{
    private array $parameters = [];

    /**
     * @var array<int, Parameter>
     */
    private array $requiredParameters = [];
    public function __construct(
        private readonly string $name,
        private readonly string $description,
    )
    {
        
    }

    public static function create(string $name, string $description): self
    {
        return new self($name, $description);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'parameters' => $this->getParameters(),

        ];
    }

    public function addParameter(Parameter $parameter): self
    {
        $this->parameters[] = $parameter;

        return $this;
    }


    protected function getParameters(): array
    {
        $parameters = [
            'type' => 'object',
            'properties' => [],
        ];

        /**
         * @var Parameter $parameter
         */
        foreach ($this->parameters as $parameter) {
            if (!$parameter instanceof Parameter) {
                continue;
            }

            $parameters['properties'][$parameter->getName()] = $parameter->getProps();

            if ($parameter->isRequired()) {
                $this->requiredParameters[] = $parameter->getName();
            }
        }

        $parameters['required'] = $this->requiredParameters;


        return $parameters;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

}