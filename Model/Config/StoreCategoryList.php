<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Model\Config;

use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\StoreManagerInterface;
use Netstarter\StoreLocator\Model\ResourceModel\Category\CollectionFactory;

/**
 * Class StoreCategoryList.
 */
class StoreCategoryList implements ArrayInterface
{
    /**
     * Variable categoryCollectionFactory.
     *
     * @var CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * Variable storeManager.
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * StoreCategoryList constructor.
     * @param CollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Function toOptionArray.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];
        $ret[] = ['value' => '', 'label' => 'Please Select'];
        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value,
            ];
        }

        return $ret;
    }

    /**
     * Function toArray.
     *
     * @return array
     */
    public function toArray()
    {
        $categories = $this->getStoreCategories();
        $catagoryList = [];
        foreach ($categories as $category) {
            $catagoryList[$category->getCategoryId()] = __($category->getName());
        }

        return $catagoryList;
    }

    /**
     * Function getStoreCategories.
     *
     * @return mixed
     */
    public function getStoreCategories()
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addFieldToSelect('category_id');
        $collection->addFieldToSelect('name');

        return $collection;
    }
}
