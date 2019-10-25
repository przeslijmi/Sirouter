<?php declare(strict_types=1);

use Przeslijmi\Sirouter\Sirouter;



/////////////////////////
// Routes for testing. //
/////////////////////////

require('vendor/przeslijmi/sirouter/tests/Helpers/ExampleRsrc.php');

Sirouter::register('/_test_', 'GET')
    ->setCall('Przeslijmi\Sirouter\Helpers\ExampleRsrc', 'get');

Sirouter::register('/_test_/(\d+)/params/(\d+)', 'PUT')
    ->setCall('Przeslijmi\Sirouter\Helpers\ExampleRsrc', 'put')
    ->setParam(0, 'testParam1')
    ->setParam(1, 'testParam2');

Sirouter::register('/_testScalar_', 'GET')
    ->setCall('Przeslijmi\Sirouter\Helpers\ExampleRsrc', 'getScalar');

Sirouter::register('/_testWrotype_', 'GET')
    ->setCall('Przeslijmi\Sirouter\Helpers\ExampleRsrc', 'getWrotype');

Sirouter::register('/_testTxt_', 'GET')
    ->setCall('Przeslijmi\Sirouter\Helpers\ExampleRsrc', 'getTxt');

// method 'delete' is not present in the class on test purpose
Sirouter::register('/_test_', 'DELETE')
    ->setCall('Przeslijmi\Sirouter\Helpers\ExampleRsrc', 'delete');

// this class do not exists on test purpose
Sirouter::register('/_testNonexistingClass_', 'GET')
    ->setCall('Przeslijmi\Sirouter\Helpers\ExampleNonexistingRsrc', 'get');

// this class exists but is not Rsrc class on test purpose
Sirouter::register('/_testNonRsrcClass_', 'GET')
    ->setCall('Przeslijmi\Sirouter\Sirouter', 'get');
