<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Model\ResourceModel\Users;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection class.
 */
class Collection extends AbstractCollection
{
    /**
     * Collection constructor.
     */
    protected function _construct()
    {
        $this->_init(
            'CameraHouse\StoreLocator\Model\Users',
            'CameraHouse\StoreLocator\Model\ResourceModel\Users'
        );
    }
}
