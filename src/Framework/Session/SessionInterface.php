<?php

namespace Framework\Session;

interface SessionInterface
{

    /** Retrieves information in Session */
    public function get(string $key, mixed $default = null): mixed;

    /** Add information in Session */
    public function set(string $key, $value): void;

    /** Delete a session key */
    public function delete(string $key): void;
}
