<?php declare(strict_types=1);

namespace Przeslijmi\Sirouter;

use Przeslijmi\Sexceptions\Exceptions\ClassFopException;
use Przeslijmi\Sexceptions\Exceptions\MethodFopException;
use Przeslijmi\Sexceptions\Exceptions\ParamOtosetException;
use Przeslijmi\Sirouter\Tools\AttributeExploder;

/**
 * Route element.
 *
 * Sirouter class will handle finding and starting the route.
 *
 * ## Definition example
 * ```
 *
 * // non-parameter definition
 * Sirouter::register('/beneficiaries', 'GET')
 *     ->setCall('Namespace\Space\Resources\Class', 'get');
 * // class used to call route has to be ancestor of
 * // Przeslijmi\Sirouter\Resource
 *
 * // definition with parameter
 * Sirouter::register('/beneficiaries/(\d+)', 'GET')
 *     ->setCall('Namespace\Space\Resources\Class', 'get')
 *     ->setParam(0, 'id');
 *
 * ```
 */
class Route
{

    /**
     * URI of the Route.
     *
     * @var   string
     * @since v1.0
     */
    private $uri = '';

    /**
     * HTTP Method used by the route (default is GET).
     *
     * @var   string
     * @since v1.0
     */
    private $httpMethod = 'GET';

    /**
     * Class name to call to serve route (has to be ancestor of Przeslijmi\Sirouter\Resource).
     *
     * @var   string
     * @since v1.0
     */
    private $className = '';

    /**
     * Method name to call.
     *
     * @var   string
     * @since v1.0
     */
    private $methodName = '';

    /**
     * List of parameters inside URI.
     *
     * @var   string
     * @since v1.0
     */
    private $params = [];

    /**
     * List of attributes sent along with the call (QUERY STRING).
     *
     * @var   string
     * @since v1.0
     */
    private $attributes = [];

    /**
     * Body of the call.
     *
     * @var   \stdClass
     * @since v1.0
     */
    private $body;

    /**
     * Constructor.
     *
     * @param string $uri        URI of the Route.
     * @param string $httpMethod Opt., GET. HTTP Method used by the route.
     *
     * @since  v1.0
     * @throws ClassFopException When creation of route has failed.
     * @return self
     */
    public function __construct(string $uri, string $httpMethod = 'GET')
    {

        try {
            $this->setHttpMethod($httpMethod);
            $this->uri  = $uri;
            $this->body = json_decode(file_get_contents('php://input'));

        } catch (\Exception $e) {
            throw new ClassFopException('creationOfNewRouteFailed', $e);
        }
    }

    /**
     * Sets HTTP Method for this Route.
     *
     * @param string $httpMethod HTTP Method.
     *
     * @since  v1.0
     * @throws ParamOtosetException When HTTP Method is unknown.
     * @return self
     */
    private function setHttpMethod(string $httpMethod) : self
    {

        // Lvd.
        $httpMethod = strtoupper($httpMethod);

        // Test.
        if (in_array($httpMethod, Sirouter::ACCEPTED_HTTP_METHODS) === false) {
            throw new ParamOtosetException('httpMethod', Sirouter::ACCEPTED_HTTP_METHODS, $httpMethod);
        }

        // Set.
        $this->httpMethod = $httpMethod;

        return $this;
    }

    /**
     * Returns signature for Route, ie. URI plus Method.
     *
     * @since  v1.0
     * @return string
     */
    public function getSignature() : string
    {

        return $this->httpMethod . ':' . $this->uri;
    }

    /**
     * Sets class and method to call for this route.
     *
     * No validation is started during registration - only on call.
     *
     * @param string $className  Name of the class (has to be ancestor of Przeslijmi\Sirouter\Resource).
     * @param string $methodName Name of the method inside class.
     *
     * @since  v1.0
     * @return self
     */
    public function setCall(string $className, string $methodName) : self
    {

        $this->className  = $className;
        $this->methodName = $methodName;

        return $this;
    }

    /**
     * Returns name of the class to call in this Route.
     *
     * Class has to be ancestor of Przeslijmi\Sirouter\Resource.
     *
     * @since  v1.0
     * @return string
     */
    public function getClassName() : string
    {

        return $this->className;
    }

    /**
     * Returns HTTP method for this Route.
     *
     * @since  v1.0
     * @return string
     */
    public function getHttpMethod() : string
    {

        return $this->httpMethod;
    }

    /**
     * Returns name of the method to call in this Route.
     *
     * @since  v1.0
     * @return string
     */
    public function getMethodName() : string
    {

        return $this->methodName;
    }

    /**
     * Sets param (at least defines with order and name without value).
     *
     * @param integer     $order Order number when reading URI with regex commands (starting from 0).
     * @param string      $name  Name of the param to use in Resources.
     * @param string|null $value Opt., null. Value of the param.
     *
     * @since  v1.0
     * @return self
     */
    public function setParam(int $order, string $name, ?string $value = null) : self
    {

        $this->params[$order] = [
            'name'  => $name,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Gets REGEX result from `preg_match_all` and saves values of all params.
     *
     * ## Param `$values` example
     *
     * If there are two parameters defined:
     * ROUTE: `/test/(\d+)/sth/(\d+)`
     * URI: `/test/12/sth/333`
     * PARAM 0 => 'first_id'
     * PARAM 1 => 'second_id'
     *
     * Then `$values` will be:
     * ```
     * [
     *     0 => [
     *         0 => 12,
     *     ],
     *     1 => [
     *         0 => 333,
     *     ],
     * ]
     * ```
     *
     * @param array $values Result from `preg_match_all`.
     *
     * @since  v1.0
     * @return self
     */
    public function setParamsValuesFromRegex(array $values) : self
    {

        foreach ($values as $order => $valueInDeep) {
            $this->params[$order]['value'] = $valueInDeep[0];
        }

        return $this;
    }

    /**
     * Return all parameters.
     *
     * @return array
     */
    public function getParams() : array
    {

        return $this->params;
    }

    /**
     * Return names of all parameters.
     *
     * @since  v1.0
     * @return array
     */
    public function getParamsNames() : array
    {

        // Lvd.
        $result = [];

        foreach ($this->params as $order => $param) {
            $result[$order] = $param['name'];
        }

        return $result;
    }

    /**
     * Get value of a Param.
     *
     * @param string $name Name of the param.
     *
     * @since  v1.0
     * @throws ParamOtosetException On routeParameterByName.
     * @throws MethodFopException On getNonexistingParameterForRoute.
     * @return null|string Param's value.
     */
    public function getParam(string $name, bool $throw = true) : ?string
    {

        foreach ($this->params as $order => $param) {
            if ($param['name'] === $name) {
                return $param['value'];
            }
        }

        if ($throw === false) {
            return null;
        }

        try {
            throw new ParamOtosetException('routeParameterByName', $this->getParamsNames(), $name);
        } catch (ParamOtosetException $e) {
            throw new MethodFopException('getNonexistingParameterForRoute', $e);
        }
    }

    /**
     * Set attribute name and value.
     *
     * @param string $name  Name of the attribute.
     * @param string $value Value of the attribute.
     *
     * @since  v1.0
     * @return self
     */
    public function setAttribute(string $name, string $value) : self
    {

        $this->attributes[$name] = [
            'name'  => $name,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Get Values of all Attributes.
     *
     * @return array
     */
    public function getAttributes() : array
    {

        return $this->attributes;
    }

    /**
     * Get Value of an Attribute.
     *
     * @param string $name Name of the Attribute.
     *
     * @since  v1.0
     * @throws ParamOtosetException On routeAttributeByName.
     * @throws MethodFopException On getNonexistingAttributeForRoute.
     * @return string Attributes's value.
     */
    public function getAttribute(string $name) : string
    {

        if (isset($this->attributes[$name]) === true) {
            return $this->attributes[$name]['value'];
        }

        try {
            throw new ParamOtosetException('routeAttributeByName', array_keys($this->attributes), $name);
        } catch (ParamOtosetException $e) {
            throw new MethodFopException('getNonexistingAttributeForRoute', $e);
        }
    }

    /**
     * Get Value of an Attribute if given attribute exists.
     *
     * @param string $name Name of the Attribute.
     *
     * @since  v1.0
     * @return string|null Attributes's value or null.
     */
    public function getAttributeIfExists(string $name) : ?string
    {

        if (isset($this->attributes[$name]) === true) {
            return $this->attributes[$name]['value'];
        }

        return null;
    }

    public function getAttributeOrDefault(string $name, array $enum, $default = null)
    {

        $attr = ( $this->attributes[$name]['value'] ?? null );

        return ( array_combine($enum, $enum)[$attr] ?? $default );
    }

    /**
     * Gets HTTP QUERY STRING saves values of all Attributes.
     *
     * ## Param `$values` example=
     * ```
     * \\ aaa=value1&bbb=value2
     * [
     *     'aaa' => 'value1',
     *     'aaa' => 'value2',
     * ]
     * ```
     *
     * @param string $values HTTP QUERY STRING.
     *
     * @since  v1.0
     * @return self
     */
    public function setAttributesFromString(string $values) : self
    {

        // Shortcut.
        if (empty($values) === true) {
            return $this;
        }

        // Explode.
        $attributes = AttributeExploder::explode($values);

        // Save.
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }

        return $this;
    }

    public function setAttributesFromArray(array $attributes) : self
    {

        // Save.
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }

        return $this;
    }

    /**
     * Return JSON body of the call.
     *
     * @since  v1.0
     * @return \stdClass
     */
    public function getBody() : ?\stdClass
    {

        return $this->body;
    }
}
