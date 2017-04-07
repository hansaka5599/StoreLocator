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
class Back extends Template
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Variable service.
     *
     * @var mixed
     */
    protected $service;

    /**
     * Back constructor.
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
        $this->service = $this->coreRegistry->registry('current_store_service');

        parent::__construct($context, $data);
    }

    /**
     * Function getStoreUrl.
     *
     * @return string
     */
    public function getStoreUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl().'store/'.$this->service['url_key'];
    }

    /**
     * Function getContentHeading.
     *
     * @return mixed
     */
    public function getContentHeading()
    {
        return $this->service['content_heading'];
    }
}
