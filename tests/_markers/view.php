<?php
namespace tests\_markers;
use markdi\markdi;
use tests\view\MainView;

trait view {
	use markdi;

	function mainView():MainView{return new MainView;} 
}