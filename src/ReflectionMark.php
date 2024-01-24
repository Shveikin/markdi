<?php

namespace markdi;

class ReflectionMark
{

    public string $prop;
    public string $namespace;
    public string $shortName;

    public string $marker;
    public string $markerClass;
    public string $markerNamespace;
    public bool $isMarkerInit = false;

    public int $mode = Mark::GLOBAL;
    public array $args = [];
    public $exception = false;




    function __construct(public string $className)
    {
        try {
            $this->handle();
        } catch (\Throwable $th) {
            $this->exception = $th->getMessage();
        }
    }


    private function except(string $message){
        $this->exception = $message;
    }

    private function handle()
    {

        try {
            $reflection = new \ReflectionClass($this->className);
        } catch (\ReflectionException $th) {
            return $this->except("class not exists or not class");
        }

        if ($reflection->isTrait())
            return $this->except("is trait");

        $this->namespace = $reflection->getNamespaceName();
        $this->shortName = $reflection->getShortName();

        
        $this->setMarkerInfo();

        $this->prop = lcfirst($this->shortName);

        if ($reflection->isAbstract())
            return $this->except("is abstract");


        if (str_starts_with($this->marker, '_'))
            return $this->except("pass");



        $notMark = $reflection->getAttributes(NotMark::class);
        if (!empty($notMark))
            return;


        $attr = $reflection->getAttributes(Mark::class);
        if (!empty($attr)) {
            $classMarkAttribute = $attr[0]->newInstance();
            if ($classMarkAttribute->title)
                $this->prop = $classMarkAttribute->title;

            $this->mode = $classMarkAttribute->mode;
            $this->args = $classMarkAttribute->args;
            return;
        }


        $attr = $reflection->getAttributes(MarkInstance::class);
        if (!empty($attr)) {
            $classMarkAttribute = $attr[0]->newInstance();
            if ($classMarkAttribute->title)
                $this->prop = $classMarkAttribute->title;

            $this->mode = Mark::INSTANCE;


            $constructor = $reflection->getConstructor();
            if (!$constructor)
                return;

            $props = $constructor->getParameters();
            foreach ($props as $prop) {
                $defaultValue = null;
                if ($prop->isDefaultValueAvailable()) {
                    $defaultValue = $prop->getDefaultValue();
                }

                $full = trim($prop->getType() . ' $' . $prop->getName());

                if (!is_null($defaultValue)) {
                    $full .= " = " . var_export($defaultValue, true);
                }

                $variadi = '';
                if ($prop->isVariadic()){
                    $variadi = '...';
                }
                $this->args["$variadi$full"] = "$variadi$" . $prop->getName();
            }
            return;
        }
    }



    private function setMarkerInfo()
    {
        [$main, $this->marker] = explode('\\', "$this->namespace\\main");

        $this->markerClass = "{$main}\\_markers\\{$this->marker}";
        $this->markerNamespace = "{$main}\\_markers";
    }


    private function checkMarkerIsInit()
    {
        return false;
    }
}
