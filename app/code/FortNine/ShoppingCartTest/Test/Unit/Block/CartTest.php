<?php

declare(strict_types=1);

namespace FortNine\ShoppingCartTest\Test\Unit\Block;

use FortNine\ShoppingCartTest\Block\Cart;
use FortNine\ShoppingCartTest\Model\CartRepository;
use FortNine\ShoppingCartTest\Model\CartTotals;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template\Context;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    private CartRepository&MockObject $cartRepository;
    private CartTotals&MockObject $cartTotals;
    private FormKey&MockObject $formKey;
    private Cart $block;

    protected function setUp(): void
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cartRepository = $this->createMock(CartRepository::class);
        $this->cartTotals = $this->createMock(CartTotals::class);
        $this->formKey = $this->createMock(FormKey::class);

        $this->block = new Cart($context, $this->cartRepository, $this->cartTotals, $this->formKey);
    }

    public function testGetItemsAddsLineTotals(): void
    {
        $this->cartRepository->expects(self::once())
            ->method('getItems')
            ->willReturn([
                ['sku' => 'HELMET-001', 'name' => 'Trail Helmet', 'price' => 79.99, 'qty' => 2],
                ['sku' => 'PACK-001', 'name' => 'Hydration Pack', 'price' => 54.25, 'qty' => 1],
            ]);

        self::assertSame([
            ['sku' => 'HELMET-001', 'name' => 'Trail Helmet', 'price' => 79.99, 'qty' => 2, 'line_total' => 159.98],
            ['sku' => 'PACK-001', 'name' => 'Hydration Pack', 'price' => 54.25, 'qty' => 1, 'line_total' => 54.25],
        ], $this->block->getItems());
    }

    public function testGetTotalsUsesRepositoryItems(): void
    {
        $items = [
            ['sku' => 'HELMET-001', 'name' => 'Trail Helmet', 'price' => 79.99, 'qty' => 2],
        ];

        $summary = [
            'subtotal' => 159.98,
            'gst' => 8.0,
            'qst' => 15.96,
            'grand_total' => 183.94,
            'item_count' => 2,
        ];

        $this->cartRepository->expects(self::once())
            ->method('getItems')
            ->willReturn($items);

        $this->cartTotals->expects(self::once())
            ->method('summarize')
            ->with($items)
            ->willReturn($summary);

        self::assertSame($summary, $this->block->getTotals());
    }

    public function testGetFormKeyReturnsKey(): void
    {
        $this->formKey->expects(self::once())
            ->method('getFormKey')
            ->willReturn('form-key');

        self::assertSame('form-key', $this->block->getFormKey());
    }

    public function testGetCatalogReturnsAllProducts(): void
    {
        $catalog = $this->block->getCatalog();

        self::assertCount(4, $catalog);
        self::assertSame('HELMET-001', $catalog[0]['sku']);
        self::assertSame('Trail Helmet', $catalog[0]['name']);
    }

    public function testFormatNumberFormatsWithPrecision(): void
    {
        self::assertSame('12.35', $this->block->formatNumber(12.3456));
        self::assertSame('12.346', $this->block->formatNumber(12.3456, 3));
    }
}