<?php
namespace tests\_markers\;
use markdi\markdi;
use tests\viewz\view1;

trait viewz {
	use markdi;

	function view1():view1{return new view1;} 
}