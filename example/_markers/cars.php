<?php
namespace markexample\_markers;
use markdi\markdi;
use markexample\cars\premium\Mercedes;
use markexample\cars\Reno;

/**
 * @property-read Mercedes $mercedes
 * @property-read Reno $mySuperCar

*/
trait cars {
    use markdi;

   function mercedes(): Mercedes { return new Mercedes; }
   function mySuperCar(): Reno { return new Reno; }

}