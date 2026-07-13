<?php

declare(strict_types=1);

namespace FortNine\ShoppingCartTest\Test\Unit\Model;

use FortNine\ShoppingCartTest\Api\Data\ConfigInterface;
use FortNine\ShoppingCartTest\Model\CartRepository;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\DB\Adapter\AdapterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CartRepositoryTest extends TestCase
{
    private ResourceConnection&MockObject $resourceConnection;
    private FormKey&MockObject $formKey;
    private AdapterInterface&MockObject $connection;
    private CartRepository $repository;

    protected function setUp(): void
    {
        $this->resourceConnection = $this->createMock(ResourceConnection::class);
        $this->formKey = $this->createMock(FormKey::class);
        $this->connection = $this->createMock(AdapterInterface::class);

        $this->resourceConnection->method('getConnection')->willReturn($this->connection);
        $this->resourceConnection->method('getTableName')->with(ConfigInterface::TABLE)->willReturn('fortnine_cart_item');
        $this->formKey->method('getFormKey')->willReturn('form-key-1');

        $this->repository = new CartRepository($this->resourceConnection, $this->formKey);
    }

    public function testGetItemsFormatsDatabaseRows(): void
    {
        $select = $this->createSelectStub();

        $this->connection->expects(self::once())
            ->method('select')
            ->willReturn($select);

        $this->connection->expects(self::once())
            ->method('fetchAll')
            ->with(self::identicalTo($select))
            ->willReturn([
                ['sku' => 'HELMET-001', 'name' => 'Trail Helmet', 'price' => '79.99', 'qty' => '2'],
                ['sku' => 'PACK-001', 'name' => 'Hydration Pack', 'price' => '54.25', 'qty' => '1'],
            ]);

        self::assertSame([
            ['sku' => 'HELMET-001', 'name' => 'Trail Helmet', 'price' => 79.99, 'qty' => 2],
            ['sku' => 'PACK-001', 'name' => 'Hydration Pack', 'price' => 54.25, 'qty' => 1],
        ], $this->repository->getItems());
    }

    public function testAddItemIgnoresNonPositiveQuantity(): void
    {
        $this->connection->expects(self::never())->method('fetchRow');
        $this->connection->expects(self::never())->method('insert');
        $this->connection->expects(self::never())->method('update');

        $this->repository->addItem('HELMET-001', 'Trail Helmet', 79.99, 0);
    }

    public function testAddItemUpdatesExistingRow(): void
    {
        $select = $this->createSelectStub();

        $this->connection->expects(self::once())
            ->method('select')
            ->willReturn($select);

        $this->connection->expects(self::once())
            ->method('fetchRow')
            ->with(self::identicalTo($select))
            ->willReturn(['entity_id' => 10, 'qty' => 2]);

        $this->connection->expects(self::once())
            ->method('update')
            ->with(
                'fortnine_cart_item',
                ['qty' => 5],
                ['entity_id = ?' => 10]
            );

        $this->connection->expects(self::never())->method('insert');

        $this->repository->addItem('HELMET-001', 'Trail Helmet', 79.99, 3);
    }

    public function testAddItemInsertsNewRow(): void
    {
        $select = $this->createSelectStub();

        $this->connection->expects(self::once())
            ->method('select')
            ->willReturn($select);

        $this->connection->expects(self::once())
            ->method('fetchRow')
            ->with(self::identicalTo($select))
            ->willReturn(false);

        $this->connection->expects(self::once())
            ->method('insert')
            ->with('fortnine_cart_item', [
                'session_id' => 'form-key-1',
                'sku' => 'PACK-001',
                'name' => 'Hydration Pack',
                'price' => 54.25,
                'qty' => 1,
            ]);

        $this->connection->expects(self::never())->method('update');

        $this->repository->addItem('PACK-001', 'Hydration Pack', 54.25, 1);
    }

    public function testUpdateQtyDeletesWhenQuantityIsNotPositive(): void
    {
        $this->connection->expects(self::once())
            ->method('delete')
            ->with('fortnine_cart_item', [
                'session_id = ?' => 'form-key-1',
                'sku = ?' => 'PACK-001',
            ]);

        $this->connection->expects(self::never())->method('update');

        $this->repository->updateQty('PACK-001', 0);
    }

    public function testUpdateQtyUpdatesWhenQuantityIsPositive(): void
    {
        $this->connection->expects(self::once())
            ->method('update')
            ->with('fortnine_cart_item', ['qty' => 4], [
                'session_id = ?' => 'form-key-1',
                'sku = ?' => 'HELMET-001',
            ]);

        $this->connection->expects(self::never())->method('delete');

        $this->repository->updateQty('HELMET-001', 4);
    }

    public function testClearDeletesSessionItems(): void
    {
        $this->connection->expects(self::once())
            ->method('delete')
            ->with('fortnine_cart_item', ['session_id = ?' => 'form-key-1']);

        $this->repository->clear();
    }

    private function createSelectStub(): object
    {
        return new class {
            public function from(string $table, array $columns): self
            {
                return $this;
            }

            public function where(string $condition, mixed $value): self
            {
                return $this;
            }

            public function order(string $order): self
            {
                return $this;
            }
        };
    }
}