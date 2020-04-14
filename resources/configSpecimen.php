<?php declare(strict_types=1);

use Przeslijmi\Sirouter\Sirouter;
use Przeslijmi\Sirouter\Helpers\ExampleRsrc;


/////////////////////////
// Routes for testing. //
/////////////////////////

Sirouter::register('/_test_', 'GET')
    ->setCall(ExampleRsrc::class, 'get');

Sirouter::register('/_test_/(\d+)/params/(\d+)', 'PUT')
    ->setCall(ExampleRsrc::class, 'put')
    ->setParam(0, 'testParam1')
    ->setParam(1, 'testParam2');

Sirouter::register('/_testScalar_', 'GET')
    ->setCall(ExampleRsrc::class, 'getScalar');

Sirouter::register('/_testWrotype_', 'GET')
    ->setCall(ExampleRsrc::class, 'getWrotype');

Sirouter::register('/_testTxt_', 'GET')
    ->setCall(ExampleRsrc::class, 'getTxt');

// method 'delete' is not present in the class on test purpose
Sirouter::register('/_test_', 'DELETE')
    ->setCall(ExampleRsrc::class, 'delete');

// this class do not exists on test purpose
Sirouter::register('/_testNonexistingClass_', 'GET')
    ->setCall('Przeslijmi\Sirouter\Helpers\ExampleNonexistingRsrc', 'get');

// this class exists but is not Rsrc class on test purpose
Sirouter::register('/_testNonRsrcClass_', 'GET')
    ->setCall('Przeslijmi\Sirouter\Sirouter', 'get');
