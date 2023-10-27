<?php

namespace markdi;


class Container {

    static $list = [];

    static function isset($alias){
        return isset(static::$list[$alias]);
    }

    static function get($alias){
        if (!isset(static::$list[$alias]))
            return null;

        return static::$list[$alias];
    }

    static function set($alias, $component){
        static::$list[$alias] = $component;

        return $component;
    }

    static function reset(){
        static::$list = [];
    }

}