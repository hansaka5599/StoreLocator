<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Block\Services;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;

/**
 * Class Back.
 */
class BackServices extends Template
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * BackServices constructor.
     *
     * @param Context  $context
     * @param Registry $coreRegistry
     * @param array    $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Function getStoreUrl.
     *
     * @return string
     */
    public function getStoreUrl()
    {
        $data = $this->coreRegistry->registry('current_store_services');

        return $this->_storeManager->getStore()->getBaseUrl().'store/'.$data['store_data']['url_key'];
    }
}
