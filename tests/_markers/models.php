<?php
namespace tests\_markers;
use markdi\markdi;
use tests\models\MainModel;

trait models {
	use markdi;

	function mainModel():MainModel{return new MainModel;} 
}