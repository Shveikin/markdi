<?php

namespace markdi;


class io {
    use container;

    public string $to = './_uses';

    protected function run($composer){
        var_export($composer);
        print_r(get_declared_classes());
    }

    function error($message){
        fwrite(STDERR, $message);
        die(1);
    }
}