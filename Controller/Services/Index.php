<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Controller\Services;

use CameraHouse\StoreLocator\Helper\Data;
use CameraHouse\StoreLocator\Model\ResourceModel\Store;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Netstarter\StoreLocator\Model\ResourceModel\Category\Node\CollectionFactory;

/**
 * Class Index.
 */
class Index extends Action
{
    /**
     * Variable resultForwardFactory.
     *
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Variable serviceCollectionFactory.
     *
     * @var CollectionFactory
     */
    protected $serviceCollectionFactory;

    /**
     * Variable storeResource.
     *
     * @var Store
     */
    protected $storeResource;

    /**
     * Variable storeManager.
     *
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Core registry.
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * Variable helperData.
     *
     * @var Data
     */
    private $helperData;

    /**
     * Index constructor.
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Store $storeResource
     * @param StoreManagerInterface $storeManager
     * @param ForwardFactory $resultForwardFactory
     * @param Data $helperData
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Store $storeResource,
        StoreManagerInterface $storeManager,
        ForwardFactory $resultForwardFactory,
        Data $helperData,
        Registry $coreRegistry
    ) {
        $this->serviceCollectionFactory = $collectionFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->storeResource = $storeResource;
        $this->storeManager = $storeManager;
        $this->coreRegistry = $coreRegistry;
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    /**
     * Function execute
     * {@inheritdoc}
     */
    public function execute()
    {
        $storeLocatorId = $this->getRequest()->getParam('store_locator_id');
        if (!$storeLocatorId) {
            $resultForward = $this->resultForwardFactory->create();

            return $resultForward->forward('noroute');
        }

        $categoryCollection = $this->serviceCollectionFactory->create();
        $categoryCollection->addFieldToSelect('name');
        $categoryCollection->addFieldToSelect('identifier');
        $categoryCollection->addFieldToSelect('category_icon_tile');
        $categoryCollection->addFieldToFilter('main_table.category_id', $this->helperData->getConfigServiceCatId());

        $storeCategoryNodeTable = 'ns_store_locator_store_category_node';
        $categoryCollection->getSelect()->joinInner(
            ['scn' => $storeCategoryNodeTable],
            'scn.node_id = main_table.node_id and scn.store_locator_id = '.$storeLocatorId,
            ['dedicated_page']
        );

        $categoryCollection->addOrder('sort_order', 'asc');

        $storeLocatorData = $this->storeResource->getStoreData(
            $storeLocatorId, $this->storeManager->getStore()->getId()
        );

        if ($categoryCollection) {
            $this->coreRegistry->register(
                'current_store_services',
                ['store_data' => $storeLocatorData, 'services' => $categoryCollection]
            );
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
