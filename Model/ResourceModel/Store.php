<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Model\ResourceModel;

use CameraHouse\StoreLocator\Helper\Data;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;
use Netstarter\StoreLocator\Model\ResourceModel\Filter\Factory;

/**
 * Class Store.
 */
class Store extends \Netstarter\StoreLocator\Model\ResourceModel\Store
{
    /**
     * Variable cameraHouseHelper.
     *
     * @var Data
     */
    protected $cameraHouseHelper;

    /**
     * Store constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Factory $filterFactory
     * @param Data $cameraHouseHelper
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Factory $filterFactory,
        Data $cameraHouseHelper,
        $connectionName = null
    ) {
        $this->cameraHouseHelper = $cameraHouseHelper;
        parent::__construct($context, $storeManager, $filterFactory, $connectionName);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * Function getStoreLocations.
     *
     * @param $storeId
     * @param null $conditions
     *
     * @return array
     */
    public function getStoreLocations($storeId, $conditions = null, $useAndOperator = false)
    {
        $connection = $this->getConnection();
        $storeIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int) $storeId];
        $select = $connection->select()->from(
            ['store_locator' => $this->getMainTable()],
            [
                'store_locator_id',
                'heading_name',
                'name',
                'url_key',
                'phone',
                'email',
                'street',
                'street2',
                'suburb',
                'postcode',
                'latitude',
                'longitude',
                'is_new',
                'use_default_oh',
                'store_opening_hours',
                'use_default_ho',
                'store_holiday_hours',
                'subdomain',
                'subdomain_priority',
                'photo_creation_url',
                'digital_print_url',
                'event_photography_url',
                'latest_offers_url',
                'spot_image_1',
                'alt_text_1',
                'spot_url_1',
                'spot_image_2',
                'alt_text_2',
                'spot_url_2',
                'spot_image_3',
                'alt_text_3',
                'spot_url_3',
            ]
        )->join(
            ['store_locator_store' => $this->getTable('ns_store_locator_store')],
            'store_locator.store_locator_id = store_locator_store.store_locator_id',
            []
        )->joinLeft(
            ['region' => $this->getTable('directory_country_region')],
            'store_locator.region_id = region.region_id',
            ['region_code' => 'code']
        )->where(
            'is_active = ?',
            1
        )->where(
            'store_locator_store.store_id IN (?)',
            $storeIds
        )->order('store_locator.store_locator_id')
        ->distinct(true);

        if ($conditions !== null) {
            if($useAndOperator){
                $select->join(
                    ['category_node' => $this->getTable('ns_store_locator_store_category_node')],
                    'store_locator.store_locator_id = category_node.store_locator_id',
                    []
                );
                $nodeids=[];

                foreach ($conditions as $class => $param) {
                    if(is_array($param)) {
                        foreach ($param as $id){
                            $nodeids[] = $id;
                            //$select->where('node_id =?', $id);
                        }
                    }
                }
                $nodeIdsTxt = implode(',', $nodeids);

                $select->where('FIND_IN_SET(category_node.node_id, ?)', $nodeIdsTxt);
                $select->group('store_locator.store_locator_id');
                $select->having('COUNT(*) >=(SELECT COUNT(DISTINCT category_node.node_id) FROM ns_store_locator_store_category_node category_node WHERE FIND_IN_SET(category_node.node_id, ?))', $nodeIdsTxt);
            } else {
                $filterObj = $this->filterFactory->create('Category');
                foreach ($conditions as $class => $param) {
                    $filterObj->prepareQuery($select, $class, $param);
                }
            }

        }

        return $connection->fetchAll($select);
    }



    /**
     * Function getStoreLocations.
     * just small dataset
     *
     * @param $storeId
     * @param null $conditions
     *
     * @return array
     */
    public function getStoreLocationsFew($storeId, $conditions = null)
    {
        $connection = $this->getConnection();
        $storeIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int) $storeId];
        $select = $connection->select()->from(
            ['store_locator' => $this->getMainTable()],
            [
                'store_locator_id',
                'name',
                'street',
                'street2',
                'suburb',
                'postcode',
                'latitude',
                'longitude'
            ]
        )->join(
            ['store_locator_store' => $this->getTable('ns_store_locator_store')],
            'store_locator.store_locator_id = store_locator_store.store_locator_id',
            []
        )->joinLeft(
            ['region' => $this->getTable('directory_country_region')],
            'store_locator.region_id = region.region_id',
            ['region_code' => 'code']
        )->where(
            'is_active = ?',
            1
        )->where(
            'store_locator_store.store_id IN (?)',
            $storeIds
        )->order('store_locator.store_locator_id');

        if ($conditions !== null) {
            $filterObj = $this->filterFactory->create('Category');
            foreach ($conditions as $class => $param) {
                $filterObj->prepareQuery($select, $class, $param);
            }
        }

        return $connection->fetchAll($select);
    }

    /**
     * Function _afterSave.
     *
     * @param AbstractModel $object
     *
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        $storeLocatorId = (int) $object->getId();
        $connection = $this->getConnection();

        // stores
        $oldStores = $this->lookupStoreIds($storeLocatorId);
        $newStores = (array) $object->getStoreId();
        $tableStore = $this->getTable('ns_store_locator_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = ['store_locator_id = ?' => $storeLocatorId, 'store_id IN (?)' => $delete];
            $connection->delete($tableStore, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = ['store_locator_id' => $storeLocatorId, 'store_id' => (int) $storeId];
            }

            $connection->insertMultiple($tableStore, $data);
        }

        return parent::_afterSave($object);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * Function saveCategories.
     *
     * @param $storeLocatorId
     * @param $categoryId
     * @param $dataNode
     *
     * @throws \Exception
     */
    public function saveCategories($storeLocatorId, $categoryId, $dataNode)
    {
        $table = $this->getTable('ns_store_locator_store_category_node');

        $where = ['store_locator_id = ?' => (int) $storeLocatorId, 'category_id = ?' => (int) $categoryId];
        $this->getConnection()->delete($table, $where);

        $data = [];
        foreach ($dataNode as $nodeId) {
            if ($nodeId['node_id']) {
                if (isset($nodeId['dedicated_page']) && $nodeId['dedicated_page'] == '0'
                    && isset($nodeId['redirect_url']) && $nodeId['redirect_url'] == ''
                ) {
                    throw new \Exception('Redirect Url cannot be empty');
                }

                if (isset($nodeId['dedicated_page']) && $nodeId['dedicated_page'] == '0'
                    && isset($nodeId['redirect_url']) && $nodeId['redirect_url'] != ''
                ) {
                    if ($this->isValidPostUrlKey($nodeId['redirect_url'])) {
                        throw new \Exception('Invalid redirect Url : '.$nodeId['redirect_url']);
                    }
                }

                $data[] = [
                    'store_locator_id' => $storeLocatorId,
                    'node_id' => $nodeId['node_id'],
                    'service_category' => $nodeId['name'],
                    'dedicated_page' => $nodeId['dedicated_page'] ? $nodeId['dedicated_page'] : 0,
                    'page_title' => $nodeId['page_title'] ? $nodeId['page_title'] : null,
                    'content_heading' => $nodeId['content_heading'] ? $nodeId['content_heading'] : null,
                    'content' => $nodeId['content'] ? $nodeId['content'] : null,
                    'meta_keyword' => $nodeId['meta_keyword'] ? $nodeId['meta_keyword'] : null,
                    'meta_data' => $nodeId['meta_data'] ? $nodeId['meta_data'] : null,
                    'category_id' => $categoryId,
                    'redirect_url' => isset($nodeId['redirect_url']) ? $nodeId['redirect_url'] : null,
                ];
            }
        }
        if ($data) {
            $this->getConnection()->insertMultiple($table, $data);
        }
    }

    /**
     * Function isValidPostUrlKey Check whether post url key is valid.
     *
     * @param $url
     *
     * @return int
     */
    protected function isValidPostUrlKey($url)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $url);
    }

    /**
     * Function getStoreEmail.
     *
     * @param $storeLocatorId
     * @param $storeId
     *
     * @return string
     */
    public function getStoreEmail($storeLocatorId, $storeId)
    {
        $connection = $this->getConnection();
        $storeIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int) $storeId];
        $select = $connection->select()->from(
            ['store_locator' => $this->getMainTable()],
            ['email']
        )->join(
            ['store_locator_store' => $this->getTable('ns_store_locator_store')],
            'store_locator.store_locator_id = store_locator_store.store_locator_id',
            []
        )->where(
            'store_locator.store_locator_id = ?',
            $storeLocatorId
        )->where(
            'is_active = ?',
            1
        )->where(
            'store_locator_store.store_id IN (?)',
            $storeIds
        )->order(
            'store_locator_store.store_id DESC'
        )->limit(
            1
        );

        return $connection->fetchOne($select);
    }

    /**
     * Function getStoreData.
     *
     * @param $id
     * @param $storeId
     *
     * @return array
     */
    public function getStoreData($id, $storeId)
    {
        $connection = $this->getConnection();
        $storeIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int) $storeId];
        $select = $connection->select()->from(
            ['store_locator' => $this->getMainTable()],
            [
                'url_key',
                'heading_name',
                'store_content',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'store_service_content',
            ]
        )->join(
            ['store_locator_store' => $this->getTable('ns_store_locator_store')],
            'store_locator.store_locator_id = store_locator_store.store_locator_id',
            []
        )->where(
            'store_locator.store_locator_id = ?',
            $id
        )->where(
            'is_active = ?',
            1
        )->where(
            'store_locator_store.store_id IN (?)',
            $storeIds
        )->order(
            'store_locator_store.store_id DESC'
        )->limit(
            1
        );

        return $connection->fetchRow($select);
    }

    /**
     * Function getStorePrintShopServices.
     *
     * @return array
     */
    public function getStorePrintShopServices()
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['scn' => 'ns_store_locator_store_category_node'],
            ['store_locator_id', 'service_category', 'dedicated_page', 'redirect_url']
        )->joinInner(
            ['cn' => $this->getTable('ns_store_locator_category_node')],
            'scn.node_id = cn.node_id',
            ['cn.identifier','cn.name']
        )
            ->where(
                'scn.category_id = ?', $this->cameraHouseHelper->getConfigPrintShopCatId()
            );

        return $connection->fetchAll($select);
    }
}
