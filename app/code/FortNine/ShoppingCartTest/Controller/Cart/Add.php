<?php

declare(strict_types=1);

namespace FortNine\ShoppingCartTest\Controller\Cart;

use FortNine\ShoppingCartTest\Api\Data\ConfigInterface;
use FortNine\ShoppingCartTest\Model\CartRepository;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Quote\Api\Data\CartItemInterface;

//class Add extends Action
class Add implements HttpPostActionInterface
{
//    private const CATALOG = [
//        'HELMET-001' => ['name' => 'Trail Helmet', 'price' => 79.99],
//        'GLOVES-001' => ['name' => 'All-Terrain Gloves', 'price' => 32.50],
//        'PACK-001' => ['name' => 'Hydration Pack', 'price' => 54.25],
//        'LIGHT-001' => ['name' => 'LED Headlight', 'price' => 24.75],
//    ];

    private RequestInterface $request;

    private MessageManagerInterface $messageManager;
    private RedirectInterface $redirect;
    private ResponseInterface $response;

    public function __construct(
        Context $context,
        private readonly CartRepository $cartRepository
    ) {
        $this->request = $context->getRequest();
        $this->messageManager = $context->getMessageManager();
        $this->redirect = $context->getRedirect();
        $this->response = $context->getResponse();
    }

    public function execute()
    {
//        $sku = (string) $this->getRequest()->getParam(CartItemInterface::KEY_SKU, '');
        $sku = (string) $this->request->getParam(CartItemInterface::KEY_SKU, '');
//        $qty = max(1, (int) $this->getRequest()->getParam(CartItemInterface::KEY_QTY, 1));
        $qty = max(1, (int) $this->request->getParam(CartItemInterface::KEY_QTY, 1));

        if (isset(ConfigInterface::CATALOG[$sku])) {
            $this->cartRepository->addItem(
                $sku,
                ConfigInterface::CATALOG[$sku][CartItemInterface::KEY_NAME],
                (float) ConfigInterface::CATALOG[$sku][CartItemInterface::KEY_PRICE],
                $qty
            );
            $this->messageManager->addSuccessMessage(__(ConfigInterface::MSG_ITEM_ADD_OK));
        } else {
            $this->messageManager->addErrorMessage(__(ConfigInterface::MSG_UNKNOWN_SKU));
        }

        return $this->makeRedirect(ConfigInterface::URL_CART_INDEX);
    }

    private function makeRedirect($path, $arguments = [])
    {
//        $this->redirect->redirect($this->getResponse(), $path, $arguments);
        $this->redirect->redirect($this->response, $path, $arguments);
//        return $this->getResponse();
//
        return $this->response;
    }
}
