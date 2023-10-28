<?php 

declare(strict_types=1);

use markdi\example\User;
use markdi\MarkTestCase;

final class MagicTest extends MarkTestCase
{
    public function testUndefinedProp(): void
    {
        $user = new User('Max');
        $this->assertEquals('foo - is var', $user->foo);
    }

}