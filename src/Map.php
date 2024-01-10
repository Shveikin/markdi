<?php

namespace markdi;

class Map
{
    static $relationship = [];
    static $classes = [];
    static $history = [];

    static function classId($class){
        $id = spl_object_id($class);
        $className = get_class($class);

        if (!isset(Map::$classes[$id]))
            Map::$classes[$id] = $className;

        return $id;
    }

    static function link($classId, string $alias, $elementId)
    {
        Map::$relationship[$classId][$alias] = $elementId;
        Map::$history[] = Map::$relationship;
    }

    static function log(string $message){
        Map::$history[] = $message;
    }

    static function getLiveData(){
        return [
            "classes" => Map::$classes,
            "history" => Map::$history,
        ];
    }


    static function get(){

    }
}
