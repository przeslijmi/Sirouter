<?php declare(strict_types=1);

namespace Przeslijmi\Sirouter;

use PHPUnit\Framework\TestCase;
use Przeslijmi\Sexceptions\Exceptions\MethodFopException;
use Przeslijmi\Sirouter\Tools\AttributeExploder;

/**
 * Methods for testing Sirouter class.
 *
 * Make sure to include `/config/routesForTesting.php` in `/config/.config.php`
 * to have /_test_ routes included in Router.
 */
final class SirouterTest extends TestCase
{

    /**
     * Test if registering proper route works.
     *
     * @return void
     */
    public function testRegisteringProperRoute() : void
    {

        // Lvd.
        $routeExpected = new Route('/_registeringTest_', 'GET');

        // Register route (route obj. will be returned).
        $route = Sirouter::register('/_registeringTest_');

        $this->assertEquals($routeExpected, $route);
    }

    /**
     * Test if registering inproper route throws.
     *
     * @return void
     */
    public function testRegisteringInproperRoute() : void
    {

        // Prepare.
        $this->expectException(MethodFopException::class);

        // Register route expecting a throw.
        $route = Sirouter::register('/_registeringTest2_', 'INPROPER_METHOD!');
    }

    /**
     * Test if `/_test_` route will be called properly and return good response.
     *
     * @return void
     */
    public function testCallingExistingJsonRoute() : void
    {

        // Lvd.
        $responseExpected = [
            'msg' => 'This is answer for GET test call!',
        ];
        $responseExpected = json_encode($responseExpected, JSON_PRETTY_PRINT);

        // Block buffer.
        ob_start();

        // Call existing route (see above).
        Sirouter::call('/_test_', 'GET');

        // Read buffer.
        $response = ob_get_clean();

        // Test.
        $this->assertEquals(200, http_response_code());
        $this->assertEquals($responseExpected, $response);
    }

    /**
     * Test if `/_testTxt_` route will be called properly and return good response.
     *
     * @return void
     */
    public function testCallingExistingTxtRoute() : void
    {

        // Lvd.
        $responseExpected = '"OK"';

        // Block buffer.
        ob_start();

        // Call existing route (see above).
        Sirouter::call('/_testTxt_', 'GET');

        // Read buffer.
        $response = ob_get_clean();

        // Test.
        $this->assertEquals(200, http_response_code());
        $this->assertEquals($responseExpected, $response);
    }

    /**
     * Test if test route with params will be called properly and return good response.
     *
     * @return void
     *
     * @phpcs:disable Zend.NamingConventions.ValidVariableName.ContainsNumbers
     */
    public function testCallingExistingRouteWithParams() : void
    {

        // Lvd.
        $param1           = (string) rand(1000, 9999);
        $param2           = (string) rand(1000, 9999);
        $responseExpected = [
            'msg' => 'This is answer for GET test call!',
            'params' => [
                'testParam1' => $param1,
                'testParam2' => $param2,
            ],
        ];
        $responseExpected = json_encode($responseExpected, JSON_PRETTY_PRINT);

        // Block buffer.
        ob_start();

        // Call existing route (see above).
        Sirouter::call('/_test_/' . $param1 . '/params/' . $param2, 'PUT');

        // Read buffer.
        $response = ob_get_clean();

        // Test.
        $this->assertEquals(200, http_response_code());
        $this->assertEquals($responseExpected, $response);
    }

    /**
     * Test if calling `/_testNonexistingClass_` will throw good response.
     *
     * @return void
     */
    public function testCallingExistingRouteWithNonexistingClass() : void
    {

        // Prepare.
        $this->expectException(MethodFopException::class);

        // Call existing route with nonexisting class (see above).
        Sirouter::call('/_testNonexistingClass_', 'GET');
    }

    /**
     * Test if calling `/_testNonRsrcClass_` will throw good response.
     *
     * @return void
     */
    public function testCallingExistingRouteWithNonRsrcClass() : void
    {

        // Prepare.
        $this->expectException(MethodFopException::class);

        // Call existing route with nonexisting class (see above).
        Sirouter::call('/_testNonRsrcClass_', 'GET');
    }

    /**
     * Test if calling `/_testNonRsrcClass_` will throw good response.
     *
     * @return void
     */
    public function testCallingExistingRouteWithNonexistingMethod() : void
    {

        // Prepare.
        $this->expectException(MethodFopException::class);

        // Call existing route with nonexisting class (see above).
        Sirouter::call('/_test_', 'DELETE');
    }

    /**
     * Test if calling nonexisting route will return 404 HTTP code.
     *
     * @return void
     */
    public function testCallingNonexistingRoute() : void
    {

        // Block buffer.
        ob_start();

        // Call any route that does not exists.
        Sirouter::call('/*nonexisting-path*', 'GET');

        // Read buffer.
        $response = ob_get_clean();

        // Test.
        $this->assertEquals(404, http_response_code());
    }

    /**
     * Test if calling route the response with scalars works.
     *
     * @return void
     */
    public function testCallingRouteThatReturnsScalars() : void
    {

        // Lvd.
        $responseExpected = [ true ];
        $responseExpected = json_encode($responseExpected, JSON_PRETTY_PRINT);

        // Block buffer.
        ob_start();

        // Call existing route (see above).
        Sirouter::call('/_testScalar_', 'GET');

        // Read buffer.
        $response = ob_get_clean();

        // Test.
        $this->assertEquals(200, http_response_code());
        $this->assertEquals($responseExpected, $response);
    }

    /**
     * Test if calling route that returns wrong type of response throws.
     *
     * @return void
     */
    public function testCallingRouteThatReturnsWrotype() : void
    {

        // Prepare.
        $this->expectException(MethodFopException::class);

        // Call existing route (see above).
        Sirouter::call('/_testWrotype_', 'GET');
    }
}
