<?php
namespace markexample\_markers;
use markdi\markdi;
use markexample\phones\Xiaomi;
use markexample\phones\premium\IPhone;

/**
 * @property-read Xiaomi $xiaomi

*/
trait phones {
    use markdi;

   function xiaomi(): Xiaomi { return new Xiaomi; }
   function iphone(int $version): IPhone { return new IPhone($version); }

}