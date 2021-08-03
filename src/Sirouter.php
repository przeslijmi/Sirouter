<?php declare(strict_types=1);

namespace Przeslijmi\Sirouter;

use Przeslijmi\Sexceptions\Exceptions\ClassDonoexException;
use Przeslijmi\Sexceptions\Exceptions\ClassWrotypeException;
use Przeslijmi\Sexceptions\Exceptions\MethodDonoexException;
use Przeslijmi\Sexceptions\Exceptions\MethodFopException;

/**
 * Finds Route for the application to use.
 *
 * ## Usage example
 * ```
 * try {
 *     Sirouter::call($_SERVER['PATH_INFO'], $_SERVER['REQUEST_METHOD'], $_SERVER['QUERY_STRING']);
 * } catch (\Exception $e) {
 *     throw (new FopException('routeCanNotBeServed', $e))
 *         ->addInfo('PATH_INFO', $_SERVER['PATH_INFO'])
 *         ->addInfo('REQUEST_METHOD', $_SERVER['REQUEST_METHOD'])
 *         ->addInfo('QUERY_STRING', $_SERVER['QUERY_STRING']);
 * }
 * ```
 */
class Sirouter
{

    /**
     * Possible HTTP Methods - that routes can use.
     *
     * @var   array
     * @since v1.0
     */
    const ACCEPTED_HTTP_METHODS = [ 'GET', 'POST', 'PUT', 'DELETE', 'PATCH' ];

    /**
     * How to serve results of calling resources.
     *
     * When set to:
     * - true - `header` and `echo` methods are called, as well as `return` with string,
     * - false - only `return` is called.
     *
     * Used for testing purposes.
     *
     * @var   boolean
     * @since v1.0
     */
    public static $sendingHeader = true;

    /**
     * Store with all stored routes that can be used.
     *
     * @var   Route[]
     * @since v1.0
     */
    private static $store = [];

    /**
     * Used to register new route in the store.
     *
     * @param string $url    What is the route url.
     * @param string $method Opt., empty., GET. Which HTTP method this route will use.
     *
     * @since  v1.0
     * @throws MethodFopException When creation of route has failed.
     * @return Route Created Route object.
     */
    public static function register(string $url, string $method = 'GET')
    {

        // Try to create.
        try {
            $route = new Route($url, $method);
        } catch (\Exception $e) {
            throw new MethodFopException('routeCanNotBeRegisteredBcsCreationOfThisRouteFailed', $e);
        }

        // Save route in store.
        self::$store[$route->getSignature()] = $route;

        return $route;
    }

    /**
     * Find which route has to be used for this call.
     *
     * @param string $url        Url that client asked for.
     * @param string $method     HTTP Method that client used.
     * @param string $attributes Opt., empty. Additional attribues that client sent (Query String).
     *
     * @since  v1.0
     * @throws ClassDonoexException On classThatServesRoute.
     * @throws MethodFopException On routeToNonexistingClass.
     * @throws ClassWrotypeException On routeClassHasToBeAChildOfResource.
     * @throws MethodFopException On routeToAClassWithAWrongParent.
     * @throws MethodDonoexException On methodThatServesRoute.
     * @throws MethodFopException On routeToNonexistingMethodInsideClass.
     * @throws MethodFopException On registeredRouteCanNotBeCalled .
     * @return void
     */
    public static function call(string $url, string $method, string $attributes = '') : void
    {

        // Define header.
        header('Access-Control-Allow-Origin: *');

        // Find route.
        $route = self::findRoute($url, $method);

        // If failed up to here - redirect response 404.
        if ($route === null) {
            http_response_code(404);
            header('HTTP/1.0 404 Not Found');
            return;
        }

        /*
         * if you're here - proper Route has been found
         */
        if (empty($attributes) === false) {
            $route->setAttributesFromString($attributes);
        }
        if ($method === 'POST') {
            $route->setAttributesFromArray($_POST);
        } elseif ($method === 'GET') {
            $route->setAttributesFromArray($_GET);
        }

        // Lvd.
        $className  = $route->getClassName();
        $methodName = $route->getMethodName();

        // Use this route.
        // try {

            // Chk if class exists.
            if (class_exists($className) === false) {
                try {
                    throw new ClassDonoexException('classThatServesRoute', $className);
                } catch (ClassDonoexException $e) {
                    throw (new MethodFopException('routeToNonexistingClass', $e));
                }
            }

            // Create class.
            $class = new $className();

            if ($class->getConstructionFailed() === true) {
                return;
            }

            // Chk if this is a Resource class.
            if (is_a($class, 'Przeslijmi\Sirouter\Resource') === false) {
                try {
                    throw new ClassWrotypeException(
                        'routeClassHasToBeAChildOfResource',
                        get_class($class),
                        'Przeslijmi\Sirouter\Resource'
                    );
                } catch (ClassWrotypeException $e) {
                    throw (new MethodFopException('routeToAClassWithAWrongParent', $e));
                }
            }

            // Check if there is method $methodName.
            if (method_exists($class, $methodName) === false) {
                try {
                    throw new MethodDonoexException('methodThatServesRoute', $className, $methodName);
                } catch (MethodDonoexException $e) {
                    throw (new MethodFopException('routeToNonexistingMethodInsideClass', $e));
                }
            }

            // Finally - call route - by call method - or directly.
            $class->setRoute($route);

            if (method_exists($class, 'call') === true) {
                $class->call($methodName);
            } else {
                $class->$methodName();
            }

        // } catch (\Exception $e) {
            // throw (new MethodFopException('registeredRouteCanNotBeCalled', $e))
                // ->addInfo('route', $route->getSignature());
        // }//end try
    }

    /**
     * Find which route fits for $url or $method. Return null if there is any.
     *
     * @param string $url    Url that client asked for.
     * @param string $method HTTP Method that client used.
     *
     * @since  v1.0
     * @return null|Route
     */
    private static function findRoute(string $url, string $method) : ?Route
    {

        // Lvd.
        $route   = null;
        $lookFor = $method . ':' . $url;

        // Try to find route directly.
        if (isset(self::$store[$lookFor]) === true) {
            $route = self::$store[$lookFor];
        }

        // If failed - try to look for it by going foreach.
        if ($route === null) {

            foreach (self::$store as $signature => $routeToTest) {

                $regex = '/^' . str_replace('/', '\\/', $signature) . '$/';
                preg_match_all($regex, $lookFor, $found);

                if (isset($found[0][0]) === true) {

                    // This is our route.
                    $route = $routeToTest;

                    // If there are params in this route.
                    if (count($found) > 1) {
                        $route->setParamsValuesFromRegex(array_slice($found, 1));
                    }

                    break;
                }
            }
        }//end if

        return $route;
    }
}
