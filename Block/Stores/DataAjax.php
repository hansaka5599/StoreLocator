<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Block\Stores;

use CameraHouse\StoreLocator\Helper\Data as CameraHouseHelperData;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use CameraHouse\StoreLocator\Model;
use Netstarter\Bannerslider\Model\ResourceModel\Banner\CollectionFactory;
use Netstarter\StoreLocator\Cache\Type\Stores;
use Netstarter\StoreLocator\Helper\Data as NetstarterHelperData;

/**
 * Class DataAjax.
 */
class DataAjax extends \Netstarter\StoreLocator\Block\Stores\DataAjax
{
    /**
     * Variable jsonHelper.
     * @var Data
     */
    protected $jsonHelper;

    /**
     * Variable storeResource.
     * @var Model\ResourceModel\Store
     */
    protected $storeResource;

    /**
     * Variable _storeManager.
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Store Locator Data Cache group.
     */
    const CACHE_GROUP = Stores::TYPE_IDENTIFIER;

    /**
     * Variable storeLocatorHelper.
     * @var NetstarterHelperData
     */
    protected $storeLocatorHelper;

    /**
     * Variable bannerCollectionFactory.
     * @var CollectionFactory
     */
    protected $bannerCollectionFactory;

    /**
     * Variable cameraHouseStoreLocatorHelper.
     * @var CameraHouseHelperData
     */
    protected $cameraHouseStoreLocatorHelper;

    protected $_timezoneInterface;

    /**
     * DataAjax constructor.
     * @param Template\Context                                                      $context
     * @param Model\ResourceModel\Store                                             $storeResource
     * @param Data                                                                  $jsonHelper
     * @param NetstarterHelperData $storeLocatorHelper
     * @param CameraHouseHelperData $cameraHouseStoreLocatorHelper
     * @param CollectionFactory $bannerCollectionFactory
     * @param array                                                                 $data
     */
    public function __construct(
        Template\Context $context,
        Model\ResourceModel\Store $storeResource,
        Data $jsonHelper,
        NetstarterHelperData $storeLocatorHelper,
        CameraHouseHelperData $cameraHouseStoreLocatorHelper,
        CollectionFactory $bannerCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $storeResource, $jsonHelper, $data);
        $this->storeResource = $storeResource;
        $this->jsonHelper = $jsonHelper;
        $this->_storeManager = $context->getStoreManager();
        $this->_isScopePrivate = true;
        $this->storeLocatorHelper = $storeLocatorHelper;
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        $this->cameraHouseStoreLocatorHelper = $cameraHouseStoreLocatorHelper;
        $this->_timezoneInterface = $context->getLocaleDate();
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * Get store location data.
     *
     * @param $storeId
     * @param null $conditions
     *
     * @return array
     */
    protected function getStoreData($storeId, $conditions = null, $useAndOperator = false)
    {
        $data = [];
        $stores = $this->storeResource->getStoreLocations($storeId, $conditions, $useAndOperator);

        $bannerCollection = $this->bannerCollectionFactory->create();
        $bannerCollection->addFieldToSelect('store_locator_id');
        $bannerCollection->addFieldToSelect('click_url');
        $bannerCollection->addFieldToSelect('as_bg');
        $bannerCollection->addFieldToSelect('target');
        $bannerCollection->addFieldToSelect('start_time');
        $bannerCollection->addFieldToSelect('end_time');
        $bannerCollection->addFieldToSelect('youtube');
        $bannerCollection->addFieldToSelect('image_alt');
        $bannerCollection->addFieldToSelect('order_banner');
        $bannerCollection->addFieldToSelect('status');
        $bannerCollection->addFieldToSelect('ns_banner_custom_html');
        $bannerCollection->addFieldToSelect('image_collection_JSON');
        $bannerCollection->addFieldToFilter('status', ['eq' => 1]);
        $bannerCollection->addFieldToFilter('slider_id',
            $this->cameraHouseStoreLocatorHelper->getConfigHomePageBannerSlider());

        $storePrintShopNodes = $this->storeResource->getStorePrintShopServices();

        foreach ($stores as $store) {
            //Store locator address
            $streetAddress2 = ($store['street2'] != '') ? $store['street2'].', ' : '';
            $address = $store['street'].', ' . $streetAddress2 . $store['suburb'] .', '
                .$store['postcode'].', '.$store['region_code'];

            //Map Address
            $streetAddressmap = ($store['street2'] != '') ? ' :br '.$store['street2'] : '';
            $mapaddress = $store['street'] . $streetAddressmap . ' :br ' . $store['suburb'].', '
                .$store['postcode'].', '.$store['region_code'];

            $arr = [
                'n' => $store['heading_name'],
                'na' => $store['name'],
                'i' => $store['store_locator_id'],
                'u' => '/store/'.$store['url_key'],
                'p' => $store['phone'],
                'e' => $store['email'],
                'a' => $address,
                'ma' => $mapaddress,
                'fa' => [
                    'st' => $store['street'],
                    'st2' => $store['street2'],
                    'su' => $store['suburb'],
                    'po' => $store['postcode'],
                    'rc' => $store['region_code'],
                ],
                'l' => $store['latitude'],
                'g' => $store['longitude'],
                'f' => ($store['is_new'] ? __('New') : ''),
                'o' => (int) $store['use_default_oh'],
                'h' => (int) $store['use_default_ho'],
                'sd' => $store['subdomain'],
                'sp' => (int) $store['subdomain_priority'],
                'ml' => [
                    'pc' => $store['photo_creation_url'],
                    'dp' => $store['digital_print_url'],
                    'ep' => $store['event_photography_url'],
                    'lo' => $store['latest_offers_url'],
                ],
                'spl1' => [
                    'img' => ($store['spot_image_1'] != '') ?
                        $this->getStoreLocatorMediaUrl().$store['spot_image_1'] : '',
                    'alt' => $store['alt_text_1'],
                    'url' => $store['spot_url_1'],
                ],
                'spl2' => [
                    'img' => ($store['spot_image_2'] != '') ?
                        $this->getStoreLocatorMediaUrl().$store['spot_image_2'] : '',
                    'alt' => $store['alt_text_2'],
                    'url' => $store['spot_url_2'],
                ],
                'spl3' => [
                    'img' => ($store['spot_image_3'] != '') ?
                        $this->getStoreLocatorMediaUrl().$store['spot_image_3'] : '',
                    'alt' => $store['alt_text_3'],
                    'url' => $store['spot_url_3'],
                ],
            ];

            if (!$store['use_default_oh']) {
                $arr['oh'] = $store['store_opening_hours'];
            }
            if (!$store['use_default_ho']) {
                $arr['ho'] = $store['store_holiday_hours'];
            }

            if ($bannerCollection) {
                foreach ($bannerCollection as $banner) {
                    if ($banner->getStoreLocatorId() == $store['store_locator_id']) {
                        $arr['hb'][] = [
                            'image_collection_JSON' => $banner->getData('image_collection_JSON'),
                            'ns_banner_custom_html' => $banner['ns_banner_custom_html'],
                            'youtube' => $banner['youtube'],
                            'click_url' => $banner['click_url'],
                            'target' => $banner['target'],
                            'as_bg' => $banner['as_bg'],
                            'order_banner' => $banner['order_banner'],
                            'image_alt' => $banner['image_alt'],
                            'start_time' => ($banner['start_time']) ? $this->_timezoneInterface->date(
                                $banner['start_time'])->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i')
                                : null,
                            'end_time' => ($banner['end_time']) ? $this->_timezoneInterface->date(
                                $banner['end_time'])->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i')
                                : null,
                        ];
                    }
                }
            }

            if ($storePrintShopNodes) {
                foreach ($storePrintShopNodes as $node) {
                    if ($node['store_locator_id'] == $store['store_locator_id']) {
                        $arr['prd'][] = [
                            'sc' => $node['name'],
                            'dp' => $node['dedicated_page'],
                            'ru' => $node['dedicated_page'] ? $node['identifier'] : $node['redirect_url'],
                        ];
                    }
                }
            }
            $data[] = $arr;
        }

        return $data;
    }



    /**
     * get store location data
     * just small dataset
     *
     * @param $storeId
     * @param null $conditions
     * @return array
     */
    protected function getStoreDataFew($storeId, $conditions = null)
    {
        $data = [];
        $stores = $this->storeResource->getStoreLocationsFew($storeId, $conditions);
        foreach ($stores as $store) {
            $address = $store['street'] . ', ' . $store['street2'] . ', ' . $store['suburb'] . ', ' . $store['postcode'] . ', ' . $store['region_code'];
            $arr = [
                'i' => $store['store_locator_id'],
                'n' => $store['name'],
                'a' => $address,
                'l' => $store['latitude'],
                'g' => $store['longitude']
            ];
            $data[] = $arr;
        }

        return $data;
    }

    /**
     * Function getStoreLocatorMediaUrl.
     *
     * @return string
     */
    protected function getStoreLocatorMediaUrl()
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        return $mediaUrl.constant(get_class($this->storeLocatorHelper).'::LOCATION_IMAGE_PATH').'/';
    }

    /**
     * get store location data as a json string
     * just small dataset
     *
     * @return string
     */
    public function getStoreLocationsFew()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $isCacheable = $this->_cacheState->isEnabled(self::CACHE_GROUP);
        $cacheKey = 'few-data';
        $conditions = [];

        $cacheKey = self::CACHE_GROUP . '-' . $storeId . ($cacheKey ? '-' . base64_encode($cacheKey) : '');
        if ($isCacheable) {
            $cacheData = $this->_cache->load($cacheKey);
            if ($cacheData) {
                return $cacheData;
            } else {
                $data = $this->jsonHelper->jsonEncode($this->getStoreDataFew($storeId, $conditions));
                $this->_cache->save($data, $cacheKey, [self::CACHE_GROUP]);

                return $data;
            }
        } else {
            $data = $this->jsonHelper->jsonEncode($this->getStoreDataFew($storeId, $conditions));

            return $data;
        }
    }
}
