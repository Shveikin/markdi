<?php
namespace tests\_markers\;
use markdi\markdi;
use tests\models\log;

trait models {
	use markdi;

	function log():log{return new log;} 
}