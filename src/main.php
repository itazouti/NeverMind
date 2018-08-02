<?php
include('nevermind.php');
//C:\Users\msa>cd /php
//C:\php>php c:\var\www\html\Nevermind\src\nevermind.php

echo "START Nevermind\n";

$NM = new neverMind();
$NM->init();
$NM->start();
$NM->test_ciffers();
$NM->test_positions();

echo "FINISH Nevermind\n";
?>