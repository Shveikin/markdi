<?php
namespace markexample\_markers;
use markdi\markdi;
use markexample\insts\NewInstance;

/**

*/
trait insts {
    use markdi;

   function newInstance(): NewInstance { return new NewInstance; }

}