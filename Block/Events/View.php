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
     * Variable filterProvider.
     *
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * Event.
     *
     * @var mixed
     */
    private $event;

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
        $this->pageConfig = $context->getPageConfig();
        $this->coreRegistry = $coreRegistry;
        $this->filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }

    /**
     * Function getEvent Data joined with store locator info.
     *
     * @return array
     */
    private function getEvent()
    {
        $eventData = $this->coreRegistry->registry('current_store_event');

        return $eventData;
    }

    /**
     * Function _prepareLayout.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->event = $this->getEvent();
        $this->pageConfig->getTitle()->set($this->event['page_title']);
        $this->pageConfig->setKeywords($this->event['meta_keyword']);
        $this->pageConfig->setDescription(substr($this->event['meta_data'], 0, 255));
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
                    'label' => __($this->event['heading_name']),
                    'title' => __($this->event['heading_name']),
                    'link' => $this->_storeManager->getStore()->getBaseUrl().'store/'.$this->event['url_key'],
                ]
            );

            $breadcrumbsBlock->addCrumb(
                'event',
                [
                    'label' => __($this->event['content_heading']),
                    'title' => __($this->event['content_heading']),
                ]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Function getPageContent.
     *
     * @return mixed
     */
    public function getPageContent()
    {
        return $this->filterProvider->getPageFilter()->filter($this->event['content']);
    }

    /**
     * Function getPageTitle.
     *
     * @return mixed
     */
    public function getPageTitle()
    {
        return $this->event['page_title'];
    }

    /**
     * Function getEventId.
     *
     * @return mixed
     */
    public function getEventId()
    {
        return $this->event['event_id'];
    }

    /**
     * Function getIdentifier.
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->event['identifier'];
    }

    /**
     * Function getStoreName.
     *
     * @return mixed
     */
    public function getStoreName()
    {
        return $this->event['heading_name'];
    }

    /**
     * Function getStoreAddress.
     *
     * @return string
     */
    public function getStoreAddress()
    {
        return $this->event['street'].'<br />'.$this->event['street2'].'<br />'
        .$this->event['suburb'].' '.$this->event['postcode'];
    }

    /**
     * Function getStorePhone.
     *
     * @return mixed
     */
    public function getStorePhone()
    {
        return $this->event['phone'];
    }

    /**
     * Function getStoreUrl.
     *
     * @return string
     */
    public function getStoreUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl().'store/'.$this->event['url_key'];
    }

    /**
     * Returns action url for contact form.
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('chstores/event/register', ['_secure' => true]);
    }
}
