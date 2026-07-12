<?php

declare(strict_types=1);

namespace FortNine\ShoppingCartTest\Test\Unit\Controller\Cart;

use FortNine\ShoppingCartTest\Controller\Cart\Index;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    public function testExecuteCreatesPageResult(): void
    {
        $pageFactory = $this->createMock(PageFactory::class);
        $page = $this->createMock(Page::class);

        $pageFactory->expects(self::once())
            ->method('create')
            ->willReturn($page);

        $controller = new Index($pageFactory);

        self::assertSame($page, $controller->execute());
    }
}