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

        throw new \Exception("$alias - not found");
    }
}
