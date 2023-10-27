<?php


namespace markdi;

use PHPUnit\Framework\TestCase;

class MarkTestCase extends TestCase {

    function setUp(): void {
        Container::reset();
    }

    function tearDown(): void {
        Container::reset();
    }
}