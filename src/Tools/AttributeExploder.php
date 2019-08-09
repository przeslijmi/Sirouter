<?php declare(strict_types=1);

namespace Przeslijmi\Sirouter\Tools;

/**
 * Explodes attributes sent by HTTP headers in a string format.
 *
 * ## Usage example
 * ```
 * $attr = 'atr1=value&atr2=value&atr3=5&atr3=1&atr4=T+e+s.';
 * $attrs = AttributeExploder::explode($attr);
 * // $attrs = [
 * //     'atr1' => 'value',
 * //     'atr2' => 'value',
 * //     'atr3' => '1',            // only last value is here
 * //     'atr3[]' => [ '5', '1' ], // two values given - changed into array
 * //     'atr4' => 'T e s.',
 * // ];
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
    public static function explode(string $attributes) : array
    {

        // Lvd.
        $result = [];

        // Shortcut.
        if (empty($attributes) === true) {
            return $result;
        }

        $all = explode('&', $attributes);

        foreach ($all as $pair) {

            $pair  = explode('=', $pair);
            $name  = urldecode($pair[0]);
            $value = urldecode(( $pair[1] ?? '' ));

            // If the result with this key already exists - create an array
            // on key `name[]` - and add first element to this array - previous
            // value for key `name`.
            if (isset($result[$name]) === true) {

                // Create new array and put there previous value.
                if (isset($result[$name . '[]']) === false) {
                    $result[$name . '[]'] = [ $result[$name] ];
                }

                // Add next value to already existing array.
                $result[$name . '[]'][] = $value;
            }

            $result[$name] = $value;
        }//end foreach

        return $result;
    }
}
