<?php
namespace PHPJava\Core\JVM\Cache;

class Cache
{
    private $items = [];

    public function fetchOrPush(string $key, callable $pushFunction, ...$parameters)
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }
        return $this->items[$key] = $pushFunction(...$parameters);
    }
}