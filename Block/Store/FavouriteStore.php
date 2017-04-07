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

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Netstarter\StoreLocator\Helper\Data;
use Netstarter\StoreLocator\Model\StoreFactory;

class FavouriteStore extends Template
{
    /**
     * @var StoreFactory
     */
    protected $storeFactory;

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
     * @var Session
     */
    protected $customerSession;

    /**
     * @var bool
     */
    protected $hasData = false;

    /**
     * @var \Netstarter\StoreLocator\Model\Store
     */
    protected $locationModel;

    /**
     * FavouriteStore constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param StoreFactory $storeFactory
     * @param Session $customerSession
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        StoreFactory $storeFactory,
        SessionFactory $customerSession,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->helper = $helper;
        $this->storeFactory = $storeFactory;
        $this->customerSession = $customerSession;
        $this->getLoadStore();
    }

    /**
     * get store details by customer.
     */
    private function getLoadStore()
    {
        $session = $this->customerSession->create();
        $customerId = $session->getCustomerId();
        if ($customerId) {
            $this->locationModel = $this->storeFactory->create();
            $this->hasData = $this->locationModel->loadStoreByCustomer($customerId);
        }
    }

    /**
     * Function getUserStore()
     * @return mixed|null
     */
    public function getUserFavoriteStore()
    {
        $session = $this->customerSession->create();
        $customerId = $session->getCustomerId();
        if ($customerId) {
            return [
                'i' => $this->locationModel->getStoreLocatorId(),
                'n' => $this->locationModel->getName(),
                'a' => $this->getAddress(),
                'u' => $this->locationModel->getUrlKey(),
                'l' => $this->locationModel->getLatitude(),
                'g' => $this->locationModel->getLongitude(),
                'sd' => $this->locationModel->getLSubDomain(),
                'p' => $this->locationModel->getPhone(),
                'f' => $this->locationModel->getFax(),
                'e' => $this->locationModel->getEmail(),
                'oh' => $this->getOpeningHours(),
                'fa' => [
                    'st' => $this->locationModel->getStreet(),
                    'st2' => $this->locationModel->getData('street2'),
                    'su' => $this->locationModel->getSuburb(),
                    'po' => $this->locationModel->getPostcode(),
                    'rc' => $this->locationModel->getRegionCode(),
                ],
            ];
        }
        else{
            return null;
        }
    }

    /**
     * get store address.
     *
     * @return string
     */
    public function getAddress()
    {
        return str_replace(',', ':br ', $this->locationModel->getStreet()).' '.str_replace(',', ':br',
            $this->locationModel->getData('street2')).' '.$this->locationModel->getSuburb()
        .' '.$this->locationModel->getRegionCode().' '.$this->locationModel->getPostcode();
    }

    /**
     * get opening hours.
     *
     * @return mixed
     */
    public function getOpeningHours()
    {
        return $this->locationModel->getUseDefaultOh() ?
            $this->locationModel->getOpeningHrs() : $this->locationModel->getStoreOpeningHours();
    }

    /**
     * get opening hours.
     *
     * @return mixed
     */
    public function getHolidays()
    {
        return $this->locationModel->getUseDefaultHo() ?
            $this->helper->getHolidays() : $this->locationModel->getStoreHolidayHours();
    }

    /**
     * Map js params.
     *
     * @return string
     */
    public function getMapParams()
    {
        $helper = $this->helper;
        $param = [
            'map' => [
                'multi_stores' => false,
                'lat' => (float) $this->locationModel->getLatitude(),
                'lng' => (float) $this->locationModel->getLongitude(),
                'pin_img' => $helper->getPinImg(),
                'styles' => $helper->getMapStyles(),
                'zoom' => 12,
                'address' => htmlspecialchars($this->getAddress(), ENT_QUOTES),
                'mom' => true,
            ],
            'dates' => [
                'oh' => htmlspecialchars($this->getOpeningHours(), ENT_QUOTES),
                'hh' => htmlspecialchars($this->getHolidays(), ENT_QUOTES),
            ],
        ];

        return \Zend_Json::encode($param);
    }

    /**
     * Get map key.
     *
     * @return string
     */
    public function getKey()
    {
        $key = $this->helper->getMapKey();

        return $key;
    }
}
