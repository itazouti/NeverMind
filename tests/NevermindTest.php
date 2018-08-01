<?php


use PHPUnit\Framework\TestCase;


class NevermindTest extends TestCase
{
          public $NM;


    public function setUp() {
        $this->NM = AppTest::getNeverMindClass();
    }




    public function testSend_start(){
        $json_result = $this->NM->send_start();
        $result = json_decode($json_result,true);

        $this->assertArrayHasKey('size', $result);
        $this->assertArrayHasKey('quizz_id', $result);

    }





}


