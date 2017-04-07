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

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;

/**
 * Class Contact.
 */
class Contact extends Template
{
    /**
     * Variable store.
     *
     * @var mixed
     */
    protected $store;

    /**
     * Variable coreRegistry.
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $coreRegistry = null;

    /**
     * Contact constructor.
     *
     * @param Template\Context            $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array                       $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->pageConfig = $context->getPageConfig();
        $this->coreRegistry = $coreRegistry;
        $this->store = $this->coreRegistry->registry('current_store_locator');
        parent::__construct($context, $data);
    }

    /**
     * Returns action url for contact form.
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('chstores/store/contact', ['_secure' => true]);
    }

    /**
     * Function getCurrentStoreId.
     *
     * @return mixed
     */
    public function getCurrentStoreId()
    {
        return $this->store->getStoreLocatorId();
    }

    /**
     * Function getLocationIdentifier.
     *
     * @return mixed
     */
    public function getLocationIdentifier()
    {
        return $this->store->getIdentifier();
    }
}
