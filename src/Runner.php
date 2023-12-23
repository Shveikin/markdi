<?php

namespace markdi;

use Composer\ClassMapGenerator\ClassMapGenerator;




class Runner
{
    private $out;
    private MarkerTraitBuilder $builder;

    function __construct($dir, $psr4, string $markersFolder = 'markers')
    {
        $this->out = $markersFolder;
        $this->builder = new MarkerTraitBuilder;

        foreach ($psr4 as $namespace => $folder) {
            if ($namespace=='markdi\\')
                continue;

            $this->handleMap($dir . "/$folder");
        }
    }



    private function handleMap($src)
    {
        $this->clearOutput($src);
        $map = ClassMapGenerator::createMap($src);
        $this->createEmptyMarkers($src, $map);

        $group = [];
        foreach ($map as $class => $file) {
            $mark = new ReflectionMark($class);
            if (!$mark->exception)
                $group[$mark->markerClass][] = $mark;
        }

        if (empty($group))
            return;

        foreach ($group as $markers) {
            $this->builder->create($src, $this->out, $markers);
        }
    }


    private function createEmptyMarkers($src, array $map){
        if (!file_exists("$src/_$this->out"))
            mkdir("$src/_$this->out", 0777, true);

        $added = [];

        foreach ($map as $class => $file) {
            [$main, $marker, $extra] = explode('\\', "$class\\@");
            if ($extra == '@')
                $marker = 'main';

            if (isset($added[$marker]) || str_starts_with($marker, '_'))
                continue;

            $code = <<<PHP
            <?php namespace $main\\_$this->out;
            trait $marker{}
            PHP;

            file_put_contents("$src/_$this->out/$marker.php", $code);

            $added[$marker] = true;
        }
    }


    private function clearOutput($path)
    {
        if (file_exists("$path/_$this->out")) {
            $this->removeFolder("$path/_$this->out");
        }
    }

    // private function check($path, $namespace)
    // {
    //     $this->clearOutput($path);

    //     $list = [];

    //     $markers = array_diff(scandir($path), ['.', '..']);

    //     // создаю пустышки
    //     foreach ($markers as $marker) {
    //         if (str_starts_with($marker, '_') || !is_dir("$path/$marker"))
    //             continue;
    //         $current_namespace = str_replace("\\\\", "\\", "$namespace\\_$this->out");
    //         file_put_contents("$path/_$this->out/$marker.php", <<<PHP
    //         <?php namespace $current_namespace;
    //         trait $marker{}
    //         PHP);
    //     }

    //     // анализирую классы
    //     foreach ($markers as $marker) {
    //         if (str_starts_with($marker, '_') || !is_dir("$path/$marker"))
    //             continue;

    //         $current_namespace = str_replace("\\\\", "\\", "$namespace\\$marker");
    //         $this->findClasses($list[$marker], "$current_namespace", "$path$marker");
    //         if (!$list[$marker])
    //             unset($list[$marker]);
    //     }


    //     if (!empty($list))
    //         $this->writeMarkers($namespace, $path, $list);
    // }

    // private function findClasses(&$list, $namespace, $path)
    // {


    //     $files = array_diff(scandir($path), ['.', '..']);
    //     foreach ($files as $file) {
    //         if (is_dir("$path/$file"))
    //             continue;

    //         $info = pathinfo("$path/$file");
    //         ['filename' => $class, 'extension' => $extension] = $info;
    //         if ($extension == 'php') {
    //             $reflection = new ReflectionMark("$namespace\\$class");
    //             if (!$reflection->exception) {
    //                 if (!$list)
    //                     $list = [];

    //                 $list[] = $reflection;
    //             }
    //         }
    //     }
    // }






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
}
