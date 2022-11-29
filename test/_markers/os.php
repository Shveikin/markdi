<?php
namespace test\_markers;
use markdi\markdi;
use test\os\Linux;
use test\os\Mac;
use test\os\Windows;

trait os {
	use markdi;

	function linux():Linux{return new Linux;} 
	function mac():Mac{return new Mac;} 
	function windows():Windows{return new Windows;} 
}