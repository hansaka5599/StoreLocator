<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Model\ResourceModel\Category;

use Magento\Store\Model\Store;
use Netstarter\StoreLocator\Model\ResourceModel\Category\Collection as CategoryCollection;

/**
 * Class Collection.
 */
class Collection extends CategoryCollection
{
    /**
     * Function aroundGetStoreCategories.
     *
     * @param CategoryCollection $subject
     * @param \Closure           $proceed
     * @param $storeId
     *
     * @return CategoryCollection
     */
    public function aroundGetStoreCategories(
        CategoryCollection $subject,
        \Closure $proceed,
        $storeId
    ) {
        $storeIds = [Store::DEFAULT_STORE_ID, (int) $storeId];
        $subject->join(
            ['category_store' => 'ns_store_locator_category_store'],
            'main_table.category_id = category_store.category_id'
        )
            ->addFieldToFilter('store_id', ['in' => $storeIds])
            ->addFieldToFilter('in_main_page_side_pane', ['eq' => '1']);

        return $subject;
    }
}
