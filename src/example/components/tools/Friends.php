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
        return "hello {$this->getFriendName()}";
    }

    function getFriendName(){
        return $this->user->name;
    }
}