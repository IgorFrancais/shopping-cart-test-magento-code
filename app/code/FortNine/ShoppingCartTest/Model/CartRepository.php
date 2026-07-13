<?php

declare(strict_types=1);

namespace FortNine\ShoppingCartTest\Model;

use FortNine\ShoppingCartTest\Api\Data\ConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Form\FormKey;

class CartRepository
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly FormKey $formKey
    ) {
    }

    /**
     * @return array<int, array{sku:string,name:string,price:float,qty:int}>
     */
    public function getItems(): array
    {
        return $this->formatItems($this->getAllItems());
    }

    public function addItem(string $sku, string $name, float $price, int $qty): void
    {
        if ($qty >= 1) {
            $connection = $this->resourceConnection->getConnection();
            $table = $this->resourceConnection->getTableName(ConfigInterface::TABLE);
            $sessionId = $this->getSessionId();

            $existing = $connection->fetchRow(
                $connection->select()
                    ->from($table, ['entity_id', 'qty'])
                    ->where('session_id = ?', $sessionId)
                    ->where('sku = ?', $sku)
            );

            if ($existing) {
                $connection->update(
                    $table,
                    ['qty' => ((int) $existing['qty']) + $qty],
                    ['entity_id = ?' => (int) $existing['entity_id']]
                );
            } else {
                $connection->insert($table, [
                    'session_id' => $sessionId,
                    'sku' => $sku,
                    'name' => $name,
                    'price' => $price,
                    'qty' => $qty,
                ]);
            }
        }
    }

    public function updateQty(string $sku, int $qty): void
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName(ConfigInterface::TABLE);
        $where = [
            'session_id = ?' => $this->getSessionId(),
            'sku = ?' => $sku,
        ];

        if ($qty <= 0) {
            $connection->delete($table, $where);
        } else {
            $connection->update($table, ['qty' => $qty], $where);
        }
    }

    public function clear(): void
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName(ConfigInterface::TABLE);
        $connection->delete($table, ['session_id = ?' => $this->getSessionId()]);
    }

    private function getSessionId(): string
    {
        return (string) $this->formKey->getFormKey();
    }

    private function getAllItems(): array
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName(ConfigInterface::TABLE);

        $select = $connection->select()
            ->from($table, ['sku', 'name', 'price', 'qty'])
            ->where('session_id = ?', $this->getSessionId())
            ->order('entity_id ASC');

        return $connection->fetchAll($select);
    }

    private function formatItems(array $items): array
    {
        return array_map(static function (array $row): array {
            return [
                'sku' => (string) $row['sku'],
                'name' => (string) $row['name'],
                'price' => (float) $row['price'],
                'qty' => (int) $row['qty'],
            ];
        }, $items);
    }
}
