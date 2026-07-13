<?php

declare(strict_types=1);

namespace FortNine\ShoppingCartTest\Test\Unit\Model;

use FortNine\ShoppingCartTest\Model\CartTotals;
use FortNine\ShoppingCartTest\Model\TaxCalculator;
use PHPUnit\Framework\TestCase;

class CartTotalsTest extends TestCase
{
    public function testSummarizeForItems(): void
    {
        $service = new CartTotals(new TaxCalculator());

        $summary = $service->summarize([
            ['sku' => 'HELMET-001', 'name' => 'Trail Helmet', 'price' => 79.99, 'qty' => 2],
            ['sku' => 'PACK-001', 'name' => 'Hydration Pack', 'price' => 54.25, 'qty' => 1],
        ]);

        self::assertSame(214.23, $summary['subtotal']);
        self::assertSame(10.71, $summary['gst']);
        self::assertSame(21.37, $summary['qst']);
        self::assertSame(246.31, $summary['grand_total']);
        self::assertSame(3, $summary['item_count']);
    }

    public function testSummarizeForEmptyCart(): void
    {
        $service = new CartTotals(new TaxCalculator());

        $summary = $service->summarize([]);

        self::assertSame(0.0, $summary['subtotal']);
        self::assertSame(0.0, $summary['gst']);
        self::assertSame(0.0, $summary['qst']);
        self::assertSame(0.0, $summary['grand_total']);
        self::assertSame(0, $summary['item_count']);
    }
}
