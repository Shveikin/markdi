<?php

namespace markdi;

class ReflectionMark
{

    public string $full;
    public string $title;
    public string $mode = Mark::GLOBAL;
    public array $args = [];
    public $exception = false;




    function __construct($namespace, $class)
    {
        $this->full = "$namespace\\$class";
        $title = lcfirst($class);


        try {
            $this->bindProps();
        } catch (\Throwable $th) {
            $this->exception = $th->getMessage();
        }
    }




    private function bindProps()
    {
        $reflection = new \ReflectionClass($this->full);
        if ($reflection->isAbstract()) {
            $this->exception = "abstract class";
            return;
            throw new \Exception("abstract class", 0);
        }



        $notMark = $reflection->getAttributes(NotMark::class);
        if (!empty($notMark))
            return;


        $attr = $reflection->getAttributes(Mark::class);
        if (!empty($attr)) {
            $mark = $attr[0]->newInstance();
            if ($mark->title)
                $this->title = $mark->title;

            $this->mode = $mark->mode;
            $this->args = $mark->args;
            return;
        }


        $attr = $reflection->getAttributes(MarkInstance::class);
        if (!empty($attr)) {
            $mark = $attr[0]->newInstance();
            if ($mark->title)
                $this->title = $mark->title;

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
}
