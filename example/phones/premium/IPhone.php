<?php

namespace markexample\phones\premium;

use markdi\Mark;
use markdi\MarkInstance;

#[MarkInstance('iphone')]
class IPhone {
    
    function __construct(private int $version){

    }

}