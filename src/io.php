<?php

namespace markdi;

class io {
    use container;
    use markers;

    private $verbose = false;

    private $config = [
        'from' => 'src',
        'to' => 'markers',
        'name' => false,
    ];
    private $struct = [];
    private $entry = './';
    private $links = [];
    private $namespaces = [];
    private $composerJson;
    private $composerJsonFile;


    protected function run($path){
        if ($composerRoot = $this->findComposerJson($path)){
            $this->composerJsonFile = "$composerRoot/composer.json";
            $this->composerJson = json_decode(file_get_contents($this->composerJsonFile), true);
            $this->composerName = isset($this->composerJson['name'])?$this->composerJson['name']:'myapp';
            if (isset($this->composerJson['markdi'])){
                $this->setConfig($this->composerJson['markdi']);
                $this->pars($composerRoot);
            } else {
                $this->error("add to composer.json prop \"markid\"\n");
            }
        } else {
            $this->error("composer.json not found in ($path)\n");
        }
    }



    function findComposerJson($path){
        for ($i=0; $i < 3; $i++) { 
            $path = dirname($path);
            if (file_exists("$path/composer.json")){
                return $path;
            }
        }
        return false;
    }



    function setConfig($config){
        if (is_array($config)){
            $this->config = array_merge($this->config, $config);
        } else {
            $this->config['from'] = $config;
            if ($config=='src'){
                $this->config['name'] = $this->composerName;
            } else {
                $this->config['name'] = $config;
            }
        }
    }



    function pars($root){
        $this->entry = $root . '/' . $this->config['from'];
        $this->scan();
        $this->checkNameSpaces();
        $this->changeComposerJson();
    }


    function changeComposerJson(){
        if (!isset($this->composerJson['autoload']))
            $this->composerJson['autoload'] = [];

        if (!isset($this->composerJson['autoload']['psr-4']))
            $this->composerJson['autoload']['psr-4'] = [];

        $this->composerJson['autoload']['psr-4'] = array_merge(
            $this->composerJson['autoload']['psr-4'],
            $this->namespaces
        );

        file_put_contents($this->composerJsonFile, json_encode($this->composerJson, JSON_PRETTY_PRINT));
    }


    function scan($dir = ''){
        if ($this->verbose)
            echo "$dir\n";

        foreach (scandir("$this->entry/$dir") as $elm) {
            if (in_array($elm, ['.', '..']))
                continue;

            if (is_dir("$this->entry/$dir/$elm")){
                if (!str_starts_with($elm, '_'))
                    $this->scan("$dir/$elm");
            } else {
                $_ = explode('.', $elm);
                if (end($_) == 'php'){
                    if (!isset($this->struct[$dir]))
                        $this->struct[$dir] = [
                            'namespace' => $this->config['name'] . $dir . '/',
                            'files' => [],
                        ];
                    
                    $this->struct[$dir]['files'][] = $elm;
                    if ($this->verbose)
                        echo " - $elm\n";
                }
            }
        }
    }


    function checkNameSpaces(){
        foreach ($this->struct as $dir => $extract) {
            extract($extract);
            foreach ($files as $fileName) {
                $this->setNameSpace($dir, $fileName, $namespace);
            }
        }

        $this->createDiLinks();
    }



    function setNameSpace($dir, $fileName, $namespace){
        $this->updateFileClass($dir, $fileName, $namespace);

        if ($dir){
            $firstDir = explode('/', $dir)[1];


            if (!isset($this->links[$firstDir]))
                $this->links[$firstDir] = [];


            $this->links[$firstDir][] = [
                'file' => $fileName,
                'namespace' => $namespace,
                'unique' => false,
            ];
        }
    }



    function updateFileClass($dir, $fileName, $namespace){
        $code = file_get_contents("{$this->entry}$dir/$fileName");

        $className = explode('.', $fileName)[0];
        $this->parsfab->parse($code);
        $this->parsfab->namespace(str_replace('/', '\\',trim($namespace, '/')), $className);
        $code2 = $this->parsfab->getCode();

        file_put_contents("{$this->entry}$dir/$fileName", $code2);
    }



    function rrmdir($dir){
        if (is_dir($dir)){
            $objects = scandir($dir);

            foreach ($objects as $object){
                if ($object != '.' && $object != '..'){
                    if (filetype($dir.'/'.$object) == 'dir') {
                        $this->rrmdir($dir.'/'.$object);
                    }
                    else {unlink($dir.'/'.$object);}
                }
            }

            reset($objects);
            rmdir($dir);
        }
    }



    function createDiLinks(){
        $to = $this->config['to'];
        if (!str_starts_with($to, '_'))
            $to = "_$to";

        if (file_exists("$this->entry/$to"))
            $this->rrmdir("$this->entry/$to");

        $rootNameSpace = $this->config['name'];
        foreach ($this->links as $fileLinkName => $methods) {
            $activeNamespace = "$rootNameSpace\\$to\\";
            $linkDir = "$rootNameSpace/$to/";

            $this->namespaces[$activeNamespace] = $linkDir;

            $list = [];
            $use = [];
            foreach ($methods as $data) {
                $className = explode('.', $data['file'])[0];
                $method = lcfirst($className);
                $namespace = str_replace('/', '\\', $data['namespace']);
                
                $this->namespaces[$namespace] = $data['namespace'];

                $use[] = "use {$namespace}{$className};";
                $list[] = "\tfunction $method():{$className}{return new {$className};} ";
            }
            $listStr = implode("\n", $list);
            $useStr  = implode("\n", $use);


            $content = <<<CODE
            <?php
            namespace $activeNamespace;
            use markdi\markdi;
            $useStr

            trait $fileLinkName {
            \tuse markdi;

            $listStr
            }
            CODE;

            

            

            if (!file_exists($linkDir))
                mkdir($linkDir, 0777, true);

            file_put_contents("$linkDir/$fileLinkName.php", $content);
        }
    }



    function error($message){
        fwrite(STDERR, $message);
        die(1);
    }
}