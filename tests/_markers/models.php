<?php
namespace tests\_markers;
use markdi\markdi;
use tests\models\GModel;

trait models {
	use markdi;

	function gModel():GModel{return new GModel;} 
}