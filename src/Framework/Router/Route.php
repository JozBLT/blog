<?php

namespace Framework\Router;

/**
 * Class Route
 * Represents a matched path
 */
class Route
{

    /**
     * @var string
     */
    private string $name;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @var array
     */
    private array $parameters;

    public function __construct(string $name, callable $callback, array $parameters)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     *
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * Retrieve URL's parameters
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
}
