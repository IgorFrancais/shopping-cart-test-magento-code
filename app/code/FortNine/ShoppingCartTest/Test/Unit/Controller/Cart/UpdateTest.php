<?php

declare(strict_types=1);

namespace FortNine\ShoppingCartTest\Test\Unit\Controller\Cart;

use FortNine\ShoppingCartTest\Api\Data\ConfigInterface;
use FortNine\ShoppingCartTest\Controller\Cart\Update;
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

class UpdateTest extends TestCase
{
    private RequestInterface&MockObject $request;
    private ResponseInterface&MockObject $response;
    private RedirectInterface&MockObject $redirect;
    private MessageManagerInterface&MockObject $messageManager;
    private CartRepository&MockObject $cartRepository;
    private Update $controller;

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

        $this->controller = new Update($context, $this->cartRepository);
    }

    public function testExecuteClearsCartWhenRequested(): void
    {
        $this->request->expects(self::once())
            ->method('getParam')
            ->with('cart_action', 'update')
            ->willReturn('clear');

        $this->cartRepository->expects(self::once())->method('clear');
        $this->cartRepository->expects(self::never())->method('updateQty');

        $this->messageManager->expects(self::once())
            ->method('addSuccessMessage');

        $this->redirect->expects(self::once())
            ->method('redirect')
            ->with($this->response, ConfigInterface::URL_CART_VIEW, []);

        self::assertSame($this->response, $this->controller->execute());
    }

    public function testExecuteUpdatesQuantities(): void
    {
        $calls = [];

        $this->request->expects(self::exactly(2))
            ->method('getParam')
            ->willReturnMap([
                ['cart_action', 'update', 'update'],
                [CartItemInterface::KEY_QTY, [], ['HELMET-001' => 4, 'PACK-001' => 1]],
            ]);

        $this->cartRepository->expects(self::exactly(2))
            ->method('updateQty')
            ->willReturnCallback(static function (string $sku, int $qty) use (&$calls): void {
                $calls[] = [$sku, $qty];
            });

        $this->cartRepository->expects(self::never())->method('clear');

        $this->messageManager->expects(self::once())
            ->method('addSuccessMessage');

        $this->redirect->expects(self::once())
            ->method('redirect')
            ->with($this->response, ConfigInterface::URL_CART_VIEW, []);

        self::assertSame($this->response, $this->controller->execute());
        self::assertSame([
            ['HELMET-001', 4],
            ['PACK-001', 1],
        ], $calls);
    }
}