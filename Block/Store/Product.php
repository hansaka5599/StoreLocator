<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Block\Store;

use CameraHouse\StoreLocator\Helper\Data;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Element\Template;

/**
 * Class Product.
 */
class Product extends Template
{
    /**
     * Variable store.
     *
     * @var mixed
     */
    protected $store;

    /**
     * Variable cameraHouseStoreLocatorHelper.
     *
     * @var Data
     */
    protected $cameraHouseStoreLocatorHelper;

    /**
     * Variable productCollectionFactory.
     *
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * Variable catalogProductVisibility.
     *
     * @var Visibility
     */
    protected $catalogProductVisibility;

    /**
     * Variable scopeConfig.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Variable listProduct.
     *
     * @var ListProduct
     */
    protected $listProduct;

    /**
     * Variable storeManager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Product constructor.
     * @param Context $context
     * @param Data $cameraHouseStoreLocatorHelper
     * @param CollectionFactory $productCollectionFactory
     * @param Visibility $catalogProductVisibility
     * @param ListProduct $listProduct
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $cameraHouseStoreLocatorHelper,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        ListProduct $listProduct,
        array $data = []
    ) {
        $this->store = $context->getRegistry()->registry('current_store_locator');
        $this->cameraHouseStoreLocatorHelper = $cameraHouseStoreLocatorHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->scopeConfig = $context->getScopeConfig();
        $this->listProduct = $listProduct;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    /**
     * Function getCourseProductCollection.
     *
     * @return $this
     */
    public function getCourseProductCollection()
    {
        $productLimit = $this->cameraHouseStoreLocatorHelper->getConfigStoreCourseProductLimit();
        $storeLocationId = $this->store->getStoreLocatorId();

        $collection = $this->productCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_active', ['eq' => 1])
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->addAttributeToFilter('attribute_set_id',
                ['eq' => $this->cameraHouseStoreLocatorHelper->getConfigCourseAttributeSetId()])
            ->addAttributeToFilter('course_store', ['eq' => $storeLocationId]);

        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        $collection->getSelect()->order('course_date')
            ->limit($productLimit);
        //$collection->load();

        return $collection;
    }

    /**
     * Function getListProductBlock.
     *
     * @return ListProduct
     */
    public function getListProductBlock()
    {
        return $this->listProduct;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        return $this->listProduct->getProductPrice($product);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        return $this->listProduct->getAddToCartPostParams($product);
    }

    /**
     * Get current store locator name used for course products.
     *
     * @return mixed
     */
    public function getCurrentStoreName()
    {
        return $this->store->getName();
    }
}
