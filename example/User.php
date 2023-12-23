<?php

namespace markexample;
use markexample\_markers\cars;


class User {
    use cars;
    
    private $myCar;

    function __construct()
    {
        $this->myCar = $this->mercedes->setColor('black');
    }

}