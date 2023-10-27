<?php 

declare(strict_types=1);

use markdi\example\User;
use markdi\MarkTestCase;

final class GreeterTest extends MarkTestCase
{
    public function testFirst(): void
    {
        
        $user = new User('Jhon');

        $this->assertEquals('hello Jhon', $user->helloFromFriends());
        $this->assertEquals('Jhon', $user->friends->getFriendName());


    }

    public function testWithOtherName(): void
    {
        
        $user = new User('Ivan');

        $this->assertEquals('hello Ivan', $user->helloFromFriends());
        $this->assertEquals('Ivan', $user->friends->getFriendName());


    }
}