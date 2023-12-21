<?php

namespace markdi;




class Runner
{

    private $out;


    function __construct($dir, $psr4, string $out = 'markers')
    {
        $this->out = $out;
        foreach ($psr4 as $namespace => $folder) {
            $this->check($dir . "/$folder", $namespace);
        }
    }


    private function clearOutput($path)
    {
        if (file_exists("$path/_$this->out")) {
            $this->removeFolder("$path/_$this->out");
        }

        if (!file_exists("$path/_$this->out"))
            mkdir("$path/_$this->out", 0777, true);
    }

    private function check($path, $namespace)
    {
        $this->clearOutput($path);

        $list = [];

        $markers = array_diff(scandir($path), ['.', '..']);

        // создаю пустышки
        foreach ($markers as $marker) {
            if (str_starts_with($marker, '_') || !is_dir("$path/$marker"))
                continue;
            $current_namespace = str_replace("\\\\", "\\", "$namespace\\_$this->out");
            file_put_contents("$path/_$this->out/$marker.php", <<<PHP
            <?php namespace $current_namespace;
            trait $marker{}
            PHP);
        }

        // анализирую классы
        foreach ($markers as $marker) {
            if (str_starts_with($marker, '_') || !is_dir("$path/$marker"))
                continue;

            $current_namespace = str_replace("\\\\", "\\", "$namespace\\$marker");
            $this->findClasses($list[$marker], "$current_namespace", "$path$marker");
            if (!$list[$marker])
                unset($list[$marker]);
        }


        if (!empty($list))
            $this->writeMarkers($namespace, $path, $list);
    }

    private function findClasses(&$list, $namespace, $path)
    {


        $files = array_diff(scandir($path), ['.', '..']);
        foreach ($files as $file) {
            if (is_dir("$path/$file"))
                continue;

            $info = pathinfo("$path/$file");
            ['filename' => $class, 'extension' => $extension] = $info;
            if ($extension == 'php') {
                $reflection = new ReflectionMark($namespace, $class);
                if (!$reflection->exception) {
                    if (!$list)
                        $list = [];

                    $list[] = $reflection;
                }
            }
            // if ($classInfo = $this->getClassInfo($namespace, $class)) {
            //     if (!$list)
            //         $list = [];

            //     $list[] = $classInfo;
            // }
        }
    }






    // private function getClassInfo($namespace, $class)
    // {
    //     $full = "$namespace\\$class";
    //     $title = lcfirst($class);
    //     $mode = Mark::GLOBAL;
    //     $args = [];

    //     try {
    //         $this->bindProps($full, $title, $mode, $args);
    //     } catch (\Throwable $th) {
    //         echo "ignore - $class\n";
    //         echo "\t> " . $th->getMessage() . "\n";
    //         return;
    //     }


    //     return new class($full, $title, $class, $mode, $args)
    //     {
    //         function __construct(
    //             public string $full,
    //             public string $title,
    //             public string $class,
    //             public string $mode,
    //             public array  $args,
    //         ) {
    //         }
    //     };
    // }

    private function removeFolder(string $path)
    {
        if (PHP_OS === 'Windows') {
            exec(sprintf("rd /s /q %s", escapeshellarg($path)));
        } else {
            exec(sprintf("rm -rf %s", escapeshellarg($path)));
        }
    }

    private function writeMarkers($root, $path, $list)
    {
        foreach ($list as $marker => $classes) {
            $namespaces = "";
            $varibles = "";
            $methods = "";

            foreach ($classes as $class) {
                $props = $this->getProps($class->args, $class->title, $class->mode);
                $namespaces .= "use $class->full;\n";

                if ($class->mode != Mark::INSTANCE)
                    $varibles   .= " * @property-read $class->class \${$class->title}\n";

                $mehodProps = $class->mode == Mark::INSTANCE ? $this->wrap(array_keys($class->args)) : '()';
                $modeSymbol = $class->mode == Mark::LOCAL    ? '_'    : '';
                $methods   .= "   function $modeSymbol{$class->title}$mehodProps: {$class->class} { return new {$class->class}$props; }\n";
            }

            $code = <<<CODE
                <?php
                namespace {$root}_{$this->out};
                use markdi\markdi;
                $namespaces
                /**
                $varibles
                */
                trait $marker {
                    use markdi;

                $methods
                }
                CODE;

            file_put_contents("$path/_$this->out/$marker.php", $code);
        }
    }

    private function getProps(array $args, string $title, int $mode)
    {
        if ($mode == Mark::INSTANCE)
            return $this->wrap($args);

        $result = [];

        foreach ($args as $argument) {
            switch ($argument) {
                case 'parent':
                    $result[] = '$this';
                    break;

                case 'super':
                    $result[] = "\$this->super('$title')";
                    break;

                default:
                    $result[] = $argument;
            }
        }

        return $this->wrap($result);
    }


    private function wrap(array $props)
    {
        $resultStr = implode(', ', $props);
        return $resultStr ? "($resultStr)" : '';
    }
}
