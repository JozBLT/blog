<?php

namespace Framework\Session;

interface SessionInterface
{

    public function get(string $key, mixed $default = null): mixed;

    public function set(string $key, $value): void;

    public function delete(string $key): void;
}
