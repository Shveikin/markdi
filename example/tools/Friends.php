<?php

namespace markexample\tools;
use markexample\User;



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