<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema.
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Function install.
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /*
         * Create table 'ns_store_locator_users'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ns_store_locator_users')
        )->addColumn(
            'sequence_id',
            Table::TYPE_SMALLINT,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'before' => 'store_locator_id',
                'primary' => true,
            ],
            'Sequence Id'
        )->addColumn(
            'store_locator_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Locator Id'
        )->addColumn(
            'user_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'User ID'
        )->addForeignKey(
            $installer->getFkName('ns_store_locator_users', 'store_locator_id', 'ns_store_locator', 'store_locator_id'),
            'store_locator_id',
            $installer->getTable('ns_store_locator'),
            'store_locator_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('ns_store_locator_users', 'user_id', 'admin_user', 'user_id'),
            'user_id',
            $installer->getTable('admin_user'),
            'user_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Store Locator Users'
        );

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
