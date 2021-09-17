<?php declare(strict_types=1);

namespace Przeslijmi\Sirouter\Helpers;

use Przeslijmi\Sirouter\Resource;
use Przeslijmi\Sirouter\Sirouter;

/**
 * Fictional resource for testing purposes.
 */
class ExampleRsrc extends Resource
{

    /**
     * Answers for GET HTTP RESTful call.
     *
     * @return void
     */
    public function get() : void
    {

        // Create response.
        $response = [
            'msg' => 'This is answer for GET test call!',
        ];

        $this->sendJson($response);
    }

    /**
     * Answers for PUT HTTP RESTful call with params.
     *
     * @return void
     */
    public function put()
    {

        $response = [
            'msg' => 'This is answer for GET test call!',
            'params' => [
                'testParam1' => $this->route->getParam('testParam1'),
                'testParam2' => $this->route->getParam('testParam2'),
            ],
        ];

        $this->sendJson($response);
    }

    /**
     * Send `true` as a response.
     *
     * @return void
     */
    public function getScalar() : void
    {

        // Create response.
        $response = true;

        $this->sendJson($response);
    }

    /**
     * Send wrong type of answer as a response.
     *
     * @return void
     */
    public function getWrotype() : void
    {

        // Create response.
        $response = new Sirouter();

        $this->sendJson($response);
    }

    /**
     * Send text answer as a response.
     *
     * @return void
     */
    public function getTxt() : void
    {

        // Create response.
        $response = 'OK';

        $this->sendTxt($response);
    }
}
