<?php
namespace test\_markers;
use markdi\markdi;
use test\coolers\MarserColler;

trait coolers {
	use markdi;

	function marserColler():MarserColler{return new MarserColler;} 
}