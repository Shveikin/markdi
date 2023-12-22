<?php

namespace markdi;

class ReflectionMark
{

    public string $prop;
    public string $namespace;
    public string $shortName;

    public string $marker;
    public string $markerClass;
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




    private function handle()
    {
        $reflection = new \ReflectionClass($this->className);

        $this->namespace = $reflection->getNamespaceName();
        $this->shortName = $reflection->getShortName();

        [$this->marker, $this->markerClass] = $this->getMarkerFromNamespace();
        $this->prop = lcfirst($this->shortName);


        if ($reflection->isAbstract()) {
            $this->exception = "abstract class";
            return;
        }



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

                $full = $prop->getType() . ' $' . $prop->getName();

                if (!is_null($defaultValue)) {
                    $full .= " = " . var_export($defaultValue, true);
                }
                $this->args[$full] = '$' . $prop->getName();
            }
            return;
        }
    }



    private function getMarkerFromNamespace()
    {
        [$main, $marker] = explode('\\', "$this->namespace\\\\");

        $markerClass = "{$main}\\_markers\\$marker";
        return [$marker, $markerClass];
    }


    private function checkMarkerIsInit()
    {
        return false;
    }
}
