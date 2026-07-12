<?php

declare(strict_types=1);

namespace FortNine\ShoppingCartTest\Model;

use FortNine\ShoppingCartTest\Api\Data\ConfigInterface;
class TaxCalculator
{
//    private const GST_RATE = 0.05;
//    private const QST_RATE = 0.09975;

    /**
     * @return array{gst:float,qst:float,total:float}
     */
    public function calculate(float $subtotal): array
    {
        $subtotal = max(0.0, $subtotal);
//        $gst = round($subtotal * self::GST_RATE, 2);
        $gst = $this->getGst($subtotal);
//        $qst = round($subtotal * self::QST_RATE, 2);
        $qst = $this->getQst($subtotal);;
//        $total = round($subtotal + $gst + $qst, 2);
//        $total = $this->getTotal($subtotal, $gst, $qst);

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
