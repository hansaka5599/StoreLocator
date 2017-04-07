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

/**
 * Class Store.
 */
class Store extends \Netstarter\StoreLocator\Model\Store
{
    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * Function saveCategories.
     *
     * @param $storeLocatorId
     * @param $categoryId
     * @param $nodes
     */
    public function saveCategories($storeLocatorId, $categoryId, $nodes)
    {
        $this->_getResource()->saveCategories($storeLocatorId, $categoryId, $nodes);
    }
}
