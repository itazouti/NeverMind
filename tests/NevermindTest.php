<?php


use PHPUnit\Framework\TestCase;


class NevermindTest extends TestCase
{
          public $NM;


    public function setUp() {
        $this->NM = AppTest::getNeverMindClass();
    }




    public function Send_start(){
        $json_result = $this->NM->send_start();
        $result = json_decode($json_result,true);

        $this->assertArrayHasKey('size', $result);
        $this->assertArrayHasKey('quizz_id', $result);

    }

    public function testDouble(){
        $this->assertEquals(4,HelloWorld::double(2));
    }
    public function testDoubleifZero(){
        $this->assertEquals(0,HelloWorld::double(0));
    }




}


