<?php

namespace markdi\example\components\tools;

use markdi\example\User;

class Friends {

    function __construct
    (
        private User $user,
    )
    {}


    function seyHello(){
        echo "hello {$this->user->name}\n";
    }
}