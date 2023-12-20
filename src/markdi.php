<?php

namespace markdi;


trait markdi
{
    /**
     * @xdebug_never
     */
    public function __get($alias){
        if (!property_exists($this, $alias)) 
            $this->$alias = $this->__getAlias__($alias);

        return $this->$alias;
    }

    private function __getAlias__(string $alias){
        if (method_exists($this, "_$alias"))
            return $this->___bind($alias, $this->{"_$alias"}());

        if (method_exists($this, $alias))
            if (Container::isset($alias))
                return $this->___bind($alias, Container::get($alias));
            else
                return $this->___bind($alias, Container::set($alias, $this->$alias()));
        else
            if (method_exists($this, '___get')) 
                return $this->___bind($alias, $this->___get($alias));
    }

    private function ___bind($alias, $element){
        if (!isset($this->___binding___))
            $this->___binding___ = [];
        $this->___binding___[] = $alias;        

        return $element;
    }

    private function ___clearBindings(){
        foreach ($this->___binding___ as $prop) {
            unset($this->$prop);
        }
        $this->___binding___ = [];
    }

    private function super(string $alias){
        return fn($class) => Container::set($alias, $class);
    }

    protected function ___playground(string $name, callable $run){
        $this->___clearBindings();
        Container::runNamespace($name, $run);
        $this->___clearBindings();
    }
}
