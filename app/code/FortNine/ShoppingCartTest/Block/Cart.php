<?php

declare(strict_types=1);

namespace FortNine\ShoppingCartTest\Block;

use FortNine\ShoppingCartTest\Model\CartRepository;
use FortNine\ShoppingCartTest\Model\CartTotals;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Cart extends Template
{
    public function __construct(
        Context $context,
        private readonly CartRepository $cartRepository,
        private readonly CartTotals $cartTotals,
        private readonly FormKey $formKey,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return array<int, array{sku:string,name:string,price:float,qty:int,line_total:float}>
     */
    public function getItems(): array
    {
        $items = $this->cartRepository->getItems();

        foreach ($items as &$item) {
            $item['line_total'] = round($item['price'] * $item['qty'], 2);
        }
        unset($item);

        return $items;
    }

    /**
     * @return array{subtotal:float,gst:float,qst:float,grand_total:float,item_count:int}
     */
    public function getTotals(): array
    {
        return $this->cartTotals->summarize($this->cartRepository->getItems());
    }

    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
    }

    /**
     * @return array<int, array{sku:string,name:string,price:float}>
     */
    public function getCatalog(): array
    {
        return [
            ['sku' => 'HELMET-001', 'name' => 'Trail Helmet', 'price' => 79.99],
            ['sku' => 'GLOVES-001', 'name' => 'All-Terrain Gloves', 'price' => 32.50],
            ['sku' => 'PACK-001', 'name' => 'Hydration Pack', 'price' => 54.25],
            ['sku' => 'LIGHT-001', 'name' => 'LED Headlight', 'price' => 24.75],
        ];
    }

    public function formatNumber(float $number, int $decimal = 2): string
    {
        return number_format($number, $decimal);
    }
}
