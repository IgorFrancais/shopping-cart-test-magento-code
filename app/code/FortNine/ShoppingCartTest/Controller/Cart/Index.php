<?php

declare(strict_types=1);

namespace FortNine\ShoppingCartTest\Controller\Cart;

//use Magento\Framework\App\Action\Action;
//use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

//class Index extends Action implements HttpGetActionInterface
class Index implements HttpGetActionInterface
{
    public function __construct(
//        Context $context,
        private readonly PageFactory $resultPageFactory
    ) {
//        parent::__construct($context);
    }

    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
