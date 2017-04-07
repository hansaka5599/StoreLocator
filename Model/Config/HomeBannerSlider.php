<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Model\Config;

use Magento\Store\Model\StoreManagerInterface;
use Netstarter\Bannerslider\Model\ResourceModel\Slider\CollectionFactory;

/**
 * Class HomeBannerSlider.
 */
class HomeBannerSlider
{
    /**
     * Variable sliderCollectionFactory.
     *
     * @var CollectionFactory
     */
    protected $sliderCollectionFactory;

    /**
     * Variable storeManager.
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * HomeBannerSlider constructor.
     *
     * @param CollectionFactory $sliderCollectionFactory
     * @param StoreManagerInterface                            $storeManager
     */
    public function __construct(
        CollectionFactory $sliderCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->sliderCollectionFactory = $sliderCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Function toOptionArray.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];
        $ret[] = ['value' => '', 'label' => 'Please Select'];
        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value,
            ];
        }

        return $ret;
    }

    /**
     * Function toArray.
     *
     * @return array
     */
    public function toArray()
    {
        $sliders = $this->getBannerSliders();

        $sliderList = [];
        foreach ($sliders as $slider) {
            $sliderList[$slider->getSliderId()] = __($slider->getTitle());
        }

        return $sliderList;
    }

    /**
     * Function getBannerSliders.
     *
     * @return \Netstarter\Bannerslider\Model\ResourceModel\Slider\Collection
     */
    public function getBannerSliders()
    {
        $collection = $this->sliderCollectionFactory->create();
        $collection->addFieldToSelect('slider_id');
        $collection->addFieldToSelect('title');

        return $collection;
    }
}
