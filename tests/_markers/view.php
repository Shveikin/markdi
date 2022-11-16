<?php
namespace tests\_markers;
use markdi\markdi;
use tests\view\DialogModel;
use tests\view\MainView;
use tests\view\nav\Navigator;

trait view {
	use markdi;

	function dialogModel():DialogModel{return new DialogModel;} 
	function mainView():MainView{return new MainView;} 
	function navigator():Navigator{return new Navigator;} 
}