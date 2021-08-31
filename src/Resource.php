<?php declare(strict_types=1);

namespace Przeslijmi\Sirouter;

use Przeslijmi\Sexceptions\Exceptions\TypeHintingFailException;
use Przeslijmi\Sexceptions\Exceptions\MethodFopException;

/**
 * Parent for all Restful answering classes (ie. Resources).
 *
 * Resource classes should have *Rsrc as suffix of class name.
 *
 * Resource class is a class called to serve HTTP RestFULL commands GET, POST, etc.
 */
abstract class Resource
{

    /**
     * Route that has been called.
     *
     * @var   Route
     * @since v1.0
     */
    protected $route;

    protected $constructionFailed = false;

    protected $response = [
        'status' => 'success',
        'data' => [
            'errors' => [],
        ],
    ];

    protected $code;

    /**
     * Sends JSON response to the client.
     *
     * @param scalar|array|stdClass $response Response to be showed as JSON.
     * @param integer               $code     Opt., 200. HTTP Response code.
     *
     * @since  v1.0
     * @throws TypeHintingFailException On scalarArrayOrStdClass.
     * @throws MethodFopException On routeCanNotSendResponseBcsResponseIsWrotype.
     * @return void
     */
    protected function sendJson($response, int $code = 200) : void
    {

        // Save code.
        $this->code = $code;

        // Set headers.
        http_response_code($code);
        header('Content-type: application/json; charset=utf-8');

        if ($response === null) {
            return;
        }

        // Reformat $response to encode it to JSON.
        $isArray    = ( is_array($response) );
        $isScalar   = ( is_scalar($response) );
        $isStdClass = ( is_object($response) && is_a($response, 'stdClass') );

        if ($isArray === false && $isScalar === true) {
            $response = [ $response ];
            $isArray  = true;
        }

        // Response is WroType - throw.
        if ($isArray !== true && $isStdClass !== true) {
            try {
                throw new TypeHintingFailException('scalarArrayOrStdClass', get_class($response));
            } catch (TypeHintingFailException $e) {
                throw new MethodFopException('routeCanNotSendResponseBcsResponseIsWrotype', $e);
            }
        }

        echo json_encode($response, ( JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE ) );
    }

    /**
     * Sends TXT response to the client.
     *
     * @param string  $response Response to be showed as TXT.
     * @param integer $code     Opt., 200. HTTP Response code.
     *
     * @since  v1.2
     * @return void
     */
    public function sendTxt(string $response, int $code = 200) : void
    {

        // Set headers.
        http_response_code($code);
        header('Content-type: text/plain; charset=utf-8');

        echo $response;
    }

    /**
     * Sends HTML response to the client.
     *
     * @param string  $response Response to be showed as HTML.
     * @param integer $code     Opt., 200. HTTP Response code.
     *
     * @since  v1.2
     * @return void
     */
    public function sendHtml(string $response, int $code = 200) : void
    {

        // Set headers.
        http_response_code($code);
        header('Content-type: text/html; charset=utf-8');

        echo $response;
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

    public function getResponse() : array
    {

        return $this->response;
    }

    public function getConstructionFailed() : bool
    {

        return $this->constructionFailed;
    }
}
