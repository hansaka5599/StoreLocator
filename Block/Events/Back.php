<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Block\Events;

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
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Variable event.
     * @var mixed
     */
    protected $event;

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
        $this->event = $this->coreRegistry->registry('current_store_event');

        parent::__construct($context, $data);
    }

    /**
     * Function getStoreUrl.
     * @return string
     */
    public function getStoreUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl().'store/'.$this->event['url_key'];
    }

    /**
     * Function getContentHeading.
     *
     * @return string
     */
    public function getContentHeading()
    {
        return $this->event['content_heading'];
    }
}
