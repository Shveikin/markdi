<?php

namespace markdi;


trait markdi
{
    public function __get($alias){
        
        if (!property_exists($this, $alias)) 
            $this->$alias = $this->__getAlias__($alias);


        return $this->$alias;
    }

    private function __getAlias__($alias){
        foreach ($this->xmergeusemethods() as $use) {
            if (method_exists($use, $alias)) {
                return MP::DI($this, $alias);
            }

            if (method_exists($use, "_$alias")) {
                return $this->{"_$alias"}();
            }
        }

        if (method_exists($this, '___get')) {
            return $this->___get($alias);
        }

        return false;
    }

    private function xmergeusemethods(){
        foreach (class_uses($this) as $use) {
            yield $use;
        }

        foreach (class_parents($this) as $parent) {
            foreach (class_uses($parent) as $use) {
                yield $use;
            }
        }

        return false;
    }
}
