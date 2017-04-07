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

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class View.
 */
class View extends Template
{
    /**
     * Page Config.
     *
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * Core registry.
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * Variable service.
     *
     * @var mixed|null
     */
    private $service = null;

    /**
     * Variable filterProvider.
     *
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * View constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->filterProvider = $filterProvider;
        $this->pageConfig = $context->getPageConfig();
        $this->coreRegistry = $coreRegistry;

        $this->service = $this->coreRegistry->registry('current_store_service');
        parent::__construct($context, $data);
    }

    /**
     * Function _prepareLayout.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set($this->service['page_title']);
        $this->pageConfig->setKeywords($this->service['meta_keyword']);
        $this->pageConfig->setDescription(substr($this->service['meta_data'], 0, 255));

        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbsBlock) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl(),
                ]
            );

            $breadcrumbsBlock->addCrumb(
                'stores',
                [
                    'label' => __('Stores'),
                    'title' => __('Stores Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl().'stores',
                ]
            );

            $breadcrumbsBlock->addCrumb(
                'store',
                [
                    'label' => __($this->service['store_heading_name']),
                    'title' => __($this->service['store_heading_name']),
                    'link' => $this->_storeManager->getStore()->getBaseUrl().'store/'.$this->service['url_key'],
                ]
            );

            $breadcrumbsBlock->addCrumb(
                'event',
                [
                    'label' => __($this->service['content_heading']),
                    'title' => __($this->service['content_heading']),
                ]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Function getPageContent.
     *
     * @return string
     */
    public function getPageContent()
    {
        return $this->filterProvider->getPageFilter()->filter($this->service['content']);
    }

    /**
     * Returns action url for contact form.
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('chstores/services/register', ['_secure' => true]);
    }

    /**
     * Function getNodeId.
     *
     * @return mixed
     */
    public function getNodeId()
    {
        return $this->service['node_id'];
    }

    /**
     * Function getRequestUrl.
     *
     * @return mixed
     */
    public function getRequestUrl()
    {
        return $this->service['request_url'];
    }

    /**
     * Function getIdentifier.
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->service['identifier'];
    }

    /**
     * Function getStoreUrlKey.
     *
     * @return mixed
     */
    public function getStoreUrlKey()
    {
        return $this->service['url_key'];
    }

    /**
     * Function getPageTitle.
     *
     * @return mixed
     */
    public function getPageTitle()
    {
        return $this->service['page_title'];
    }

    /**
     * Function getServiceName.
     *
     * @return mixed
     */
    public function getServiceName()
    {
        return $this->service['name'];
    }

    /**
     * Function getStoreName.
     *
     * @return mixed
     */
    public function getStoreName()
    {
        return $this->service['store_heading_name'];
    }

    /**
     * Function getStoreAddress.
     *
     * @return string
     */
    public function getStoreAddress()
    {
        return $this->service['street'].'<br />'.$this->service['street2'].'<br />'
        .$this->service['suburb'].'<br />'.$this->service['postcode'];
    }

    /**
     * Function getStorePhone.
     *
     * @return mixed
     */
    public function getStorePhone()
    {
        return $this->service['phone'];
    }

    /**
     * Function getStoreLocatorId.
     *
     * @return mixed
     */
    public function getStoreLocatorId()
    {
        return $this->service['store_locator_id'];
    }
}
