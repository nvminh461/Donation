<?php

namespace Dev\Donation\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements DataPatchInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private $setup;

    /**
     * InstallSchema constructor.
     * @param SchemaSetupInterface $setup
     */
    public function __construct(
        SchemaSetupInterface $setup
    ) {
        $this->setup = $setup;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->setup->startSetup();
        $connection = $this->setup->getConnection();

        $columnNames = ['quote', 'sales_order', 'sales_invoice'];
        $columnName = 'donation_amount';
        $columnDefinition = [
            'type' => Table::TYPE_DECIMAL,
            'length' => '12,4',
            'nullable' => false,
            'default' => '0.0000',
            'comment' => 'Donation amount'
        ];

        foreach ($columnNames as $tableName) {
            if (!$connection->tableColumnExists($this->setup->getTable($tableName), $columnName)) {
                $connection->addColumn($this->setup->getTable($tableName), $columnName, $columnDefinition);
            }
        }

        $this->setup->endSetup();
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }
}
