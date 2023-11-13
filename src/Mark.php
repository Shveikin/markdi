<?php

namespace markdi;

#[\Attribute]
class Mark {

    const GLOBAL = 1;
    const LOCAL  = 2;
    const FRESH  = 3;

    function __construct(public string $title, public string $mode = Mark::GLOBAL){
        
    }

}