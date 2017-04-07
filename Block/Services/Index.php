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
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Index - Store Services Landing Page.
 */
class Index extends Template
{
    /**
     * Page Config.
     *
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * Variable storeLocatorData.
     *
     * @var null
     */
    private $storeLocatorData = null;

    /**
     * Variable servicesData.
     *
     * @var null
     */
    private $servicesData = null;

    /**
     * Core registry.
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * Variable filterProvider.
     *
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param Registry                      $coreRegistry
     * @param FilterProvider       $filterProvider
     * @param array                                            $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->pageConfig = $context->getPageConfig();
        $this->coreRegistry = $coreRegistry;
        $this->filterProvider = $filterProvider;

        $data = $this->coreRegistry->registry('current_store_services');

        $this->storeLocatorData = $data['store_data'];
        $this->servicesData = $data['services'];

        parent::__construct($context, $data);
    }

    /**
     * Function _prepareLayout.
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set($this->storeLocatorData['heading_name'].' Services');
        $this->pageConfig->setKeywords($this->storeLocatorData['meta_keywords']);
        $this->pageConfig->setDescription(substr($this->storeLocatorData['meta_description'], 0, 255));

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
                    'label' => __($this->storeLocatorData['heading_name']),
                    'title' => __($this->storeLocatorData['heading_name']),
                    'link' => $this->_storeManager->getStore()->getBaseUrl().
                        'store/'.$this->storeLocatorData['url_key'],
                ]
            );

            $breadcrumbsBlock->addCrumb(
                'event',
                [
                    'label' => __('Services'),
                    'title' => __('Services'),
                ]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Function getServices.
     */
    public function getServices()
    {
        return $this->servicesData;
    }

    /**
     * Function getStoreData.
     */
    public function getStoreData()
    {
        return $this->storeLocatorData;
    }

    /**
     * Function getImageUrl.
     *
     * @param $path
     *
     * @return string
     */
    public function getImageUrl($path)
    {
        return $this->_storeManager->getStore()
            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA).'wysiwyg/'.$path;
    }

    /**
     * Function getServiceUrl.
     *
     * @param $url
     *
     * @return string
     */
    public function getServiceUrl($url)
    {
        return $this->_storeManager->getStore()->getBaseUrl().$url;
    }

    /**
     * Function getServiceContent.
     *
     * @return string
     */
    public function getServiceContent()
    {
        return $this->filterProvider->getPageFilter()->filter($this->storeLocatorData['store_service_content']);
    }
}
