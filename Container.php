<?php

class Container
{
    public static $instance;

    private $single;

    public function __construct()
    {
        static::setInstance($this);
    }

    public function bind($key, $concrete)
    {
        if ($concrete instanceof Closure) {
            $this->single[$key] = $concrete();
        } else {
            $this->single[$key] = $concrete;
        }

        return true;
    }

    public function make($key)
    {
        if (!isset($this->single[$key]))
            return false;

        return $this->single[$key];
    }

    public static function instance()
    {
        if (is_null(static::$instance))
            static::$instance = new static();

        return static::$instance;
    }

    public static function setInstance(Container $container)
    {
        static::$instance = $container;
    }
}