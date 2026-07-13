<?php

declare(strict_types=1);

namespace FortNine\ShoppingCartTest\Test\Unit\Controller\Cart;

use FortNine\ShoppingCartTest\Api\Data\ConfigInterface;
use FortNine\ShoppingCartTest\Controller\Cart\Add;
use FortNine\ShoppingCartTest\Model\CartRepository;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AddTest extends TestCase
{
    private RequestInterface&MockObject $request;
    private ResponseInterface&MockObject $response;
    private RedirectInterface&MockObject $redirect;
    private MessageManagerInterface&MockObject $messageManager;
    private CartRepository&MockObject $cartRepository;
    private Add $controller;

    protected function setUp(): void
    {
        $this->request = $this->createMock(RequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->redirect = $this->createMock(RedirectInterface::class);
        $this->messageManager = $this->createMock(MessageManagerInterface::class);
        $this->cartRepository = $this->createMock(CartRepository::class);

        $context = new Context(
            $this->request,
            $this->response,
            $this->createMock(ObjectManagerInterface::class),
            $this->createMock(EventManagerInterface::class),
            $this->createMock(UrlInterface::class),
            $this->redirect,
            $this->createMock(ActionFlag::class),
            $this->createMock(ViewInterface::class),
            $this->messageManager,
            $this->createMock(RedirectFactory::class),
            $this->createMock(ResultFactory::class)
        );

        $this->controller = new Add($context, $this->cartRepository);
    }

    public function testExecuteAddsKnownSkuToCart(): void
    {
        $this->request->expects(self::exactly(2))
            ->method('getParam')
            ->willReturnMap([
                [CartItemInterface::KEY_SKU, '', 'HELMET-001'],
                [CartItemInterface::KEY_QTY, 1, 2],
            ]);

        $this->cartRepository->expects(self::once())
            ->method('addItem')
            ->with('HELMET-001', 'Trail Helmet', 79.99, 2);

        $this->messageManager->expects(self::once())
            ->method('addSuccessMessage');

        $this->messageManager->expects(self::never())
            ->method('addErrorMessage');

        $this->redirect->expects(self::once())
            ->method('redirect')
            ->with(
                $this->response,
                ConfigInterface::URL_CART_VIEW,
                self::callback(static function (array $arguments): bool {
                    return isset($arguments['_query']['cart_refresh'])
                        && (string) $arguments['_query']['cart_refresh'] !== '';
                })
            );

        self::assertSame($this->response, $this->controller->execute());
    }

    public function testExecuteReportsUnknownSku(): void
    {
        $this->request->expects(self::exactly(2))
            ->method('getParam')
            ->willReturnMap([
                [CartItemInterface::KEY_SKU, '', 'UNKNOWN'],
                [CartItemInterface::KEY_QTY, 1, 1],
            ]);

        $this->cartRepository->expects(self::never())->method('addItem');

        $this->messageManager->expects(self::never())
            ->method('addSuccessMessage');

        $this->messageManager->expects(self::once())
            ->method('addErrorMessage');

        $this->redirect->expects(self::once())
            ->method('redirect')
            ->with(
                $this->response,
                ConfigInterface::URL_CART_VIEW,
                self::callback(static function (array $arguments): bool {
                    return isset($arguments['_query']['cart_refresh'])
                        && (string) $arguments['_query']['cart_refresh'] !== '';
                })
            );

        self::assertSame($this->response, $this->controller->execute());
    }
}