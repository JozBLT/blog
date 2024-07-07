<?php

namespace Framework\Session;

class ArraySession implements SessionInterface
{

    private $session = [];

    public function get(string $key, $default = null): mixed
    {
        if (array_key_exists($key, $this->session)) {
            return $this->session[$key];
        }

        return $default;
    }

    public function set(string $key, $value): void
    {
        $this->session[$key] = $value;
    }

    public function delete(string $key): void
    {
        unset($this->session[$key]);
    }
}
