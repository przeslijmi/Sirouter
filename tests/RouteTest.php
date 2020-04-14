<?php declare(strict_types=1);

namespace Przeslijmi\Sirouter;

use PHPUnit\Framework\TestCase;
use Przeslijmi\Sirouter\Route;
use Przeslijmi\Sexceptions\Exceptions\MethodFopException;

/**
 * Methods for testing Route class.
 */
final class RouteTest extends TestCase
{

    /**
     * Test if Route can be properly created and read.
     *
     * @return void
     */
    public function testProperCreation() : void
    {

        // Lvd.
        $uri       = '/_testUri_/(\d+)';
        $className = 'Przeslijmi\Sirouter\Helpers\ExampleRsrc';

        // Create Route.
        $route = new Route($uri);
        $route->setCall($className, 'get');
        $route->setParam(0, 'param1', '123Test');
        $route->setAttribute('attr1', 'value1');
        $route->setAttribute('attr2', 'value2');
        $route->setAttributesFromString('attr3=value3&attr4=value4');

        // Test.
        $this->assertEquals('GET', $route->getHttpMethod());
        $this->assertEquals('GET:' . $uri, $route->getSignature());
        $this->assertEquals($className, $route->getClassName());
        $this->assertEquals('get', $route->getMethodName());
        $this->assertEquals('', $route->getBody());
        $this->assertEquals([ 'param1' ], $route->getParamsNames());
        $this->assertEquals('123Test', $route->getParam('param1'));
        $this->assertEquals('value1', $route->getAttribute('attr1'));
        $this->assertEquals('value2', $route->getAttribute('attr2'));
        $this->assertEquals('value3', $route->getAttribute('attr3'));
        $this->assertEquals('value4', $route->getAttribute('attr4'));
        $this->assertEquals(null, $route->getAttributeIfExists('nonexistingAttribute'));
        $this->assertEquals('value1', $route->getAttributeIfExists('attr1'));
    }

    /**
     * Test if trying to read nonexisting param will throw.
     *
     * @return void
     */
    public function testReadingNonexistingParam() : void
    {

        // Create Route.
        $route = new Route('/_testUri_');

        // Prepare.
        $this->expectException(MethodFopException::class);

        // Test.
        $route->getParam('nonexistingParam');
    }

    /**
     * Test if trying to read nonexisting attribute will throw.
     *
     * @return void
     */
    public function testReadingNonexistingAttribute() : void
    {

        // Create Route.
        $route = new Route('/_testUri_');

        // Prepare.
        $this->expectException(MethodFopException::class);

        // Test.
        $route->getAttribute('nonexistingAttribute');
    }
}
