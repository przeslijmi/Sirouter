<?php

namespace Przeslijmi\Sirouter\Tools;

/**
 * Explodes attributes sent by HTTP headers in a string format.
 *
 * ## Usage example
 * ```
 * $attr = 'atr1=value&atr2=value'
 * $attrs = AttributeExploder::explode($attr);
 * // attrs:
 * // [
 * //     'attr1' => 'value',
 * //     'attr2' => 'value',
 * // ]
 * ```
 */
class AttributeExploder
{

    /**
     * Explodes attributes string into array.
     *
     * @param string $attributes Attributes string from HTTP query.
     *
     * @since  v1.0
     * @return array
     */
    public static function explode(string $attributes)
    {

        // lvd
        $result = [];

        // shortcut
        if (empty($attributes) === true) {
            return $result;
        }

        $all = explode('&', $attributes);

        foreach ($all as $pair) {

            $pair = explode('=', $pair);

            $name = urldecode($pair[0]);
            $value = urldecode(($pair[1] ?? ''));

            $result[$name] = $value;
        }

        return $result;
    }
}
