<?php
namespace markexample\_markers;
use markdi\markdi;
use markexample\User;

/**
 * @property-read User $user

*/
trait main {
    use markdi;

   function user(): User { return new User; }

}