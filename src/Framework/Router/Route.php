<?php

namespace Framework\Router;

/**
 * Class Route
 * Represents a matched path
 */
class Route
{

    private string $name;

    private mixed $callback;

    private array $parameters;

    public function __construct(string $name, callable|string|array $callback, array $parameters)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->parameters = $parameters;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCallback(): callable|string|array
    {
        return $this->callback;
    }

    /**
     * Retrieve URL's parameters
     *
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
}
