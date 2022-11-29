<?php

namespace test;

use markdi\container;

class Log {
    use container;


    protected function loggg(){
        echo "\n\nlog test\n";
    }
}