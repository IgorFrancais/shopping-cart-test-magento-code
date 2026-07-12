<?php

declare(strict_types=1);

namespace FortNine\ShoppingCartTest\Controller\Cart;

use FortNine\ShoppingCartTest\Api\Data\ConfigInterface;
use FortNine\ShoppingCartTest\Model\CartRepository;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Quote\Api\Data\CartItemInterface;

class Update implements HttpPostActionInterface
{
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
        $action = (string) $this->request->getParam('cart_action', 'update');

        if ($action === 'clear') {
            $this->cartRepository->clear();
            $this->messageManager->addSuccessMessage(__(ConfigInterface::MSG_CART_CLEARED));
            return $this->makeRedirect(ConfigInterface::URL_CART_INDEX);
        }

        $quantities = (array) $this->request->getParam(CartItemInterface::KEY_QTY, []);
        foreach ($quantities as $sku => $qty) {
            $this->cartRepository->updateQty((string) $sku, (int) $qty);
        }

        $this->messageManager->addSuccessMessage(__(ConfigInterface::MSG_CART_UPDATED));

        return $this->makeRedirect(ConfigInterface::URL_CART_INDEX);
    }

    private function makeRedirect(string $path, array $arguments = []): ResponseInterface
    {
        $this->redirect->redirect($this->response, $path, $arguments);

        return $this->response;
    }
}
