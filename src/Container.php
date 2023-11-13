<?php

namespace markdi;


final class Container {

    static $namespace = 'default';
    static $list = [];

    static function isset($alias){
        return isset(static::$list[static::$namespace][$alias]);
    }

    static function get($alias){
        if (!isset(static::$list[static::$namespace][$alias]))
            return null;

        return static::$list[static::$namespace][$alias];
    }

    static function set($alias, $component){
        if (!isset(static::$list[static::$namespace]))
            static::$list[static::$namespace] = [];

        static::$list[static::$namespace][$alias] = $component;

        return $component;
    }

    static function reset(){
        static::$list = [];
    }

    static function resetNamespace(){
        static::$list[static::$namespace] = [];
    }

    static function setNamespace($name = 'default'){
        static::$namespace = $name;
    }

    static function runNamespace(string $name, callable $run){
        $old = static::$namespace;

        static::setNamespace($name);
        $run();
        static::setNamespace($old);
        unset(static::$list[$name]);
    }

}