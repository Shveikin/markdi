<?php

namespace markdi;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Mark
{

    const GLOBAL = 1;
    const LOCAL  = 2;
    const FRESH  = 3;

    function __construct(
        public string $title,
        public array $args = [],
        public string $mode = Mark::GLOBAL,
    ) {
    }
}
