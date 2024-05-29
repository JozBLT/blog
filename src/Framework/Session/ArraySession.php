<?php

namespace Framework\Session;

class ArraySession implements SessionInterface
{

    private $session = [];

    /**
     * @param string $key
     * @param $default
     * @return mixed
     */
    public function get(string $key, $default = null): mixed
    {
        if (array_key_exists($key, $this->session)) {
            return $this->session[$key];
        }
        return $default;
    }

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->session[$key] = $value;
    }

    /**
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        unset($this->session[$key]);
    }
}