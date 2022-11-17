<?php
namespace tests\_markers;
use markdi\markdi;
use tests\view\kde;
use tests\view\vi;
use tests\view\logicx\MainView;
use tests\view\logicx\mainFPT\ORDER;

trait view {
	use markdi;

	function kde():kde{return new kde;} 
	function vi():vi{return new vi;} 
	function mainView():MainView{return new MainView;} 
	function oRDER():ORDER{return new ORDER;} 
}