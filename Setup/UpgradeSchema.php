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
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table as DdlTable;

/**
 * Class UpgradeSchema.
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Function addColumn.
     *
     * @param SchemaSetupInterface $setup
     * @param $tableName
     * @param $columns
     */
    private function addColumn(SchemaSetupInterface $setup, $tableName, $columns)
    {
        $connection = $setup->getConnection();
        foreach ($columns as $name => $definition) {
            $connection->addColumn($tableName, $name, $definition);
        }
    }

    /**
     * Function modifyColumn.
     * @param SchemaSetupInterface $setup
     * @param $tableName
     * @param $columnName
     * @param $definition
     */
    private function modifyColumn(SchemaSetupInterface $setup, $tableName, $columnName, $definition)
    {
        $connection = $setup->getConnection();

        $connection->modifyColumn($tableName, $columnName, $definition);
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.1.0', '<')) {
            $tableName = $setup->getTable('ns_store_locator');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'subdomain' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'Store Sub Domain Name',
                    ],
                    'subdomain_priority' => [
                        'type' => DdlTable::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Store Priority',
                    ],
                ];
                $this->addColumn($setup, $tableName, $columns);
            }
        }

        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            $tableName = $setup->getTable('ns_store_locator');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'photo_creation_url' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Store Photo Creation URL',
                    ],
                    'digital_print_url' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Digital Print URL',
                    ],
                    'event_photography_url' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Event Photography URL',
                    ],
                    'latest_offers_url' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Latest Offers URL',
                    ],
                ];
                $this->addColumn($setup, $tableName, $columns);
            }
        }

        if (version_compare($context->getVersion(), '2.3.0', '<')) {
            $tableName = $setup->getTable('ns_store_locator');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'spot_image_1' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Spot Banner 1',
                    ],
                    'alt_text_1' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Alt Text 1',
                    ],
                    'spot_url_1' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'URL 1',
                    ],
                    'spot_image_2' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Spot Banner 2',
                    ],
                    'alt_text_2' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Alt Text 2',
                    ],
                    'spot_url_2' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'URL 2',
                    ],
                    'spot_image_3' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Spot Banner 3',
                    ],
                    'alt_text_3' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Alt Text 3',
                    ],
                    'spot_url_3' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'URL 3',
                    ],
                ];
                $this->addColumn($setup, $tableName, $columns);
            }
        }

        if (version_compare($context->getVersion(), '2.6.0', '<')) {
            $tableName = $setup->getTable('ns_store_locator_category_node');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'category_icon_tile' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Icon image and Title Image JSON',
                    ],
                ];
                $this->addColumn($setup, $tableName, $columns);
            }
        }

        if (version_compare($context->getVersion(), '2.7.0', '<')) {
            $tableName = $setup->getTable('ns_store_locator_store_category_node');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'service_category' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Service Category',
                    ],
                    'dedicated_page' => [
                        'type' => DdlTable::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Dedicated Page',
                    ],
                    'page_title' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Page Title',
                    ],
                    'content_heading' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Content Heading',
                    ],
                    'content' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Content',
                    ],
                    'meta_keyword' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'meta keyword',
                    ],
                    'meta_data' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'meta description',
                    ],
                ];
                $this->addColumn($setup, $tableName, $columns);
            }
        }

        if (version_compare($context->getVersion(), '2.8.0', '<')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('ns_ch_store_locator_events')
            )
                ->addColumn(
                    'event_id',
                    DdlTable::TYPE_SMALLINT,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'Event ID'
                )
                ->addColumn(
                    'store_locator_id',
                    DdlTable::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Service Name'
                )
                ->addColumn(
                    'identifier',
                    DdlTable::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Request Url'
                )
                ->addColumn(
                    'sort_order',
                    DdlTable::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Sort order'
                )
                ->addColumn(
                    'status',
                    DdlTable::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Status'
                )
                ->addColumn(
                    'page_title',
                    DdlTable::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'page title'
                )
                ->addColumn(
                    'content_heading',
                    DdlTable::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'content heading'
                )
                ->addColumn(
                    'content',
                    DdlTable::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'content'
                )
                ->addColumn(
                    'meta_keyword',
                    DdlTable::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'meta keyword'
                )
                ->addColumn(
                    'meta_data',
                    DdlTable::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'meta data'
                )
                ->addIndex($setup->getIdxName('event_url', ['identifier']), ['identifier']);
            $setup->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '2.8.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ns_store_locator_store_category_node'),
                'category_id',
                [
                    'type' => DdlTable::TYPE_SMALLINT,
                    'comment' => 'Category Id',
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.8.2', '<')) {
            $tableName = $setup->getTable('ns_store_locator');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'slider_id' => [
                        'type' => DdlTable::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Banner Slider',
                    ],
                    'store_page_spot_image_1' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Spot Banner 1',
                    ],
                    'store_page_alt_text_1' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Alt Text 1',
                    ],
                    'store_page_spot_url_1' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'URL 1',
                    ],
                    'store_page_spot_image_2' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Spot Banner 2',
                    ],
                    'store_page_alt_text_2' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Alt Text 2',
                    ],
                    'store_page_spot_url_2' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'URL 2',
                    ],
                ];
                $this->addColumn($setup, $tableName, $columns);
            }
        }

        if (version_compare($context->getVersion(), '2.8.3', '<')) {
            $tableName = $setup->getTable('ns_store_locator_store_category_node');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'redirect_url' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Printshop Redirect Url',
                    ],
                ];
                $this->addColumn($setup, $tableName, $columns);
            }
        }

        if (version_compare($context->getVersion(), '2.8.4', '<')) {
            $tableName = $setup->getTable('ns_store_locator');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'store_service_content' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Store Service Content',
                    ],
                ];
                $this->addColumn($setup, $tableName, $columns);
            }
        }

        if (version_compare($context->getVersion(), '2.8.5', '<')) {
            $tableName = $setup->getTable('ns_store_locator_category');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'in_main_page_side_pane' => [
                        'type' => DdlTable::TYPE_SMALLINT,
                        'comment' => 'Display in store main page side pane',
                    ],
                ];
                $this->addColumn($setup, $tableName, $columns);
            }
        }

        if (version_compare($context->getVersion(), '2.8.6', '<')) {
            $tableName = $setup->getTable('ns_store_locator_category_node');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $this->modifyColumn(
                    $setup,
                    $tableName,
                    'name',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => 150,
                    ]
                );
                /* $tableName, $columnName, $definition, $flushData = false, $schemaName = null */
            }
        }

        if (version_compare($context->getVersion(), '2.8.7', '<')) {
            $tableName = $setup->getTable('ns_store_locator');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'street2' => [
                        'type' => DdlTable::TYPE_TEXT,
                        'comment' => 'Address line 2',
                    ],
                ];
                $this->addColumn($setup, $tableName, $columns);
            }
        }

        $setup->endSetup();
    }
}
