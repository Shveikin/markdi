<?php

namespace markdi\example\components\markers;

use markdi\example\components\tools\Friends;
use markdi\markdi;

/** 
 * @property-read Friends $friends
*/

trait tools {
    use markdi;

    function friends(): Friends {
        return new Friends($this);
    }
}