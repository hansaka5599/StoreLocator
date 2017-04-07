<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Users.
 */
class Users extends AbstractModel
{
    /**
     * Function _construct.
     */
    protected function _construct()
    {
        $this->_init('CameraHouse\StoreLocator\Model\ResourceModel\Users');
    }
}
