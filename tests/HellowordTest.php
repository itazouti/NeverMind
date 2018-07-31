<?php

use PHPUnit\Framework\TestCase;


class HellowordTest extends TestCase
{

    public function testDouble(){
        $this->assertEquals(4,HelloWorld::double(2));
    }


    public function testDoubleifZero(){
        $this->assertEquals(0,HelloWorld::double(0));
    }

}