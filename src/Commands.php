<?php


namespace markdi;


class Commands {

    public static function createMarkScript($event){
        $composer = $event->getComposer();

        $composerFile = $composer->getConfig()->get('vendor-dir') . '/../composer.json';

        $json = json_decode(file_get_contents($composerFile), true);
        $json['scripts']['mark'] = 'php vendor/bin/mark';

        file_put_contents($composerFile, json_encode($json, JSON_PRETTY_PRINT));
    }

}