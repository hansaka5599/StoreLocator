<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Users.
 */
class Users extends AbstractDb
{
    /**
     * Function _construct.
     */
    protected function _construct()
    {
        $this->_init('ns_store_locator_users', 'sequence_id');
    }
}
