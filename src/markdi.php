<?php

namespace markdi;


trait markdi
{
    public function __get($alias){
        
        if (!property_exists($this, $alias)) 
            $this->$alias = $this->__getAlias__($alias);


        return $this->$alias;
    }

    private function __getAlias__(string $alias){
        if (method_exists($this, "_$alias"))
            return $this->{"_$alias"}();

        if (method_exists($this, $alias))
            if (Container::isset($alias))
                return Container::get($alias);
            else
                return Container::set($alias, $this->$alias());
        else
            if (method_exists($this, '___get')) 
                return $this->___get($alias);

        throw new \Exception("$alias - not found");
    }

    private function super(string $alias){
        return fn($class) => Container::set($alias, $class);
    }
}
