<?php

namespace markexample\tools;

use markdi\Mark;
use markexample\User;


#[Mark('friends', ['parent'])]
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