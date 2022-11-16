<?php

namespace markdi;

use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

class io {
    use container;

    private $verbose = false;

    private $config = [
        'from' => 'src',
        'to' => '_links',
    ];
    private $struct = [];


    protected function run($path){
        if ($composerRoot = $this->findComposerJson($path)){
            $composer = json_decode(file_get_contents("$composerRoot/composer.json"), true);
            if (isset($composer['markdi'])){
                $this->setConfig($composer['markdi']);
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
        }
    }



    function pars($root){
        $this->scan($root . '/' . $this->config['from']);
        print_r($this->struct);
    }


    function scan($dir){
        if ($this->verbose)
            echo "$dir\n";

        foreach (scandir($dir) as $elm) {
            if (in_array($elm, ['.', '..']))
                continue;

            if (is_dir("$dir/$elm")){
                $this->scan("$dir/$elm");
            } else {
                $_ = explode('.', $elm);
                if (end($_) == 'php'){
                    if (!isset($this->struct[$dir]))
                        $this->struct[$dir] = [];
                    
                    $this->struct[$dir][] = $elm;
                    if ($this->verbose)
                        echo " - $elm\n";
                }
            }
        }
    }


    function error($message){
        fwrite(STDERR, $message);
        die(1);
    }
}