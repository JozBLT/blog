<?php

namespace Framework\Session;

class PHPSession implements SessionInterface, \ArrayAccess
{

    /** Ensures that the Session is started */
    private function ensureStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /** Retrieves information in Session */
    public function get(string $key, $default = null): mixed
    {
        $this->ensureStarted();
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }

        return $default;
    }

    /** Add information in Session */
    public function set(string $key, $value): void
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }

    /** Delete a session key */
    public function delete(string $key): void
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }

    /** Checks hether an offset exists*/
    public function offsetExists(mixed $offset): bool
    {
        $this->ensureStarted();
        return array_key_exists($offset, $_SESSION);
    }

    /** Offset to retrieve */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /** Offset to set */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /** Offset to unset */
    public function offsetUnset(mixed $offset): void
    {
        $this->delete($offset);
    }
}
