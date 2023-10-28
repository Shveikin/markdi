<?php

namespace markdi\example;

use markdi\example\components\markers\tools;

class User {
    use tools;

    function __construct(
        public readonly string $name
    )
    {}

    function helloFromFriends(){
        return $this->friends->seyHello();
    }


    function ___get($var){
        return "$var - is var";
    }

}