<?php

namespace Przeslijmi\Sirouter;

use Przeslijmi\Sexceptions\Exceptions\TypeHintingFailException;
use Przeslijmi\Sexceptions\Exceptions\MethodFopException;

/**
 * Parent for all Restful answering classes (ie. Resources).
 */
class Resource
{

    /**
     * Route that has been called.
     *
     * @var   Route
     * @since v1.0
     */
    protected $route;

    /**
     * Sends JSON response to the client.
     *
     * @param scalar|array|stdClass $response Response to be showed as JSON.
     * @param integer               $code     (opt., 200) HTTP Response code.
     *
     * @since  v1.0
     * @throws MethodFopException
     * @return void
     */
    protected function sendJson($response, int $code=200) : void
    {

        // set headers
        http_response_code($code);
        header("Content-type: application/json; charset=utf-8");

        // reformat $response to encode it to JSON
        $isArray = (is_array($response));
        $isScalar = (is_scalar($response));
        $isStdClass = (is_object($response) && is_a($response, 'stdClass'));

        if ($isArray === false && $isScalar === true) {
            $response = [ $response ];
            $isArray = true;
        }

        // response is WroType - throw
        if ($isArray !== true && $isStdClass !== true) {
            try {
                throw new TypeHintingFailException('scalarArrayOrStdClass', get_class($response));
            } catch (TypeHintingFailException $e) {
                throw new MethodFopException('routeCanNotSendResponseBcsResponseIsWrotype', $e);
            }
        }

        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * Setter for `$this->route`.
     *
     * @param Route $route Route.
     *
     * @since  v1.0
     * @return void
     */
    public function setRoute(Route $route) : void
    {

        $this->route = $route;
    }
}
