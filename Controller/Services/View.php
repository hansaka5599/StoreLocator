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

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Registry;
use Netstarter\StoreLocator\Model\ResourceModel\Category\Node\CollectionFactory;

/**
 * Class View.
 */
class View extends Action
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
     * Core registry.
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * View constructor.
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param CollectionFactory $collectionFactory
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory,
        CollectionFactory $collectionFactory,
        Registry $coreRegistry
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->serviceCollectionFactory = $collectionFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Function execute
     * {@inheritdoc}
     *
     * @return $this
     */
    public function execute()
    {
        $storeLocatorId = $this->getRequest()->getParam('store_locator_id');
        $nodeId = $this->getRequest()->getParam('node_id');

        if (!$storeLocatorId) {
            $resultForward = $this->resultForwardFactory->create();

            return $resultForward->forward('noroute');
        }

        $categoryCollection = $this->serviceCollectionFactory->create();
        $categoryCollection->addFieldToSelect('*');
        $categoryCollection->addFieldToFilter('main_table.node_id', $nodeId);
        $categoryCollection->setPageSize(1);

        $storeCategoryNodeTable = 'ns_store_locator_store_category_node';

        $categoryCollection->getSelect()->joinLeft(
            ['scn' => $storeCategoryNodeTable],
            'scn.node_id = main_table.node_id and scn.store_locator_id = '.$storeLocatorId,
            [
                'scn.content_heading',
                'scn.page_title',
                'scn.dedicated_page',
                'scn.content',
                'scn.meta_keyword',
                'scn.meta_data',
                'scn.redirect_url',
            ]
        );

        $storeLocatorTable = 'ns_store_locator';
        $categoryCollection->getSelect()->joinLeft(
            ['stl' => $storeLocatorTable],
            'stl.store_locator_id = scn.store_locator_id',
            [
                'stl.store_locator_id',
                'store_heading_name' => 'stl.heading_name',
                'stl.url_key',
                'stl.phone',
                'stl.street',
                'stl.street2',
                'stl.suburb',
                'stl.postcode',
            ]
        );

        if ($categoryCollection) {
            $this->coreRegistry->register('current_store_service', $categoryCollection->getFirstItem()->getData());
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
