<?php

namespace markdi;

use markdi\Mark;




class Runner
{

    private $out;


    function __construct($dir ,$psr4, string $out = 'markers')
    {
        $this->out = $out;
        foreach ($psr4 as $namespace => $folder) {
            $this->check($dir . "/$folder", $namespace);
        }
    }

    private function check($path, $namespace)
    {
        $list = [];

        $markers = array_diff(scandir($path), ['.', '..']);
        foreach ($markers as $marker) {
            if (str_starts_with($marker, '_') || !is_dir("$path/$marker"))
                continue;

            $list[$marker] = [];
            $current_namespace = str_replace("\\\\", "\\", "$namespace\\$marker");
            $this->findClasses($list[$marker], "$current_namespace", "$path$marker");
        }


        if (!empty($list))
            $this->writeMarkers($namespace, $path, $list);
    }

    private function findClasses(&$list, $namespace, $path)
    {
        $files = array_diff(scandir($path), ['.', '..']);
        foreach ($files as $file) {
            $info = pathinfo("$path/$file");
            ['filename' => $class, 'extension' => $extension] = $info;
            if ($extension == 'php')
                if ($classInfo = $this->getClassInfo($namespace, $class))
                    $list[] = $classInfo;
        }
    }


    private function getClassInfo($namespace, $class)
    {
        $full = "$namespace\\$class";
        $title = lcfirst($class);
        $mode = Mark::GLOBAL;

        try {
            $reflection = new \ReflectionClass($full);
            if ($reflection->isAbstract()) {
                echo "ignore - abstract $class\n";
                return;
            }

            $attr = $reflection->getAttributes(Mark::class);
            if (!empty($attr)) {
                $mark = $attr[0]->newInstance();
                $title = $mark->title;
                $mode = $mark->mode;
            }
        } catch (\Throwable $th) {
            echo "ignore - $class\n";
            echo $th->getMessage() . "\n";
            return;
        }



        return [
            'full' => $full,
            'title' => $title,
            'mode' => $mode,
            'class' => $class,
        ];
    }

    private function removeFolder(string $path)
    {
        if (PHP_OS === 'Windows') {
            exec(sprintf("rd /s /q %s", escapeshellarg($path)));
        } else {
            exec(sprintf("rm -rf %s", escapeshellarg($path)));
        }
    }

    private function writeMarkers($root, $path,$list)
    {
        if (file_exists("$path/_$this->out"))
            $this->removeFolder("$path/_$this->out");

        mkdir("$path/_$this->out");



        foreach ($list as $marker => $classes) {
            $namespaces = "";
            $varibles = "";
            $methods = "";
            foreach ($classes as [
                'full' => $full,
                'title' => $title,
                'mode' => $mode,
                'class' => $class,
            ]) {

                $namespaces .= "use $full;\n";
                $varibles   .= " * @property-read $class \$$title\n";
                $modeSymbol = $mode == Mark::LOCAL ? '_' : '';
                $methods    .= "   function $modeSymbol$title(): $class{ return new $class; }\n";
            }

            file_put_contents(
                "$path/_$this->out/$marker.php",
                <<<CODE
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
                CODE
            );
        }
    }
}

