<?php
namespace markexample\_markers;
use markdi\markdi;
use markexample\tools\Friends;

/**
 * @property-read Friends $friends

*/
trait tools {
    use markdi;

   function friends(): Friends { return new Friends($this); }

}