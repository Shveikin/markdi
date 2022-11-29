<?php

namespace markdi;

class MP {
    static $container = false;
    private $containers = [];
    private $reg = [];


    static function GET(...$props){
        $class = isset($props['class'])?$props['class']:(isset($props[0])?$props[0]:false);
        $alias = isset($props['alias'])?$props['alias']:(isset($props[1])?$props[1]:false);
        $constructor = isset($props['constructor'])?$props['constructor']:(isset($props[2])?$props[2]:false);

        if ($class!=false){
            $result = self::MAIN()->class($class, $alias, (array)$constructor);
        } else {
            $result = self::MAIN();
        }

        return $result;
    }

    static function DI($from, $alias){
        return self::MAIN()->autodi($from, $alias);
    }


    static function MAIN(){
        if (self::$container == false) {
            self::$container = new self();
        }

        return self::$container;
    }


    function autodi($from, $alias){
        if (!isset($this->containers[$alias])){
            $this->containers[$alias] = $from->{$alias}();
        }
        return $this->containers[$alias];
    }


    static function RESET(){
        self::MAIN()->reset_all_props();
    }

    private function reset_all_props(){
        $this->containers = [];
    }

    function autoprops($class, $alias, &$constructor):array {
        $parameters = (new \ReflectionClass($class))->getConstructor()?->getParameters();
                
        $result = [];
        $propsCounter = 0;
        if ($parameters)
        foreach ($parameters as $p){
            if ($p->name=='super'){
                $result[] = function($el) use ($alias){
                    $this->containers[$alias] = $el;
                };
            } else {
                if (isset($constructor[$p->name])){
                    $result[] = $constructor[$p->name];
                } else 
                if (isset($constructor[$propsCounter])){
                    $result[] = $constructor[$propsCounter];
                } else {
                    $result[] = false;
                }

                $propsCounter++;
            }
        }

        return $result;
    }

    function class(string $class, string|bool $alias = false, array $constructor = []){
        if ($alias==false) $alias = $class;

        if (!isset($this->reg[$alias])){
            if (!isset($this->containers[$alias])){
                $this->reg[$alias] = true;
                
                $tempElement = new $class(...$this->autoprops($class, $alias, $constructor));

                if (!isset($this->containers[$alias])){
                    $this->containers[$alias] = $tempElement;
                }

                if (method_exists($tempElement, '__constructor')){
                    $tempElement->__constructor();
                }
            }
        } else {
            if (!isset($this->containers[$alias]))
                throw new \Exception(" $class не реальзует функцию \$super(\$this) в __construct ", 1);
        }

        return $this->containers[$alias];
    }
}
