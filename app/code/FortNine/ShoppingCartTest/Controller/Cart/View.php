<?php

declare(strict_types=1);

namespace FortNine\ShoppingCartTest\Controller\Cart;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class View implements HttpGetActionInterface
{
    public function __construct(private readonly PageFactory $resultPageFactory)
    {
    }

    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
