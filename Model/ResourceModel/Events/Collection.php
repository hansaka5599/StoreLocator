<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Model\ResourceModel\Events;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection.
 */
class Collection extends AbstractCollection
{
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init('CameraHouse\StoreLocator\Model\Events', 'CameraHouse\StoreLocator\Model\ResourceModel\Events');
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $om = ObjectManager::getInstance();
        /** @var \Magento\Framework\App\State $state */
        $state = $om->get('Magento\Framework\App\State');
        if ('adminhtml' === $state->getAreaCode()) {
            $this->getSelect()->joinLeft(
                ['ns_store_locator' => $this->getTable('ns_store_locator')],
                'main_table.store_locator_id = ns_store_locator.store_locator_id',
                ['name']
            );
        }

        return $this;
    }
}
