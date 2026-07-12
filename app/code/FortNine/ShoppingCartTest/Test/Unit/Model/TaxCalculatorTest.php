<?php

declare(strict_types=1);

namespace FortNine\ShoppingCartTest\Test\Unit\Model;

use FortNine\ShoppingCartTest\Model\TaxCalculator;
use PHPUnit\Framework\TestCase;

class TaxCalculatorTest extends TestCase
{
    public function testCalculateForPositiveSubtotal(): void
    {
        $calculator = new TaxCalculator();
        $result = $calculator->calculate(100.00);

        self::assertSame(5.0, $result['gst']);
        self::assertSame(9.98, $result['qst']);
        self::assertSame(114.98, $result['total']);
    }

    public function testCalculateForNegativeSubtotal(): void
    {
        $calculator = new TaxCalculator();
        $result = $calculator->calculate(-1.00);

        self::assertSame(0.0, $result['gst']);
        self::assertSame(0.0, $result['qst']);
        self::assertSame(0.0, $result['total']);
    }
}
