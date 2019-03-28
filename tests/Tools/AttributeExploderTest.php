<?php declare(strict_types=1);

namespace Przeslijmi\Sivalidator;

use PHPUnit\Framework\TestCase;
use Przeslijmi\Sirouter\Tools\AttributeExploder;

/**
 * Methods for testing attributes explorer class.
 */
final class AttributeExploderTest extends TestCase
{

    /**
     * Test if "isArrayOf" returns true on positive values.
     *
     * @return void
     */
    public function testIfExplodesProper1() : void
    {

        $attr          = 'atr1=value&atr2=value&atr3=5&atr3=1&atr4=T+e+s.&atr5&atr6';
        $attrsExpected = [
            'atr1'   => 'value',
            'atr2'   => 'value',
            'atr3'   => '1',
            'atr3[]' => [ '5', '1' ],
            'atr4'   => 'T e s.',
            'atr5'   => '',
            'atr6'   => '',
        ];

        $this->assertEquals($attrsExpected, AttributeExploder::explode($attr));
    }

    /**
     * Test if "isArrayOf" returns true on positive values.
     *
     * @return void
     */
    public function testIfExplodesProper2() : void
    {

        $attr          = '';
        $attrsExpected = [];

        $this->assertEquals($attrsExpected, AttributeExploder::explode($attr));
    }
}
