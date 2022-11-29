<?php

namespace test;

use test\_markers\os;

class PC {
    use os;

    function getOs(){
        return $this->mac->getName();
    }

}