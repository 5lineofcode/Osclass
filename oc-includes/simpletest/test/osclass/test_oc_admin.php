<?php

require_once('testSuite.php');
require_once('OCadminTest.php');
require_once('MyReporter.php');
require_once("util_settings.php");

$test = new AllAdminTests();
$test->run(new MyReporter());

?>
