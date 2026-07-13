<?php

declare(strict_types=1);

namespace FortNine\ShoppingCartTest\Model;

class CartTotals
{
    public function __construct(private readonly TaxCalculator $taxCalculator)
    {
    }

    /**
     * @param array<int, array{sku:string,name:string,price:float,qty:int}> $items
     * @return array{subtotal:float,gst:float,qst:float,grand_total:float,item_count:int}
     */
    public function summarize(array $items): array
    {
        $subtotal = 0.0;
        $itemCount = 0;

        foreach ($items as $item) {
            $subtotal += ((float) $item['price']) * ((int) $item['qty']);
            $itemCount += (int) $item['qty'];
        }

        $subtotal = round($subtotal, 2);
        $taxes = $this->taxCalculator->calculate($subtotal);

        return [
            'subtotal' => $subtotal,
            'gst' => $taxes['gst'],
            'qst' => $taxes['qst'],
            'grand_total' => $taxes['total'],
            'item_count' => $itemCount,
        ];
    }
}
