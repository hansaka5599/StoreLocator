<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Helper;

use CameraHouse\StoreLocator\Model\Users;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Netstarter\StoreLocator\Model\ResourceModel\Store\CollectionFactory;

/**
 * Class Data.
 */
class Data extends AbstractHelper
{
    /**
     * Admin session.
     * @var Session
     */
    protected $adminSession;

    /**
     * CameraHouse User Model.
     * @var Users
     */
    protected $cameraHouseUsersModel;

    /**
     * XML path for super admin role.
     */
    const CONFIG_XML_PATH_SUPER_ADMIN_ROLE = 'netstarter_storelocator/acl/acl_role';

    /**
     * XML path for super admin role.
     */
    const CONFIG_XML_PATH_SERVICE_ID = 'netstarter_storelocator/settings/camerahouse_service';

    /**
     * XML path for home page banner slider id.
     */
    const CONFIG_XML_PATH_HOME_BANNER_SLIDER_ID = 'netstarter_storelocator/location/home_slider_id';

    /**
     * XML path for store detail page course product limit.
     */
    const CONFIG_XML_PATH_STORE_COURSE_PRODUCT_LIMIT = 'netstarter_storelocator/location/store_page_product_limit';

    /**
     * XML path for store detail page course attribute set id.
     */
    const CONFIG_XML_PATH_STORE_COURSE_ATTRIBUTE_SET_ID = 'netstarter_storelocator/location/course_attribute_set_id';

    /**
     * XML path for store detail page service limit.
     */
    const CONFIG_XML_PATH_STORE_PAGE_SERVICE_LIMIT = 'netstarter_storelocator/settings/store_page_service_limit';

    /**
     * XML path for super admin role.
     */
    const CONFIG_XML_PATH_PRINT_SHOP_ID = 'netstarter_storelocator/print_shop/category_id';

    /**
     * Variable scopeConfig.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Store collection.
     *
     * @var CollectionFactory
     */
    protected $storeCollection;

    /**
     * Variable storeManager.
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Data constructor.
     *
     * @param Context                                $context
     * @param StoreManagerInterface                           $storeManager
     * @param Session                                  $adminSession
     * @param Users                                $cameraHouseUsersModel
     * @param CollectionFactory $storeCollection
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Session $adminSession,
        Users $cameraHouseUsersModel,
        CollectionFactory $storeCollection
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->adminSession = $adminSession;
        $this->cameraHouseUsersModel = $cameraHouseUsersModel;
        $this->storeCollection = $storeCollection;
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * Get Assigned stores as an array for a given user.
     *
     * @param null $userId
     *
     * @return array
     */
    public function getMyAssignedStores($userId = null)
    {
        $storeList = [];
        if ($this->adminSession->getUser()) {
            if ($userId == null) {
                $currentUserId = $this->adminSession->getUser()->getUserId();
            } else {
                $currentUserId = $userId;
            }

            $collection = $this->cameraHouseUsersModel->getCollection()
                ->addFieldToFilter('user_id', $currentUserId);
            $allowedStores = $collection->getData();

            if (!empty($allowedStores)) {
                foreach ($allowedStores as $store) {
                    $storeList[] = $store['store_locator_id'];
                }
            }
        }

        return $storeList;
    }

    /**
     * Get store list.
     *
     * @param bool $selection
     *
     * @return array
     */
    public function getMyAssignedStoreOptions($selection = true)
    {
        $storeCollection = $this->storeCollection->create();
        $storeCollection->addFieldToFilter('is_active', 1);
        $storeCollection->setOrder('name', 'asc');

        //Add restriction to store list based on the assigned stores to a particular store manager
        $isGenericAdmin = $this->isGenericAdminUser();
        if ($isGenericAdmin === false) {
            $allowedStores = $this->getMyAssignedStores();
            if (!empty($allowedStores)) {
                $storeCollection->addFieldToFilter('store_locator_id', ['in', $allowedStores]);
            }
        }

        $stores = $storeCollection->load();

        if ($selection === true) {
            $options [] = ['label' => '- Not Assigned -', 'value' => 0];
        } elseif ($selection === false) {
            $options = [];
        }

        foreach ($stores as $store) {
            $options[] = ['label' => $store->getName(), 'value' => $store->getStoreLocatorId()];
        }

        return $options;
    }

    /**
     * Get all store options as options array.
     *
     * @return array
     */
    public function getAllStoreOptionsArray()
    {
        $storeCollection = $this->storeCollection->create();
        $storeCollection->setOrder('name', 'asc');
        $optionsArray = $storeCollection->getData();
        $options[] = ['label' => '', 'value' => ''];
        if (!empty($optionsArray)) {
            foreach ($optionsArray as $user) {
                if ($user['is_active'] == '1') {
                    $options[] = ['value' => $user['store_locator_id'], 'label' => $user['name']];
                }
            }
        }

        return $options;
    }

    /**
     * Get store list.
     *
     * @param bool $getAll
     *
     * @return array
     */
    public function getMyAssignedStoreDropdownOptions($getAll = false)
    {
        $storeCollection = $this->storeCollection->create();
        $storeCollection->addFieldToFilter('is_active', 1);
        $storeCollection->addOrder('name', 'asc');

        //Add restriction to store list based on the assigned stores to a particular store manager
        if ($getAll === false) {
            $isGenericAdmin = $this->isGenericAdminUser();
            if ($isGenericAdmin === false) {
                $allowedStores = $this->getMyAssignedStores();
                if (!empty($allowedStores)) {
                    $storeCollection->addFieldToFilter('store_locator_id', ['in', $allowedStores]);
                }
            }
        }

        $stores = $storeCollection->load();

        $options = [];

        foreach ($stores as $store) {
            $options[$store->getStoreLocatorId()] = $store->getName();
        }

        return $options;
    }

    /**
     * Get super admin user role.
     *
     * @return string
     */
    public function getConfigSuperUserRoleId()
    {
        $aclConfig = $this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_SUPER_ADMIN_ROLE,
            ScopeInterface::SCOPE_STORE
        );

        return trim($aclConfig);
    }

    /**
     * Get service configuration category ID.
     *
     * @return string
     */
    public function getConfigServiceCatId()
    {
        $serviceId = $this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_SERVICE_ID,
            ScopeInterface::SCOPE_STORE
        );

        return trim($serviceId);
    }

    /**
     * Get course product attribute set id.
     *
     * @return string
     */
    public function getConfigCourseAttributeSetId()
    {
        $serviceId = $this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_STORE_COURSE_ATTRIBUTE_SET_ID,
            ScopeInterface::SCOPE_STORE
        );

        return trim($serviceId);
    }

    /**
     * Get config value home page main banner slider.
     *
     * @return string
     */
    public function getConfigHomePageBannerSlider()
    {
        $sliderId = $this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_HOME_BANNER_SLIDER_ID,
            ScopeInterface::SCOPE_STORE
        );

        return trim($sliderId);
    }

    /**
     * Get config value store page course product limit.
     *
     * @return string
     */
    public function getConfigStoreCourseProductLimit()
    {
        $limit = $this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_STORE_COURSE_PRODUCT_LIMIT,
            ScopeInterface::SCOPE_STORE
        );

        return trim($limit);
    }

    /**
     * Get store detail page service limit in left side.
     *
     * @return string
     */
    public function getConfigStorePageServiceLimit()
    {
        $limit = $this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_STORE_PAGE_SERVICE_LIMIT,
            ScopeInterface::SCOPE_STORE
        );

        return trim($limit);
    }

    /**
     * Get config value for a given path.
     *
     * @param $config_path
     *
     * @return mixed
     */
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Function getConfigPrintShopCatId.
     *
     * @return string
     */
    public function getConfigPrintShopCatId()
    {
        $catId = $this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_PRINT_SHOP_ID,
            ScopeInterface::SCOPE_STORE
        );

        return trim($catId);
    }

    /**
     * Get Currently logged user ID.
     *
     * @return mixed
     */
    public function getCurrentUserId()
    {
        if ($this->adminSession->getUser()) {
            return $this->adminSession->getUser()->getUserId();
        } else {
            return null;
        }
    }

    /**
     * Get currently logged user role.
     *
     * @return mixed
     */
    public function getCurrentUserRoleId()
    {
        if ($this->adminSession->getUser()) {
            return $this->adminSession->getUser()->getRole()->getRoleId();
        } else {
            return null;
        }
    }

    /**
     * Return whether the logged user is an generic user role.
     *
     * @return bool
     */
    public function isGenericAdminUser()
    {
        $currentRoleId = $this->getCurrentUserRoleId();
        $superRoleId = $this->getConfigSuperUserRoleId();
        if ($currentRoleId == $superRoleId) {
            return true;
        } else {
            return false;
        }
    }
}
