<?php
namespace tests\_markers\;
use markdi\markdi;
use tests\fibers\x;

trait fibers {
	use markdi;

	function x():x{return new x;} 
}