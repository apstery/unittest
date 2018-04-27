<?php

use Unittest\Testcase;

class MyTestCase extends Testcase
{
    public function testCanDoStuff()
    {
        $foo = 'bar';
        $this->assertEquals('foo', $foo);
    }
}
