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
 * Class Events.
 */
class Events extends AbstractModel
{
    const URL_PREFIX = 'event';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('CameraHouse\StoreLocator\Model\ResourceModel\Events');
    }
}
