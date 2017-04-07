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

use CameraHouse\StoreLocator\Helper\Data as CameraHouseHelperData;
use CameraHouse\StoreLocator\Model\ResourceModel\Events\CollectionFactory as CameraHouseCollectionFactory;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Netstarter\Bannerslider\Model\SliderFactory;
use Netstarter\StoreLocator\Helper\Data;
use Netstarter\StoreLocator\Model\ResourceModel\Category\Node\CollectionFactory;
use Netstarter\StoreLocator\Model\Store;

/**
 * Class View.
 */
class View extends \Netstarter\StoreLocator\Block\Store\View
{
    /**
     * Variable eventsCollectionFactory.
     *
     * @var CameraHouseCollectionFactory
     */
    protected $eventsCollectionFactory;

    /**
     * Variable sliderFactory.
     *
     * @var SliderFactory
     */
    protected $sliderFactory;

    /**
     * Variable catNodeCollectionFactory.
     *
     * @var CollectionFactory
     */
    protected $catNodeCollectionFactory;

    /**
     * Variable cameraHouseStoreLocatorHelper.
     *
     * @var CameraHouseHelperData
     */
    protected $cameraHouseStoreLocatorHelper;

    protected $customerSession;

    /**
     * View constructor.
     * @param Template\Context $context
     * @param Registry $coreRegistry
     * @param CollectionFactory $catNodeCollectionFactory
     * @param PostHelper $postDataHelper
     * @param Data $helper
     * @param CameraHouseCollectionFactory $eventsCollectionFactory
     * @param SliderFactory $sliderFactory
     * @param CameraHouseHelperData $cameraHouseStoreLocatorHelper
     * @param Session $customerSession
     * @param FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $coreRegistry,
        CollectionFactory $catNodeCollectionFactory,
        PostHelper $postDataHelper,
        Data $helper,
        CameraHouseCollectionFactory $eventsCollectionFactory,
        SliderFactory $sliderFactory,
        CameraHouseHelperData $cameraHouseStoreLocatorHelper,
        SessionFactory $customerSession,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->eventsCollectionFactory = $eventsCollectionFactory;
        $this->sliderFactory = $sliderFactory;
        $this->catNodeCollectionFactory = $catNodeCollectionFactory;
        $this->cameraHouseStoreLocatorHelper = $cameraHouseStoreLocatorHelper;
        $this->filterProvider = $filterProvider;
        $this->customerSession = $customerSession;
        parent::__construct($context, $coreRegistry, $catNodeCollectionFactory, $postDataHelper, $helper, $data);
    }

    /**
     * Function getEventsList.
     *
     * @return \CameraHouse\StoreLocator\Model\ResourceModel\Events\Collection
     */
    public function getEventsList()
    {
        $events = $this->eventsCollectionFactory->create();
        $events->addFieldToSelect('identifier');
        $events->addFieldToSelect('page_title');
        $events->addFieldToFilter('status', 1);
        $events->addFieldToFilter('store_locator_id', $this->getStoreLocationId());
        $events->addOrder('sort_order', 'asc');

        return $events;
    }

    /**
     * @return array
     */
    public function getStoreServices()
    {
        $storeServices = [];
        $services = $this->catNodeCollectionFactory->create();

        $services->getSelect()->join(
            ['store_category_node' => 'ns_store_locator_store_category_node'],
            'main_table.node_id = store_category_node.node_id', ['*']
        );

        $services->addFieldToFilter('store_locator_id', $this->getStoreLocationId());
        $services->addFieldToFilter('main_table.category_id',
            $this->cameraHouseStoreLocatorHelper->getConfigServiceCatId());
        $services->getSelect()->limit($this->cameraHouseStoreLocatorHelper->getConfigStorePageServiceLimit());

        $services->addOrder('sort_order', 'asc');

        if ($services) {
            foreach ($services->getData() as $data) {
                $icons = json_decode($data['category_icon_tile'], true);
                if (is_array($icons) && !empty($icons)) {
                    if (array_key_exists(1, $icons)) {
                        $data['icon'] = $this->_storeManager->getStore()->getBaseUrl(
                                UrlInterface::URL_TYPE_MEDIA
                            ).'wysiwyg/'.$icons[1]['path'];
                    }
                }
                $storeServices[] = $data;
            }
        }

        return $storeServices;
    }

    /**
     * @param $key
     *
     * @return string
     */
    public function getEventUrl($key)
    {
        return $this->getUrl('store/event/'.$key);
    }

    /**
     * Function getServiceLandingUrl.
     *
     * @return mixed
     */
    public function getServiceLandingUrl()
    {
        return $this->_storeManager->getStore()->getUrl(
            Store::URL_PREFIX.'/'.$this->store->getUrlKey().'/services'
        );
    }

    /**
     * Function getServiceUrl.
     *
     * @param $key
     *
     * @return string
     */
    public function getServiceUrl($key)
    {
        return $this->_storeManager->getStore()->getBaseUrl()
        . Store::URL_PREFIX
        .'/'.$this->store->getUrlKey().'/'.$key;
    }

    /**
     * Function getSlider.
     */
    public function getSlider()
    {
        $identifier = null;
        $slider = $this->store->getSliderId();
        if ($slider) {
            $model = $this->sliderFactory->create();
            $model->load($slider);
            $identifier = $model->getIdentifier();
        }

        return $identifier;
    }

    /**
     * Function getStoreSpotLight.
     *
     * @param null $id
     *
     * @return array
     */
    public function getStoreSpotLight($id = null)
    {
        $spot_light = [];
        if ($id) {
            if ($id == '1') {
                $image_src = $this->store->getStorePageSpotImage1();
                if (!empty($image_src)) {
                    $image_src = $this->helper->prepareStoreImageUrl($image_src);
                }
                $spot_light['src'] = $image_src;
                $spot_light['alt'] = $this->store->getStorePageAltText1();
                $spot_light['url'] = $this->store->getStorePageSpotUrl1();
            }
            if ($id == '2') {
                $image_src = $this->store->getStorePageSpotImage2();
                if (!empty($image_src)) {
                    $image_src = $this->helper->prepareStoreImageUrl($image_src);
                }
                $spot_light['src'] = $image_src;
                $spot_light['alt'] = $this->store->getStorePageAltText2();
                $spot_light['url'] = $this->store->getStorePageSpotUrl2();
            }
        }

        return $spot_light;
    }

    /**
     * get save store url.
     *
     * @return string
     */
    public function getSaveStoreUrl()
    {
        $session = $this->customerSession->create();
        $customer = $session->isLoggedIn();
        if ($customer) {
            return $this->postDataHelper->getPostData($this->getUrl('stores/account/save'));
        } else {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function getStoreHeadingName()
    {
        return $this->store->getHeadingName();
    }

    /**
     * Function getContent with wysiwyg images.
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->filterProvider->getPageFilter()->filter($this->store->getStoreContent());
    }

    /**
     * get store address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->store->getStreet().'<br  />'. $this->store->getData('street2').'<br />'.$this->store->getSuburb()
        .' '.$this->store->getPostcode().' '.$this->store->getRegionCode();
    }
}
