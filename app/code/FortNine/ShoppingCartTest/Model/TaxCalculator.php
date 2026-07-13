<?php

declare(strict_types=1);

namespace FortNine\ShoppingCartTest\Model;

use FortNine\ShoppingCartTest\Api\Data\ConfigInterface;
class TaxCalculator
{
    /**
     * @return array{gst:float,qst:float,total:float}
     */
    public function calculate(float $subtotal): array
    {
        $subtotal = max(0.0, $subtotal);
        $gst = $this->getGst($subtotal);
        $qst = $this->getQst($subtotal);;

        return [
            'gst' => $gst,
            'qst' => $qst,
            'total' => $this->getTotal($subtotal, $gst, $qst)
        ];
    }

    private function getGst(float $subtotal): float
    {
        return round($subtotal * ConfigInterface::GST_RATE, 2);
    }

    private function getQst(float $subtotal): float
    {
        return round($subtotal * ConfigInterface::QST_RATE, 2);
    }

    private function getTotal(float $subtotal, float $gst, float $qst): float
    {
        return round($subtotal + $gst + $qst, 2);
    }
}
