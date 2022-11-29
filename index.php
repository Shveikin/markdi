<?php

require "./vendor/autoload.php";

use test\Log;
use test\PC;

$mypc = new PC;



echo $mypc->getOs();

$mypc->RESET_ALL_PROPS();

echo $mypc->getOs();


Log::loggg();

