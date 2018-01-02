<?php

namespace Tests\Money;

use Money\Number;

final class NumberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider numberExamples
     * @test
     */
    public function it_has_attributes($number, $decimal, $half, $currentEven, $negative, $integerPart, $fractionalPart)
    {
        $number = Number::fromString($number);

        $this->assertSame($decimal, $number->isDecimal());
        $this->assertSame($half, $number->isHalf());
        $this->assertSame($currentEven, $number->isCurrentEven());
        $this->assertSame($negative, $number->isNegative());
        $this->assertSame($integerPart, $number->getIntegerPart());
        $this->assertSame($fractionalPart, $number->getFractionalPart());
        $this->assertSame($negative ? '-1' : '1', $number->getIntegerRoundingMultiplier());
    }

    public function numberExamples()
    {
        return [
            ['0', false, false, true, false, '0', ''],
            ['0.00', false, false, true, false, '0', ''],
            ['0.5', true, true, true, false, '0', '5'],
            ['0.500', true, true, true, false, '0', '5'],
            ['-0', false, false, true, true, '-0', ''],
            ['-0.5', true, true, true, true, '-0', '5'],
            ['3', false, false, false, false, '3', ''],
            ['3.00', false, false, false, false, '3', ''],
            ['3.5', true, true, false, false, '3', '5'],
            ['3.500', true, true, false, false, '3', '5'],
            ['-3', false, false, false, true, '-3', ''],
            ['-3.5', true, true, false, true, '-3', '5'],
            ['10', false, false, true, false, '10', ''],
            ['10.00', false, false, true, false, '10', ''],
            ['10.5', true, true, true, false, '10', '5'],
            ['10.500', true, true, true, false, '10', '5'],
            ['10.9', true, false, true, false, '10', '9'],
            ['-10', false, false, true, true, '-10', ''],
            ['-0', false, false, true, true, '-0', ''],
            ['-10.5', true, true, true, true, '-10', '5'],
            ['-.5', true, true, true, true, '-0', '5'],
            ['.5', true, true, true, false, '0', '5'],
            [(string) PHP_INT_MAX, false, false, false, false, (string) PHP_INT_MAX, ''],
            [(string) -PHP_INT_MAX, false, false, false, true, (string) -PHP_INT_MAX, ''],
            [
                PHP_INT_MAX.PHP_INT_MAX.PHP_INT_MAX,
                false,
                false,
                false,
                false,
                PHP_INT_MAX.PHP_INT_MAX.PHP_INT_MAX,
                '',
            ],
            [
                -PHP_INT_MAX.PHP_INT_MAX.PHP_INT_MAX,
                false,
                false,
                false,
                true,
                -PHP_INT_MAX.PHP_INT_MAX.PHP_INT_MAX,
                '',
            ],
            [
                substr(PHP_INT_MAX, 0, strlen((string) PHP_INT_MAX) - 1).str_repeat('0', strlen((string) PHP_INT_MAX) - 1).PHP_INT_MAX,
                false,
                false,
                false,
                false,
                substr(PHP_INT_MAX, 0, strlen((string) PHP_INT_MAX) - 1).str_repeat('0', strlen((string) PHP_INT_MAX) - 1).PHP_INT_MAX,
                '',
            ],
        ];
    }

    /**
     * @dataProvider invalidNumberExamples
     * @expectedException \InvalidArgumentException
     * @test
     */
    public function it_fails_parsing_invalid_numbers($number)
    {
        Number::fromString($number);
    }

    /**
     * @dataProvider base10Examples
     * @test
     */
    public function testBase10($numberString, $baseNumber, $expectedResult)
    {
        $number = Number::fromString($numberString);
        $this->assertSame($expectedResult, (string) $number->base10($baseNumber));
    }

    public function invalidNumberExamples()
    {
        return [
            [''],
            ['000'],
            ['005'],
            ['123456789012345678-123456'],
            ['---123'],
            ['123456789012345678+13456'],
            ['-123456789012345678.-13456'],
            ['+123456789'],
            ['+123456789012345678.+13456'],
        ];
    }

    public function base10Examples()
    {
        return [
            ['0', 10, '0'],
            ['5', 1, '0.5'],
            ['50', 2, '0.5'],
            ['50', 3, '0.05'],
            ['0.5', 2, '0.005'],
            ['500', 2, '5'],
            ['500', 0, '500'],
            ['500', -2, '50000'],
            ['0.5', -2, '50'],
            ['0.5', -3, '500'],
        ];
    }
}
